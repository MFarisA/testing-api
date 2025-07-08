<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Iklan;
use App\Models\Translation;
use App\Models\IklanTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class IklanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = Iklan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                  ->orWhere('isi', 'like', '%' . $search . '%');
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

        $iklan = $query->paginate($perPage);

        return response()->json([
            'current_page' => $iklan->currentPage(),
            'per_page' => $iklan->perPage(),
            'total' => $iklan->total(),
            'last_page' => $iklan->lastPage(),
            'next_page_url' => $iklan->nextPageUrl(),
            'prev_page_url' => $iklan->previousPageUrl(),
            'data' => $iklan->items(),
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

            $query = IklanTranslation::with('translation');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%");
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

    public function getOnlyTranslationData($id_iklan)
    {
        try {
            $iklan = Iklan::with('translations.translation')->find($id_iklan);

            if (!$iklan) {
                return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
            }

            $translations = $iklan->translations;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'isi' => 'nullable|string|max:1000',
                'video' => 'nullable|string|max:255',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'isi_' . $lang->code => 'nullable|string|max:1000',
                ]);
            }

            $data = $request->except(['foto']);
            $data['user_id'] = Auth::id();

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'iklan';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['foto'] = $filePath;
            }

            $iklan = Iklan::create($data);

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $isiKey = 'isi_' . $lang->code;

                IklanTranslation::create([
                    'marketing_id' => $iklan->id,
                    'translation_id' => $lang->id,
                    'judul' => $request->input($judulKey, $data['judul']),
                    'isi' => $request->input($isiKey, $data['isi']),
                    'foto' => $data['foto'] ?? null,
                ]);
            }

            return response()->json($iklan->load('translations.translation'), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $iklan = Iklan::with('translations.translation')->find($id);

        if (!$iklan) {
            return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($iklan, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $iklan = Iklan::find($id);

            if (!$iklan) {
                return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
            }

            $request->validate([
                'judul' => 'required|string|max:255',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'isi' => 'nullable|string|max:1000',
                'video' => 'nullable|string|max:255',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'isi_' . $lang->code => 'nullable|string|max:1000',
                ]);
            }

            $data = $request->except(['foto']);
            $data['user_id'] = Auth::id();

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'iklan';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;

                if ($iklan->foto && Storage::disk('tvku_storage')->exists($iklan->foto)) {
                    Storage::disk('tvku_storage')->delete($iklan->foto);
                }

                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['foto'] = $filePath;
            }

            $iklan->update($data);

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $isiKey = 'isi_' . $lang->code;

                $translation = IklanTranslation::where('marketing_id', $iklan->id)
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $translation->update([
                        'judul' => $request->input($judulKey, $iklan->judul),
                        'isi' => $request->input($isiKey, $iklan->isi),
                        'foto' => $data['foto'] ?? $translation->foto,
                    ]);
                } else {
                    IklanTranslation::create([
                        'marketing_id' => $iklan->id,
                        'translation_id' => $lang->id,
                        'judul' => $request->input($judulKey, $iklan->judul),
                        'isi' => $request->input($isiKey, $iklan->isi),
                        'foto' => $data['foto'] ?? null,
                    ]);
                }
            }

            return response()->json($iklan->load('translations.translation'), Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $iklan = Iklan::with('translations')->findOrFail($id);

            if ($iklan->foto && Storage::disk('tvku_storage')->exists($iklan->foto)) {
                Storage::disk('tvku_storage')->delete($iklan->foto);
            }

            foreach ($iklan->translations as $translation) {
                if ($translation->foto && Storage::disk('tvku_storage')->exists($translation->foto)) {
                    Storage::disk('tvku_storage')->delete($translation->foto);
                }
                $translation->delete();
            }

            $iklan->delete();
            return response()->json(['message' => 'Iklan deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyTranslation($id)
    {
        $translation = IklanTranslation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        $translation->delete();

        return response()->json(['message' => 'Translation deleted successfully'], 200);
    }
}