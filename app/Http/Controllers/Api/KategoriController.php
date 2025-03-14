<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KategoriController extends Controller
{
    public function index()
    {
        return response()->json(Kategori::all(), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:tb_kategori,nama',
            'slug' => 'required|string|max:20|unique:tb_kategori,slug',
            'top_nav' => 'nullable|in:0,1',
            'urutan' => 'nullable|integer',
        ]);

        $kategori = Kategori::create($request->all());
        return response()->json($kategori, Response::HTTP_CREATED);
    }

    public function show($id_kategori)
    {
        $kategori = Kategori::where('id_kategori', $id_kategori)->first();
        if (!$kategori) {
            return response()->json(['message' => 'Kategori not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($kategori, Response::HTTP_OK);
    }

    public function update(Request $request, $id_kategori)
    {
        $kategori = Kategori::where('id_kategori', $id_kategori)->first();
        if (!$kategori) {
            return response()->json(['message' => 'Kategori not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'nama' => 'sometimes|required|string|max:255|unique:tb_kategori,nama,' . $id_kategori . ',id_kategori',
            'slug' => 'sometimes|required|string|max:20|unique:tb_kategori,slug,' . $id_kategori . ',id_kategori',
            'top_nav' => 'nullable|in:0,1',
            'urutan' => 'nullable|integer',
        ]);

        $kategori->update($request->all());
        return response()->json($kategori, Response::HTTP_OK);
    }

    public function destroy($id_kategori)
    {
        $kategori = Kategori::where('id_kategori', $id_kategori)->first();
        if (!$kategori) {
            return response()->json(['message' => 'Kategori not found'], Response::HTTP_NOT_FOUND);
        }

        $kategori->delete();
        return response()->json(['message' => 'Kategori deleted successfully'], Response::HTTP_OK);
    }
}
