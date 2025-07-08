<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider as ModelsHomeSlider;
use App\Models\HomeSliderTranslation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeSliderController extends Controller
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

        $query = ModelsHomeSlider::with('translations.translation');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                    ->orWhere('sub_judul', 'like', '%' . $search . '%');
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

        $sliders = $query->paginate($perPage);

        return response()->json([
            'current_page' => $sliders->currentPage(),
            'per_page' => $sliders->perPage(),
            'total' => $sliders->total(),
            'last_page' => $sliders->lastPage(),
            'next_page_url' => $sliders->nextPageUrl(),
            'prev_page_url' => $sliders->previousPageUrl(),
            'data' => $sliders->items(),
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

        $query = HomeSliderTranslation::where('translation_id', $translation->id);

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
                'gambar' => $item->gambar,
                'judul' => $item->judul,
                'sub_judul' => $item->sub_judul,
                'urutan' => $item->urutan,
                'url' => $item->url,
                'translation_id' => $item->translation_id,
                'slider_id' => $item->ourexpertise2_id,
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
        try {
            $request->validate([
                'judul' => 'nullable|string|max:255',
                'sub_judul' => 'nullable|string|max:255',
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'urutan' => 'nullable|integer',
                'url' => 'nullable|url',
            ]);

            $allLangs = Translation::all();
            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul' . $lang->code => 'required|string|max:255',
                    'sub_judul' . $lang->code => 'required|string|max:255',
                ]);
            }

            $data = $request->except('gambar');

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'home/slider';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }
                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                $data['gambar'] = $filePath;
            }
            $homeslider = ModelsHomeSlider::create($data);
            foreach ($allLangs as $lang) {
                $judulKey = 'judul' . $lang->code;
                $sub_judulKey = 'sub_judul' . $lang->code;

                $homeslider->translations()->create([
                    'slider_id' => $homeslider->id,
                    'translation_id' => $lang->id,
                    'judul' => $request->input($judulKey),
                    'sub_judul' => $request->input($sub_judulKey),
                ]);
            }

            return response()->json([
                'message' => 'Home Slider successfully created',
                'data' => $homeslider,
                'data' => $homeslider->load('translations.translation'),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $id)
    {
        $homeslider = ModelsHomeSlider::find($id);
        if ($homeslider) {
            return response()->json($homeslider, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Home Slider not found'], Response::HTTP_NOT_FOUND);
        }
    }
    public function update(Request $request, string $id)
    {
        $homeslider = ModelsHomeSlider::find($id);
        if (!$homeslider) {
            return response()->json(['message' => 'Home Slider not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'sub_judul' => 'nullable|string|max:255',
            'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'urutan' => 'nullable|integer',
            'url' => 'nullable|url',
        ]);

        $allLangs = Translation::all();
        foreach ($allLangs as $lang) {
            $request->validate([
                'judul' . $lang->code => 'nullable|string|max:255',
                'sub_judul' . $lang->code => 'nullable|string|max:255',
            ]);
        }

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'home/slider';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($homeslider->gambar && Storage::disk('tvku_storage')->exists($homeslider->gambar)) {
                Storage::disk('tvku_storage')->delete($homeslider->gambar);
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $homeslider->gambar = $filePath;
        }

        $fields = ['judul', 'sub_judul', 'urutan', 'url'];
        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $homeslider->{$field} = $validated[$field];
            }
        }

        try {
            $homeslider->update();

            foreach ($allLangs as $lang) {
                $judulKey =  'judul' . $lang->code;
                $sub_judulKey = 'sub_judul' . $lang->code;

                $translation = HomeSliderTranslation::where('slider_id', $homeslider->id)
                    ->where('translation_id', $lang->id)
                    ->first();
                if ($translation) {
                    $translation->update([
                        'judul' => $request->input($judulKey),
                        'sub_judul' => $request->input($sub_judulKey),
                        'urutan' => $homeslider->urutan,
                    ]);
                } else {
                    HomeSliderTranslation::create([
                        'slider_id' => $homeslider->id,
                        'translation_id' => $lang->id,
                        'judul' => $request->input($judulKey),
                        'sub_judul' => $request->input($sub_judulKey),
                    ]);
                }
            }

            return response()->json([
                'message' => 'Home Slider successfully updated!',
                'data' => $homeslider->load('translations.translation'),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            if (isset($filePath) && Storage::disk('tvku_storage')->exists($filePath)) {
                Storage::disk('tvku_storage')->delete($filePath);
            }
            return response()->json([
                'message' => 'Failed to update Home Slider',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(string $id)
    {
        try {
            $homeslider = ModelsHomeSlider::findOrFail($id);

            if ($homeslider->gambar && Storage::disk('tvku_storage')->exists($homeslider->gambar)) {
                Storage::disk('tvku_storage')->delete($homeslider->gambar);
            }
            $homeslider->delete();

            return response()->json(['message' => 'Home Slider successfully deleted'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyTranslation($id)
    {
        $translation = HomeSliderTranslation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        $translation->delete();

        return response()->json(['message' => 'Translation deleted successfully'], 200);
    }
}
