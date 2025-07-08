<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Translation;
use App\Models\KategoriTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KategoriController extends Controller
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

        $query = Kategori::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('nama', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id_kategori', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id_kategori', 'desc');
        } else {
            $query->orderBy('id_kategori', 'desc');
        }

        $kategori = $query->paginate($perPage);

        return response()->json([
            'current_page' => $kategori->currentPage(),
            'per_page' => $kategori->perPage(),
            'total' => $kategori->total(),
            'last_page' => $kategori->lastPage(),
            'next_page_url' => $kategori->nextPageUrl(),
            'prev_page_url' => $kategori->previousPageUrl(),
            'data' => $kategori->items(),
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

            $query = KategoriTranslation::with('translation');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
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

    public function getOnlyTranslationData($id_kategori)
    {
        try {
            $kategori = Kategori::with('translations.translation')->find($id_kategori);

            if (!$kategori) {
                return response()->json(['message' => 'Kategori not found'], Response::HTTP_NOT_FOUND);
            }

            $translations = $kategori->translations;

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
                'nama' => 'required|string|max:255|unique:tb_kategori,nama',
                'slug' => 'required|string|max:20|unique:tb_kategori,slug',
                'top_nav' => 'nullable|in:0,1',
                'urutan' => 'nullable|integer',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'nama_' . $lang->code => 'nullable|string|max:255|unique:kategori_translations,nama',
                    'slug_' . $lang->code => 'nullable|string|max:20|unique:kategori_translations,slug',
                ]);
            }

            $data = $request->only(['nama', 'slug', 'top_nav', 'urutan']);
            $kategori = Kategori::create($data);

            $kategoriId = $kategori->id_kategori;

            foreach ($allLangs as $lang) {
                $namaKey = 'nama_' . $lang->code;
                $slugKey = 'slug_' . $lang->code;

                KategoriTranslation::create([
                    'id_kategori' => $kategoriId,
                    'translation_id' => $lang->id,
                    'nama' => $request->input($namaKey) ?? $data['nama'] ?? null,
                    'slug' => $request->input($slugKey) ?? $data['slug'] ?? null,
                ]);
            }

            return response()->json($kategori->load('translations.translation'), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id_kategori)
    {
        $kategori = Kategori::with('translations.translation')->find($id_kategori);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($kategori, Response::HTTP_OK);
    }

    public function update(Request $request, $id_kategori)
    {
        try {
            $kategori = Kategori::with('translations')->where('id_kategori', $id_kategori)->first();
            if (!$kategori) {
                return response()->json(['message' => 'Kategori not found'], Response::HTTP_NOT_FOUND);
            }

            $request->validate([
                'nama' => 'sometimes|required|string|max:255|unique:tb_kategori,nama,' . $id_kategori . ',id_kategori',
                'slug' => 'sometimes|required|string|max:20|unique:tb_kategori,slug,' . $id_kategori . ',id_kategori',
                'top_nav' => 'nullable|in:0,1',
                'urutan' => 'nullable|integer',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'nama_' . $lang->code => 'nullable|string|max:255|unique:kategori_translations,nama,' . $id_kategori . ',id_kategori',
                    'slug_' . $lang->code => 'nullable|string|max:20|unique:kategori_translations,slug,' . $id_kategori . ',id_kategori',
                ]);
            }

            $kategori->update($request->only(['nama', 'slug', 'top_nav', 'urutan']));

            foreach ($allLangs as $lang) {
                $namaKey = 'nama_' . $lang->code;
                $slugKey = 'slug_' . $lang->code;

                $translation = KategoriTranslation::where('id_kategori', $kategori->id_kategori)
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $translation->update([
                        'nama' => $request->input($namaKey, $translation->nama), 
                        'slug' => $request->input($slugKey, $translation->slug), 
                    ]);
                } else {
                    KategoriTranslation::create([
                        'id_kategori' => $kategori->id_kategori,
                        'translation_id' => $lang->id,
                        'nama' => $request->input($namaKey, $kategori->nama), 
                        'slug' => $request->input($slugKey, $kategori->slug), 
                    ]);
                }
            }

            return response()->json($kategori->load('translations.translation'), Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id_kategori)
    {
        try {
            $kategori = Kategori::with('translations')->where('id_kategori', $id_kategori)->firstOrFail();

            foreach ($kategori->translations as $translation) {
                $translation->delete();
            }
            $kategori->delete();

            return response()->json(['message' => 'Kategori deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Kategori not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Kategori',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyTranslation($id)
    {
        try {
            $translation = KategoriTranslation::findOrFail($id);
            $translation->delete();

            return response()->json(['message' => 'Translation deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Translation',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function getKategoriTranslationById(Request $request, $id_kategori)
    {
        try {
            $languageCode = $request->input('language_code');
            
            if (!$languageCode) {
                return response()->json([
                    'message' => 'Language code is required',
                    'error' => 'Please provide language_code parameter'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check if kategori exists first
            $kategori = Kategori::where('id_kategori', $id_kategori)->first();
            
            if (!$kategori) {
                return response()->json([
                    'message' => 'Kategori not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Get translations for specific language
            $translations = KategoriTranslation::with('translation')
                ->where('id_kategori', $id_kategori)
                ->whereHas('translation', function ($query) use ($languageCode) {
                    $query->where('code', $languageCode);
                })
                ->get();

            if ($translations->isEmpty()) {
                return response()->json([
                    'message' => 'No translation found for the specified language',
                    'language_code' => $languageCode
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Translation retrieved successfully',
                'language_code' => $languageCode,
                'data' => $translations,
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve translation',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getKategoriTranslationsByParams(Request $request)
    {
        try {
            $languageCode = $request->input('language_code');
            $idKategori = $request->input('id_kategori');
            
            if (!$languageCode) {
                return response()->json([
                    'message' => 'Language code is required',
                    'error' => 'Please provide language_code parameter'
                ], Response::HTTP_BAD_REQUEST);
            }

            if (!$idKategori) {
                return response()->json([
                    'message' => 'Category ID is required',
                    'error' => 'Please provide id_kategori parameter'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check if kategori exists first
            $kategori = Kategori::where('id_kategori', $idKategori)->first();
            
            if (!$kategori) {
                return response()->json([
                    'message' => 'Kategori not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Get translations for specific language
            $translations = KategoriTranslation::with('translation')
                ->where('id_kategori', $idKategori)
                ->whereHas('translation', function ($query) use ($languageCode) {
                    $query->where('code', $languageCode);
                })
                ->get();

            if ($translations->isEmpty()) {
                return response()->json([
                    'message' => 'No translation found for the specified language',
                    'language_code' => $languageCode,
                    'id_kategori' => $idKategori
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Translation retrieved successfully',
                'language_code' => $languageCode,
                'id_kategori' => $idKategori,
                'data' => $translations,
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve translation',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
