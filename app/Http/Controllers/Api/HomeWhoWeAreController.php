<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeWhoWeAre;
use App\Models\HomeWhoWeAreTranslation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeWhoWeAreController extends Controller
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

        $query = HomeWhoWeAre::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');
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

        $whoWeAre = $query->paginate($perPage);

        return response()->json([
            'current_page' => $whoWeAre->currentPage(),
            'per_page' => $whoWeAre->perPage(),
            'total' => $whoWeAre->total(),
            'last_page' => $whoWeAre->lastPage(),
            'next_page_url' => $whoWeAre->nextPageUrl(),
            'prev_page_url' => $whoWeAre->previousPageUrl(),
            'data' => $whoWeAre->items(),
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

            $query = HomeWhoWeAreTranslation::with('translation');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('motto1', 'like', "%{$search}%")
                    ->orWhere('motto2', 'like', "%{$search}%")
                    ->orWhere('motto3', 'like', "%{$search}%")
                    ->orWhere('motto1sub', 'like', "%{$search}%")
                    ->orWhere('motto2sub', 'like', "%{$search}%")
                    ->orWhere('motto3sub', 'like', "%{$search}%");
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
    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string|max:1000',
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'motto1' => 'nullable|string|max:100',
                'motto2' => 'nullable|string|max:100',
                'motto3' => 'nullable|string|max:100',
                'motto1sub' => 'nullable|string|max:255',
                'motto2sub' => 'nullable|string|max:255',
                'motto3sub' => 'nullable|string|max:255',
            ]);
    
            $allLangs = Translation::all();
    
            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'deskripsi_' . $lang->code => 'nullable|string|max:1000',
                    'motto1_' . $lang->code => 'nullable|string|max:100',
                    'motto2_' . $lang->code => 'nullable|string|max:100',
                    'motto3_' . $lang->code => 'nullable|string|max:100',
                    'motto1sub_' . $lang->code => 'nullable|string|max:255',
                    'motto2sub_' . $lang->code => 'nullable|string|max:255',
                    'motto3sub_' . $lang->code => 'nullable|string|max:255',
                ]);
            }
    
            $data = $request->except(['gambar']);
    
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'home/whoweare';
    
                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }
    
                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['gambar'] = $filePath;
            }
    
            $homeWhoWeAre = HomeWhoWeAre::create($data);
    
            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $deskripsiKey = 'deskripsi_' . $lang->code;
                $motto1Key = 'motto1_' . $lang->code;
                $motto2Key = 'motto2_' . $lang->code;
                $motto3Key = 'motto3_' . $lang->code;
                $motto1subKey = 'motto1sub_' . $lang->code;
                $motto2subKey = 'motto2sub_' . $lang->code;
                $motto3subKey = 'motto3sub_' . $lang->code;
    
                HomeWhoWeAreTranslation::create([
                    'whoweare_id' => $homeWhoWeAre->id,
                    'translation_id' => $lang->id,
                    'judul' => $request->input($judulKey, $data['judul']),
                    'deskripsi' => $request->input($deskripsiKey, $data['deskripsi']),
                    'motto1' => $request->input($motto1Key, $data['motto1']),
                    'motto2' => $request->input($motto2Key, $data['motto2']),
                    'motto3' => $request->input($motto3Key, $data['motto3']),
                    'motto1sub' => $request->input($motto1subKey, $data['motto1sub']),
                    'motto2sub' => $request->input($motto2subKey, $data['motto2sub']),
                    'motto3sub' => $request->input($motto3subKey, $data['motto3sub']),
                    'gambar' => $data['gambar'] ?? null,
                ]);
            }
    
            return response()->json($homeWhoWeAre->load('translations.translation'), Response::HTTP_CREATED);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $homeWhoWeAre = HomeWhoWeAre::with('translations.translation')->find($id);

        if (!$homeWhoWeAre) {
            return response()->json(['message' => 'Home Who We Are not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($homeWhoWeAre, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        try {
            $homeWhoWeAre = HomeWhoWeAre::find($id);

            if (!$homeWhoWeAre) {
                return response()->json(['message' => 'Home Who We Are not found'], Response::HTTP_NOT_FOUND);
            }

            $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string|max:1000',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'motto1' => 'nullable|string|max:100',
                'motto2' => 'nullable|string|max:100',
                'motto3' => 'nullable|string|max:100',
                'motto1sub' => 'nullable|string|max:255',
                'motto2sub' => 'nullable|string|max:255',
                'motto3sub' => 'nullable|string|max:255',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'deskripsi_' . $lang->code => 'nullable|string|max:1000',
                    'motto1_' . $lang->code => 'nullable|string|max:100',
                    'motto2_' . $lang->code => 'nullable|string|max:100',
                    'motto3_' . $lang->code => 'nullable|string|max:100',
                    'motto1sub_' . $lang->code => 'nullable|string|max:255',
                    'motto2sub_' . $lang->code => 'nullable|string|max:255',
                    'motto3sub_' . $lang->code => 'nullable|string|max:255',
                ]);
            }

            $data = $request->except(['gambar']);

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'home/whoweare';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                if ($homeWhoWeAre->gambar && Storage::disk('tvku_storage')->exists($homeWhoWeAre->gambar)) {
                    Storage::disk('tvku_storage')->delete($homeWhoWeAre->gambar);
                }

                $data['gambar'] = $filePath;
            }

            $homeWhoWeAre->update($data);

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $deskripsiKey = 'deskripsi_' . $lang->code;
                $motto1Key = 'motto1_' . $lang->code;
                $motto2Key = 'motto2_' . $lang->code;
                $motto3Key = 'motto3_' . $lang->code;
                $motto1subKey = 'motto1sub_' . $lang->code;
                $motto2subKey = 'motto2sub_' . $lang->code;
                $motto3subKey = 'motto3sub_' . $lang->code;

                $translation = HomeWhoWeAreTranslation::where('whoweare_id', $homeWhoWeAre->id)
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $translation->update([
                        'judul' => $request->input($judulKey, $homeWhoWeAre->judul),
                        'deskripsi' => $request->input($deskripsiKey, $homeWhoWeAre->deskripsi),
                        'motto1' => $request->input($motto1Key, $homeWhoWeAre->motto1),
                        'motto2' => $request->input($motto2Key, $homeWhoWeAre->motto2),
                        'motto3' => $request->input($motto3Key, $homeWhoWeAre->motto3),
                        'motto1sub' => $request->input($motto1subKey, $homeWhoWeAre->motto1sub),
                        'motto2sub' => $request->input($motto2subKey, $homeWhoWeAre->motto2sub),
                        'motto3sub' => $request->input($motto3subKey, $homeWhoWeAre->motto3sub),
                        'gambar' => $data['gambar'] ?? $translation->gambar,
                    ]);
                } else {
                    HomeWhoWeAreTranslation::create([
                        'whoweare_id' => $homeWhoWeAre->id,
                        'translation_id' => $lang->id,
                        'judul' => $request->input($judulKey, $homeWhoWeAre->judul),
                        'deskripsi' => $request->input($deskripsiKey, $homeWhoWeAre->deskripsi),
                        'motto1' => $request->input($motto1Key, $homeWhoWeAre->motto1),
                        'motto2' => $request->input($motto2Key, $homeWhoWeAre->motto2),
                        'motto3' => $request->input($motto3Key, $homeWhoWeAre->motto3),
                        'motto1sub' => $request->input($motto1subKey, $homeWhoWeAre->motto1sub),
                        'motto2sub' => $request->input($motto2subKey, $homeWhoWeAre->motto2sub),
                        'motto3sub' => $request->input($motto3subKey, $homeWhoWeAre->motto3sub),
                        'gambar' => $data['gambar'] ?? null,
                    ]);
                }
            }

            return response()->json($homeWhoWeAre->load('translations.translation'), Response::HTTP_OK);

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
            $homeWhoWeAre = HomeWhoWeAre::with('translations')->findOrFail($id);

            if ($homeWhoWeAre->gambar && Storage::disk('tvku_storage')->exists($homeWhoWeAre->gambar)) {
                Storage::disk('tvku_storage')->delete($homeWhoWeAre->gambar);
            }

            foreach ($homeWhoWeAre->translations as $translation) {
                if ($translation->gambar && Storage::disk('tvku_storage')->exists($translation->gambar)) {
                    Storage::disk('tvku_storage')->delete($translation->gambar);
                }
                $translation->delete();
            }

            $homeWhoWeAre->delete();

            return response()->json(['message' => 'Home Who We Are deleted successfully'], Response::HTTP_OK);
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
            $translation = HomeWhoWeAreTranslation::find($id);

            if (!$translation) {
                return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
            }

            if ($translation->gambar && Storage::disk('tvku_storage')->exists($translation->gambar)) {
                Storage::disk('tvku_storage')->delete($translation->gambar);
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
