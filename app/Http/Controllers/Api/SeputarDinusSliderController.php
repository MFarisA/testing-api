<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSlider as ModelsSeputarDinusSlider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class SeputarDinusSliderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1); 
        $search = $request->input('search', null); 
        $sort = $request->input('sort', 'id_desc'); 
        $idSlidesTitle = $request->input('id_slides_title', null);

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = ModelsSeputarDinusSlider::query();

        if ($idSlidesTitle) {
            $query->where('id_slides_title', $idSlidesTitle);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('teks', 'like', '%' . $search . '%')
                ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('teks', $sort);
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
                'id_slides_title' => 'required|integer',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'thumbnail_hover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'teks' => 'nullable|string|max:255',
                'link' => 'nullable|string',
                'deskripsi' => 'required|string',
            ]);
    
            $data = $request->except(['thumbnail', 'thumbnail_hover']);
    
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'seputar_dinus/thumbnails';
                $filePath = $folderPath . '/' . $filename;
    
                if (!Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->makeDirectory($folderPath);
                }
    
                Storage::disk('public')->put($filePath, file_get_contents($file));
                $data['thumbnail'] = $filePath;
            }
    
            if ($request->hasFile('thumbnail_hover')) {
                $file = $request->file('thumbnail_hover');
                $filename = now()->format('d-m-Y') . '_hover_' . $file->getClientOriginalName();
                $folderPath = 'seputar_dinus/thumbnails';
                $filePath = $folderPath . '/' . $filename;
    
                if (!Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->makeDirectory($folderPath);
                }
    
                Storage::disk('public')->put($filePath, file_get_contents($file));
    
                $data['thumbnail_hover'] = $filePath;
            }
    
            $seputarDinusSlider = ModelsSeputarDinusSlider::create($data);
    
            return response()->json([
                'message' => 'Seputar Dinus Slider successfully created',
                'data' => $seputarDinusSlider,
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
        $seputarDinusSlider = ModelsSeputarDinusSlider::find($id);
        if ($seputarDinusSlider) {
            return response()->json($seputarDinusSlider, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    public function update(Request $request, string $id)
    {
        $seputarDinusSlider = ModelsSeputarDinusSlider::find($id);
        if (!$seputarDinusSlider) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'id_slides_title' => 'required|integer',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_hover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'teks' => 'nullable|string|max:255',
            'link' => 'nullable|string',
            'deskripsi' => 'required|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'seputar_dinus/thumbnails';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }

            if ($seputarDinusSlider->thumbnail) {
                $oldPath = $seputarDinusSlider->thumbnail;
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            Storage::disk('public')->put($filePath, file_get_contents($file));
            $seputarDinusSlider->thumbnail = $filePath;
        }

        if ($request->hasFile('thumbnail_hover')) {
            $file = $request->file('thumbnail_hover');
            $filename = now()->format('d-m-Y') . '_hover_' . $file->getClientOriginalName();
            $folderPath = 'seputar_dinus/thumbnails';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }

            if ($seputarDinusSlider->thumbnail_hover) {
                $oldPath = $seputarDinusSlider->thumbnail_hover;
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            Storage::disk('public')->put($filePath, file_get_contents($file));
            $seputarDinusSlider->thumbnail_hover = $filePath;
        }

        $fields = ['id_slides_title', 'teks', 'link', 'deskripsi'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $seputarDinusSlider->{$field} = $validated[$field];
            }
        }

        try {
            $seputarDinusSlider->save();

            $seputarDinusSlider->thumbnail_url = asset('storage/' . $seputarDinusSlider->thumbnail);
            $seputarDinusSlider->thumbnail_hover_url = asset('storage/' . $seputarDinusSlider->thumbnail_hover);

            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $seputarDinusSlider,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return response()->json([
                'message' => 'Failed to update data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(string $id)
    {
        $seputarDinusSlider = ModelsSeputarDinusSlider::find($id);
        if (!$seputarDinusSlider) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $seputarDinusSlider->delete();
        return response()->json(['message' => 'Data deleted successfully'], Response::HTTP_OK);
    }
}