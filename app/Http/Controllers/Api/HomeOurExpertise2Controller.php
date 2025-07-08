<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\HomeOurExpertise2;
use App\Models\HomeOurexpertise2Translation;
use App\Models\Translation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class HomeOurExpertise2Controller extends Controller
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

        $query = HomeOurExpertise2::with('translations.translation');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%');
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

    public function getOnlyTranslationData(Request $request)
    {
        $langId = $request->input('translation_id');

        if (!$langId) {
            return response()->json(['message' => 'Translation ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $translation = Translation::find($langId);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        }

        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = HomeOurExpertise2Translation::where('translation_id', $translation->id);

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

        $paginated = $query->paginate($perPage);

        $data = $paginated->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'thumbnail' => $item->thumbnail,
                'judul' => $item->judul,
                'translation_id' => $item->translation_id,
                'ourexpertise2_id' => $item->ourexpertise2_id,
            ];
        });

        return response()->json([
            'translation' => [
                'id' => $translation->id,
                'name' => $translation->name,
                'code' => $translation->code,
            ],
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'judul' => 'required|string|max:255',
        ]);
        
        $allLangs = Translation::all();
        foreach ($allLangs as $lang) {
            $validator->addRules([
                'judul' . $lang->code => 'nullable|string|max:255',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $data = $request->except('thumbnail', 'translations');

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'home/ourexpertise2';

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            $filePath = $folderPath . '/' . $filename;
            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $data['thumbnail'] = $filePath;
        }

        $expertise = HomeOurExpertise2::create($data);
        foreach ($allLangs as $lang) {
            $judulKey = 'judul' . $lang->code;

            HomeOurexpertise2Translation::create([
                'ourexpertise2_id' => $expertise->id,
                'translation_id' => $lang->id,
                'judul' => $request->input($judulKey),
            ]);
        }
        return response()->json($expertise->load('translations.translation'), Response::HTTP_CREATED);
    }


    public function show(string $id)
    {
        $expertise = HomeOurExpertise2::find($id);
        if (!$expertise) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($expertise, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $expertise = HomeOurExpertise2::find($id);
        if (!$expertise) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'judul' => 'required|string|max:255',
        ]);

        $allLangs = Translation::all();
        foreach ($allLangs as $lang) {
            $request->validate([
                'judul' . $lang->code => 'nullable|string|max:255',
            ]);
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'home/ourexpertise2';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($expertise->thumbnail) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $expertise->thumbnail);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $expertise->thumbnail = $filePath;
        }

        $fields = ['judul'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $expertise->{$field} = $validated[$field];
            }
        }

        try {
            $expertise->update();
            foreach ($allLangs as $lang) {
                $judulKey =  'judul' . $lang->code;

                $translation = HomeOurexpertise2Translation::where('ourexpertise2_id', $expertise->id)
                    ->where('translation_id', $lang->id)
                    ->first();
                if ($translation) {
                    $translation->update([
                        'judul' => $request->input($judulKey),
                    ]);
                } else {
                    HomeOurexpertise2Translation::create([
                        'ourexpertise2_id' => $expertise->id,
                        'translation_id' => $lang->id,
                        'judul' => $request->input($judulKey),
                    ]);
                }
            }
            return response()->json($expertise->load('translations.translation'), Response::HTTP_OK);
        } catch (\Exception $e) {
            if (isset($filePath) && Storage::disk('tvku_storage')->exists($filePath)) {
                Storage::disk('tvku_storage')->delete($filePath);
            }
            return response()->json([
                'message' => 'Failed to update data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(string $id)
    {
        try {
            $expertise = HomeOurExpertise2::findOrFail($id);

            foreach ($expertise->translations as $translation) {
                $translation->delete();;
            }

            if ($expertise->thumbnail && Storage::disk('tvku_storage')->exists($expertise->thumbnail)) {
                Storage::disk('tvku_storage')->delete($expertise->thumbnail);
            }
            $expertise->delete();

            return response()->json(['message' => 'Original data and its translations deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyTranslation($id)
    {
        $translation = HomeOurexpertise2Translation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        $translation->delete();

        return response()->json(['message' => 'Translation deleted successfully'], 200);
    }
}
