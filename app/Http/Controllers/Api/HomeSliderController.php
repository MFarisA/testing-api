<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider as ModelsHomeSlider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
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

        $query = ModelsHomeSlider::query();

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

            $data = $request->except('gambar');

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'slider';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }
                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                $data['gambar'] = $filePath;
            }
            $homeslider = ModelsHomeSlider::create($data);

            return response()->json([
                'message' => 'Home Slider successfully created',
                'data' => $homeslider,
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
        if($homeslider) {
            return response()->json($homeslider, Response::HTTP_OK);
        }
        else {
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
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'urutan' => 'nullable|integer',
            'url' => 'nullable|url',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'slider';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($homeslider->gambar) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $homeslider->gambar);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
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
            $homeslider->save();
            $homeslider->gambar_url = config('app.tvku_storage.base_url') . '/' . $homeslider->gambar;

            return response()->json([
                'message' => 'Home Slider successfully updated!',
                'data' => $homeslider,
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
        $homeslider = ModelsHomeSlider::find($id);
        if ($homeslider) {
            $homeslider->delete();
            return response()->json(['message' => 'Home Slider successfully deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Home Slider not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
