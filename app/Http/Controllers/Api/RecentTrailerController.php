<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecentTrailer;
use App\Models\RecentTrailerTranslation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class RecentTrailerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = RecentTrailer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                    ->orWhere('youtube_id', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('judul', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $recentTrailers = $query->paginate($perPage);

        return response()->json([
            'current_page' => $recentTrailers->currentPage(),
            'per_page' => $recentTrailers->perPage(),
            'total' => $recentTrailers->total(),
            'last_page' => $recentTrailers->lastPage(),
            'next_page_url' => $recentTrailers->nextPageUrl(),
            'prev_page_url' => $recentTrailers->previousPageUrl(),
            'data' => $recentTrailers->items(),
        ], Response::HTTP_OK);
    }

    public function indexTranslations(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $currentPage = $request->input('current_page', 1);
            $search = $request->input('search', null);
            $sort = $request->input('sort', 'id_desc');
            $languageCode = $request->input('language_code', null);

            $query = RecentTrailerTranslation::with('translation');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('youtube_id', 'like', "%{$search}%");
                });
            }

            if ($languageCode) {
                $query->whereHas('translation', function ($q) use ($languageCode) {
                    $q->where('code', $languageCode);
                });
            }

            if ($sort === 'id_asc') {
                $query->orderBy('id', 'asc');
            } elseif ($sort === 'id_desc') {
                $query->orderBy('id', 'desc');
            }

            $translations = $query->paginate($perPage, ['*'], 'page', $currentPage);

            return response()->json([
                'current_page' => $translations->currentPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
                'last_page' => $translations->lastPage(),
                'next_page_url' => $translations->nextPageUrl(),
                'prev_page_url' => $translations->previousPageUrl(),
                'data' => $translations->items(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getOnlyTranslationData($id_trailer)
    {
        try {
            $trailer = RecentTrailer::with('translations.translation')->find($id_trailer);

            if (!$trailer) {
                return response()->json(['message' => 'Trailer not found'], Response::HTTP_NOT_FOUND);
            }

            $translations = $trailer->translations;

            return response()->json([
                'message' => 'Translations retrieved successfully',
                'data' => $translations,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve translations',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'date' => 'required|date',
            'youtube_id' => 'required|string|max:255',
        ]);

        $allLang = Translation::all();
        foreach ($allLang as $lang) {
            $request->validate([
                'judul' . $lang->code => 'nullable|string|max:255'
            ]);
        }

        $recentTrailer = RecentTrailer::create($request->all());

        foreach ($allLang as $lang) {
            $judulKey = 'judul' . $lang->code;

            RecentTrailerTranslation::create([
                'recenttrailer_id' => $recentTrailer->id,
                'translation_id' => $lang->id,
                'judul' => $request->input($judulKey),
                'youtube_id' => $recentTrailer->youtube_id,
                'date' => $recentTrailer->date,
            ]);
        }

        return response()->json($recentTrailer->load('translations.translation'), Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $recentTrailer = RecentTrailer::find($id);
        if ($recentTrailer) {
            return response()->json($recentTrailer, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, string $id)
    {
        $recentTrailer = RecentTrailer::find($id);
        if (!$recentTrailer) {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'date' => 'required|date',
            'youtube_id' => 'required|string|max:255',
        ]);

        $allLang = Translation::all();
        foreach ($allLang as $lang) {
            $request->validate([
                'judul' . $lang->code => 'nullable|string|max:255'
            ]);
        }

        $fields = ['judul', 'date', 'youtube_id'];
        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $recentTrailer->{$field} = $validated[$field];
            }
        }

        try {
            $recentTrailer->update();

            foreach ($allLang as $lang) {
                $judulKey = 'judul' . $lang->code;

                $translation = RecentTrailerTranslation::where('recenttrailer_id', $recentTrailer->id)
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $translation->update([
                        'judul' => $request->input($judulKey),
                    ]);
                } else {
                    RecentTrailerTranslation::create([
                        'recenttrailer_id' => $recentTrailer->id,
                        'translation_id' => $lang->id,
                        'judul' => $request->input($judulKey),
                        'youtube_id' => $recentTrailer->youtube_id,
                        'date' => $recentTrailer->date,
                    ]);
                }
            }

            return response()->json($recentTrailer->load('translations.translation'), Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function destroy(string $id)
    {
        $recentTrailer = RecentTrailer::find($id);
        if ($recentTrailer) {
            $recentTrailer->delete();
            return response()->json(['message' => 'Recent Trailer successfully deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function destroyTranslation($id)
    {
        $translation = RecentTrailerTranslation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        $translation->delete();

        return response()->json(['message' => 'Translation deleted successfully'], 200);
    }
}
