<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource with pagination and search.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = Berita::with('kategori');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        $berita = $query->paginate($perPage);

        return response()->json([
            'current_page' => $berita->currentPage(),
            'per_page' => $berita->perPage(),
            'total' => $berita->total(),
            'last_page' => $berita->lastPage(),
            'next_page_url' => $berita->nextPageUrl(),
            'prev_page_url' => $berita->previousPageUrl(),
            'data' => $berita->items(),
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'path_media' => 'nullable|string|max:1000',
            'link' => 'nullable|url|max:1000',
            'filename' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'waktu' => 'required|date_format:Y-m-d H:i:s',
            'id_uploader' => 'required|exists:users,id',
            'id_kategori' => 'required|exists:tb_kategori,id_kategori',
            'publish' => 'nullable|boolean',
            'open' => 'nullable|boolean',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'keyword' => 'nullable|string|max:500',
            'editor' => 'nullable|integer|min:0|max:1',
            'library' => 'nullable|integer|min:0|max:1',
            'redaktur' => 'nullable|integer|min:0|max:1',
            'waktu_publish' => 'nullable|date_format:Y-m-d H:i:s',
            'program_id' => 'nullable|exists:tb_program,id_program',
            'type' => 'nullable|in:video,cetak,old',
        ]);

        $data = $request->except(['cover']);

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = config('app.tvku_storage.thumbnail_berita_path') . '/' . $filename;

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $data['cover'] = $filename; 
        }

        $berita = Berita::create($data);
        return response()->json($berita, Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'path_media' => 'nullable|string|max:1000', 
            'link' => 'nullable|url|max:1000',
            'filename' => 'nullable|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'waktu' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'id_uploader' => 'sometimes|required|exists:users,id',
            'id_kategori' => 'sometimes|required|exists:tb_kategori,id_kategori',
            'publish' => 'nullable|boolean',
            'open' => 'nullable|boolean',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', 
            'keyword' => 'nullable|string|max:500',
            'editor' => 'nullable|integer|min:0|max:1',
            'library' => 'nullable|integer|min:0|max:1',
            'redaktur' => 'nullable|integer|min:0|max:1',
            'waktu_publish' => 'nullable|date_format:Y-m-d H:i:s',
            'program_id' => 'nullable|exists:tb_program,id_program',
            'type' => 'nullable|in:video,cetak,old',
        ]);

        $data = $request->except(['cover']);

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = config('app.tvku_storage.thumbnail_berita_path') . '/' . $filename;

            if ($berita->cover) {
                Storage::disk('tvku_storage')->delete(config('app.tvku_storage.thumbnail_berita_path') . '/' . $berita->cover);
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $data['cover'] = $filename;
        }

        $berita->update($data);
        return response()->json($berita, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        }

        $berita->delete();
        return response()->json(['message' => 'Berita deleted successfully'], Response::HTTP_OK);
    }
}