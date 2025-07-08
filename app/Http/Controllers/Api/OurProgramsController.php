<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OurPrograms;
use App\Models\OurProgramsTranslation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OurProgramsController extends Controller
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

        $query = OurPrograms::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('title', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $programs = $query->paginate($perPage);

        return response()->json([
            'current_page' => $programs->currentPage(),
            'per_page' => $programs->perPage(),
            'total' => $programs->total(),
            'last_page' => $programs->lastPage(),
            'next_page_url' => $programs->nextPageUrl(),
            'prev_page_url' => $programs->previousPageUrl(),
            'data' => $programs->items(),
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

            $query = OurProgramsTranslation::with('translation');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
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

    public function getOnlyTranslationData($id)
    {
        try {
            $ourPrograms = OurPrograms::with('translations.translation')->find($id);

            if (!$ourPrograms) {
                return response()->json(['message' => 'OurPrograms not found'], Response::HTTP_NOT_FOUND);
            }

            $translations = $ourPrograms->translations;

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
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'thumbnail' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'deskripsi' => 'nullable|string|max:1000',
                'link' => 'nullable|url|max:255',
                'urutan' => 'nullable|integer|min:0',
            ]);

            $allLangs = Translation::all();
            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'deskripsi_' . $lang->code => 'nullable|string|max:1000',
                    'link_' . $lang->code  => 'nullable|url|max:255',
                ]);
            }

            $data = $request->except(['thumbnail']);

            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'ourprograms/thumbnails';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['thumbnail'] = $filePath;
            }

            $ourPrograms = OurPrograms::create($data);

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $deskripsiKey = 'deskripsi_' . $lang->code;
                $linkKey = 'link_' . $lang->code;

                OurProgramsTranslation::create([
                    'ourprogram_id' => $ourPrograms->id,
                    'translation_id' => $lang->id,
                    'judul' => $request->input($judulKey, $data['judul']),
                    'deskripsi' => $request->input($deskripsiKey, $data['deskripsi']),
                    'link' => $request->input($linkKey, $data['link']),
                    'urutan' => $data['urutan'] ?? null,
                    'thumbnail' => $data['thumbnail'] ?? null,
                ]);
            }

            return response()->json($ourPrograms->load('translations.translation'), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $ourPrograms = OurPrograms::with('translations.translation')->find($id);

        if (!$ourPrograms) {
            return response()->json(['message' => 'Our Programs not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($ourPrograms, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        try {
            $program = OurPrograms::find($id);

            if (!$program) {
                return response()->json(['message' => 'Our Programs not found'], Response::HTTP_NOT_FOUND);
            }

            $request->validate([
                'judul' => 'required|string|max:255',
                'thumbnail' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'deskripsi' => 'nullable|string|max:1000',
                'link' => 'nullable|url|max:255',
                'urutan' => 'nullable|integer|min:0',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'deskripsi_' . $lang->code => 'nullable|string|max:1000',
                    'link_' . $lang->code => 'nullable|url|max:255',
                ]);
            }

            $data = $request->except(['thumbnail']);

            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'ourprograms/thumbnails';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                if ($program->thumbnail && Storage::disk('tvku_storage')->exists($program->thumbnail)) {
                    Storage::disk('tvku_storage')->delete($program->thumbnail);
                }

                $data['thumbnail'] = $filePath;
            }

            $program->update($data);

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $deskripsiKey = 'deskripsi_' . $lang->code;
                $linkKey = 'link_' . $lang->code;

                $translation = OurProgramsTranslation::where('ourprogram_id', $program->id) 
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $translation->update([
                        'judul' => $request->input($judulKey, $program->judul),
                        'deskripsi' => $request->input($deskripsiKey, $program->deskripsi),
                        'link' => $request->input($linkKey, $program->link),
                        'urutan' => $program->urutan, 
                        'thumbnail' => $data['thumbnail'] ?? $translation->thumbnail,
                    ]);
                } else {
                    OurProgramsTranslation::create([
                        'ourprogram_id' => $program->id, 
                        'translation_id' => $lang->id,
                        'judul' => $request->input($judulKey, $program->judul),
                        'deskripsi' => $request->input($deskripsiKey, $program->deskripsi),
                        'link' => $request->input($linkKey, $program->link),
                        'urutan' => $program->urutan, 
                        'thumbnail' => $data['thumbnail'] ?? null,
                    ]);
                }
            }

            return response()->json($program->load('translations.translation'), Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $ourPrograms = OurPrograms::with('translations')->findOrFail($id);

            if ($ourPrograms->thumbnail && Storage::disk('tvku_storage')->exists($ourPrograms->thumbnail)) {
                Storage::disk('tvku_storage')->delete($ourPrograms->thumbnail);
            }

            foreach ($ourPrograms->translations as $translation) {
                if ($translation->thumbnail && Storage::disk('tvku_storage')->exists($translation->thumbnail)) {
                    Storage::disk('tvku_storage')->delete($translation->thumbnail);
                }
                $translation->delete();
            }

            $ourPrograms->delete();

            return response()->json(['message' => 'Our Programs deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyTranslation($id)
    {
        try {
            $translation = OurProgramsTranslation::find($id);

            if (!$translation) {
                return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
            }

            if ($translation->thumbnail && Storage::disk('tvku_storage')->exists($translation->thumbnail)) {
                Storage::disk('tvku_storage')->delete($translation->thumbnail);
            }

            $translation->delete();

            return response()->json(['message' => 'Translation deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
