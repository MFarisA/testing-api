<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Berita::all(), Response::HTTP_OK);
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
            'cover' => 'nullable|string|max:1000',
            'keyword' => 'nullable|string|max:500',
            'editor' => 'nullable|integer|min:0|max:1',
            'library' => 'nullable|integer|min:0|max:1',
            'redaktur' => 'nullable|integer|min:0|max:1',
            'waktu_publish' => 'nullable|date_format:Y-m-d H:i:s',
            'program_id' => 'nullable|exists:tb_program,id',
            'type' => 'nullable|in:video,cetak,old',
        ]);

        $berita = Berita::create($request->all());
        return response()->json($berita, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($berita, Response::HTTP_OK);
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
            'cover' => 'nullable|string|max:1000',
            'keyword' => 'nullable|string|max:500',
            'editor' => 'nullable|integer|min:0|max:1',
            'library' => 'nullable|integer|min:0|max:1',
            'redaktur' => 'nullable|integer|min:0|max:1',
            'waktu_publish' => 'nullable|date_format:Y-m-d H:i:s',
            'program_id' => 'nullable|exists:tb_program,id',
            'type' => 'nullable|in:video,cetak,old',
        ]);

        $berita->update($request->all());
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