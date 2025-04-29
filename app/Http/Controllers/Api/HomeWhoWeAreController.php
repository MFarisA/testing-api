<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeWhoWeAre as ModelsHomeWhoWeAre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
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
    
        $query = ModelsHomeWhoWeAre::query();
    
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


    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'motto1' => 'nullable|string|max:100',
                'motto2' => 'nullable|string|max:100',
                'motto3' => 'nullable|string|max:100',
                'motto1sub' => 'nullable|string',
                'motto2sub' => 'nullable|string',
                'motto3sub' => 'nullable|string',
            ]);
    
            $data = $request->except('gambar');
    
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'homewhoweare';
    
                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }
    
                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['gambar'] = $filePath;
            }
            $homeWhoWeAre = ModelsHomeWhoWeAre::create($data);
    
            return response()->json([
                'message' => 'Home Who We Are successfully created',
                'data' => $homeWhoWeAre,
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
        $homeWhoWeAre = ModelsHomeWhoWeAre::find($id);
        if ($homeWhoWeAre) {
            return response()->json($homeWhoWeAre, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Home Who We Are not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, string $id)
    {
        $homeWhoWeAre = ModelsHomeWhoWeAre::find($id);
        if (!$homeWhoWeAre) {
            return response()->json(['message' => 'Home Who We Are not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'motto1' => 'nullable|string|max:100',
            'motto2' => 'nullable|string|max:100',
            'motto3' => 'nullable|string|max:100',
            'motto1sub' => 'nullable|string',
            'motto2sub' => 'nullable|string',
            'motto3sub' => 'nullable|string',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'homewhoweare';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($homeWhoWeAre->gambar) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $homeWhoWeAre->gambar);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }
            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

            $homeWhoWeAre->gambar = $filePath;
        }
        $fields = ['judul', 'deskripsi', 'motto1', 'motto2', 'motto3', 'motto1sub', 'motto2sub', 'motto3sub'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $homeWhoWeAre->{$field} = $validated[$field];
            }
        }

        try {
            $homeWhoWeAre->save();
            $homeWhoWeAre->gambar_url = config('app.tvku_storage.base_url') . '/' . $homeWhoWeAre->gambar;

            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $homeWhoWeAre,
            ], Response::HTTP_OK);
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
        $homeWhoWeAre = ModelsHomeWhoWeAre::find($id);
        if (!$homeWhoWeAre) {
            return response()->json(['message' => 'Home Who We Are not found'], Response::HTTP_NOT_FOUND);
        }

        $homeWhoWeAre->delete();

        return response()->json(['message' => 'Home Who We Are deleted'], Response::HTTP_OK);
    }
}
