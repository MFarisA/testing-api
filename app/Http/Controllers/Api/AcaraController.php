<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Acara;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;
use App\Models\Translation;
use App\Models\AcaraTranslation;

class AcaraController extends Controller
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

        $query = Acara::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_acara', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('nama_acara', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id_acara', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id_acara', 'desc');
        } else {
            $query->orderBy('id_acara', 'desc');
        }

        $acara = $query->paginate($perPage);

        return response()->json([
            'current_page' => $acara->currentPage(),
            'per_page' => $acara->perPage(),
            'total' => $acara->total(),
            'last_page' => $acara->lastPage(),
            'next_page_url' => $acara->nextPageUrl(),
            'prev_page_url' => $acara->previousPageUrl(),
            'data' => $acara->items(),
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

            $query = AcaraTranslation::with('translation');

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

    public function getOnlyTranslationData($id_acara)
    {
        try {
            $acara = Acara::with('translations.translation')->find($id_acara);

            if (!$acara) {
                return response()->json(['message' => 'Acara not found'], Response::HTTP_NOT_FOUND);
            }

            $translations = $acara->translations;

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
                'nama_acara' => 'required|string|max:250',
                'thumbnail_acara' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'description' => 'nullable|string|max:1000',
                'path' => 'nullable|string|max:255',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'nama_acara_' . $lang->code => 'nullable|string|max:250',
                    'description_' . $lang->code => 'nullable|string|max:1000',
                ]);
            }

            $data = $request->except(['thumbnail_acara']);

            if ($request->hasFile('thumbnail_acara')) {
                $file = $request->file('thumbnail_acara');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'acara/thumbnails';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['thumbnail_acara'] = $filePath;
            }

            $acara = Acara::create($data);

            foreach ($allLangs as $lang) {
                $namaAcaraKey = 'nama_acara_' . $lang->code;
                $descriptionKey = 'description_' . $lang->code;

                AcaraTranslation::create([
                    'acara_id' => $acara->id_acara,
                    'translation_id' => $lang->id,
                    'nama_acara' => $request->input($namaAcaraKey, $data['nama_acara']),
                    'description' => $request->input($descriptionKey, $data['description']),
                    'thumbnail_acara' => $data['thumbnail_acara'] ?? null,
                ]);
            }

            return response()->json($acara->load('translations.translation'), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id_acara)
    {
        $acara = Acara::with('translations.translation')->find($id_acara);

        if (!$acara) {
            return response()->json(['message' => 'Acara not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($acara, Response::HTTP_OK);
    }

    public function update(Request $request, $id_acara)
    {
        try {
            $acara = Acara::find($id_acara);

            if (!$acara) {
                return response()->json(['message' => 'Acara not found'], Response::HTTP_NOT_FOUND);
            }

            $request->validate([
                'nama_acara' => 'required|string|max:250',
                'thumbnail_acara' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'description' => 'nullable|string|max:1000',
                'path' => 'nullable|string|max:255',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'nama_acara_' . $lang->code => 'nullable|string|max:250',
                    'description_' . $lang->code => 'nullable|string|max:1000',
                ]);
            }

            $data = $request->except(['thumbnail_acara']);

            if ($request->hasFile('thumbnail_acara')) {
                $file = $request->file('thumbnail_acara');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'acara/thumbnails';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                if ($acara->thumbnail_acara && Storage::disk('tvku_storage')->exists($acara->thumbnail_acara)) {
                    Storage::disk('tvku_storage')->delete($acara->thumbnail_acara);
                }

                $data['thumbnail_acara'] = $filePath;
            }

            $acara->update($data);

            foreach ($allLangs as $lang) {
                $namaAcaraKey = 'nama_acara_' . $lang->code;
                $descriptionKey = 'description_' . $lang->code; 
                $translation = AcaraTranslation::where('acara_id', $acara->id_acara)
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $translation->update([
                        'nama_acara' => $request->input($namaAcaraKey, $acara->nama_acara),
                        'description' => $request->input($descriptionKey, $acara->description),
                        'thumbnail_acara' => $data['thumbnail_acara'] ?? $translation->thumbnail_acara,
                    ]);
                } else {
                    AcaraTranslation::create([
                        'acara_id' => $acara->id_acara,
                        'translation_id' => $lang->id,
                        'nama_acara' => $request->input($namaAcaraKey, $acara->nama_acara),
                        'description' => $request->input($descriptionKey, $acara->description),
                        'thumbnail_acara' => $data['thumbnail_acara'] ?? null,
                    ]);
                }
            }

            return response()->json($acara->load('translations.translation'), Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id_acara)
    {
        try {
            $acara = Acara::with('translations')->findOrFail($id_acara);

            if ($acara->thumbnail_acara && Storage::disk('tvku_storage')->exists($acara->thumbnail_acara)) {
                Storage::disk('tvku_storage')->delete($acara->thumbnail_acara);
            }

            foreach ($acara->translations as $translation) {
                if ($translation->thumbnail_acara && Storage::disk('tvku_storage')->exists($translation->thumbnail_acara)) {
                    Storage::disk('tvku_storage')->delete($translation->thumbnail_acara);
                }
                $translation->delete();
            }

            $acara->delete();
            return response()->json(['message' => 'Acara deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyTranslation($id)
    {
        $translation = AcaraTranslation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        $translation->delete();

        return response()->json(['message' => 'Translation deleted successfully'], 200);
    }
}
