<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Acara;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;

class AcaraController extends Controller
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_acara' => 'required|string|max:250',
                'thumbnail_acara' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'description' => 'nullable|string|max:1000',
                'path' => 'nullable|string|max:255',
            ]);

            $data = $request->except('thumbnail_acara');

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
            return response()->json($acara, Response::HTTP_CREATED);
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
    public function show($id_acara)
    {
        $acara = Acara::where('id_acara', $id_acara)->first();
        if (!$acara) {
            return response()->json(['message' => 'Acara not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($acara, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_acara)
    {
        $acara = Acara::find($id_acara);
        if (!$acara) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'thumbnail_acara' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_acara' => 'nullable|string|max:250',
            'description' => 'nullable|string|max:1000',
            'path' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('thumbnail_acara')) {
            $file = $request->file('thumbnail_acara');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'acara/thumbnails';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($acara->thumbnail_acara) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $acara->thumbnail_acara);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

            $acara->thumbnail_acara = $filePath;
        }

        $fields = ['nama_acara', 'description', 'path'];
        
        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $acara->{$field} = $validated[$field];
            }
        }

        try {
            $acara->save();
            $acara->thumbnail_acara = config('app.tvku_storage.base_url') . '/' . $acara->thumbnail_acara;
            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $acara,
            ]);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_acara)
    {
        try {
            $acara = Acara::findOrFail($id_acara);
    
            if ($acara->thumbnail_acara && Storage::disk('tvku_storage')->exists($acara->thumbnail_acara)) {
                Storage::disk('tvku_storage')->delete($acara->thumbnail_acara);
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
}
