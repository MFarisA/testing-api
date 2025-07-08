<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeOurExpertise1;
use App\Models\HomeOurexpertise1Translation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeOurExpertise1Controller extends Controller
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

        $query = HomeOurExpertise1::with('translations.translation');

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

        $expertise = $query->paginate($perPage);

        return response()->json([
            'current_page' => $expertise->currentPage(),
            'per_page' => $expertise->perPage(),
            'total' => $expertise->total(),
            'last_page' => $expertise->lastPage(),
            'next_page_url' => $expertise->nextPageUrl(),
            'prev_page_url' => $expertise->previousPageUrl(),
            'data' => $expertise->items(),
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

            $query = HomeOurexpertise1Translation::with('translation');

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


    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'thumbnail' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'deskripsi' => 'nullable|string|max:1000',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'deskripsi_' . $lang->code => 'nullable|string|max:1000',
                ]);
            }

            $data = $request->except(['thumbnail']);

            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'home/ourexpertise1';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['thumbnail'] = $filePath;
            }

            $expertise = HomeOurExpertise1::create($data);

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $deskripsiKey = 'deskripsi_' . $lang->code;

                HomeOurExpertise1Translation::create([
                    'ourexpertise1_id' => $expertise->id,
                    'translation_id' => $lang->id,
                    'judul' => $request->input($judulKey, $data['judul']),
                    'deskripsi' => $request->input($deskripsiKey, $data['deskripsi']),
                    'thumbnail' => $data['thumbnail'] ?? null,
                ]);
            }

            return response()->json($expertise->load('translations.translation'), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show($id)
    {
        $expertise = HomeOurExpertise1::with('translations.translation')->find($id);

        if (!$expertise) {
            return response()->json(['message' => 'Expertise not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($expertise, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        try {
            $expertise = HomeOurExpertise1::find($id);

            if (!$expertise) {
                return response()->json(['message' => 'Expertise not found'], Response::HTTP_NOT_FOUND);
            }

            $request->validate([
                'judul' => 'required|string|max:255',
                'thumbnail' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'deskripsi' => 'nullable|string|max:1000',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'deskripsi_' . $lang->code => 'nullable|string|max:1000',
                ]);
            }

            $data = $request->except(['thumbnail']);

            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'home/ourexpertise1';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                if ($expertise->thumbnail && Storage::disk('tvku_storage')->exists($expertise->thumbnail)) {
                    Storage::disk('tvku_storage')->delete($expertise->thumbnail);
                }

                $data['thumbnail'] = $filePath;
            }

            $expertise->update($data);

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $deskripsiKey = 'deskripsi_' . $lang->code;

                $translation = HomeOurExpertise1Translation::where('ourexpertise1_id', $expertise->id)
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $translation->update([
                        'judul' => $request->input($judulKey, $expertise->judul),
                        'deskripsi' => $request->input($deskripsiKey, $expertise->deskripsi),
                        'thumbnail' => $data['thumbnail'] ?? $translation->thumbnail,
                    ]);
                } else {
                    HomeOurExpertise1Translation::create([
                        'ourexpertise1_id' => $expertise->id,
                        'translation_id' => $lang->id,
                        'judul' => $request->input($judulKey, $expertise->judul),
                        'deskripsi' => $request->input($deskripsiKey, $expertise->deskripsi),
                        'thumbnail' => $data['thumbnail'] ?? null,
                    ]);
                }
            }

            return response()->json($expertise->load('translations.translation'), Response::HTTP_OK);

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
            $expertise = HomeOurExpertise1::with('translations')->findOrFail($id);

            if ($expertise->thumbnail && Storage::disk('tvku_storage')->exists($expertise->thumbnail)) {
                Storage::disk('tvku_storage')->delete($expertise->thumbnail);
            }

            foreach ($expertise->translations as $translation) {
                if ($translation->thumbnail && Storage::disk('tvku_storage')->exists($translation->thumbnail)) {
                    Storage::disk('tvku_storage')->delete($translation->thumbnail);
                }
                $translation->delete();
            }

            $expertise->delete();

            return response()->json(['message' => 'Expertise deleted successfully'], Response::HTTP_OK);
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
            $translation = HomeOurExpertise1Translation::find($id);

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
