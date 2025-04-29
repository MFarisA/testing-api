<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class KategoriController extends Controller
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

        $query = Kategori::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('nama', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id_kategori', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id_kategori', 'desc');
        } else {
            $query->orderBy('id_kategori', 'desc');
        }

        $kategori = $query->paginate($perPage);

        return response()->json([
            'current_page' => $kategori->currentPage(),
            'per_page' => $kategori->perPage(),
            'total' => $kategori->total(),
            'last_page' => $kategori->lastPage(),
            'next_page_url' => $kategori->nextPageUrl(),
            'prev_page_url' => $kategori->previousPageUrl(),
            'data' => $kategori->items(),
        ], Response::HTTP_OK);
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
