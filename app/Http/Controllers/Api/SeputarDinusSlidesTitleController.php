<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSlidesTitle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class SeputarDinusSlidesTitleController extends Controller
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

        $query = SeputarDinusSlidesTitle::query();

        if ($search) {
            $query->where('judul', 'like', '%' . $search . '%');
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

        $slidesTitles = $query->paginate($perPage);

        return response()->json([
            'current_page' => $slidesTitles->currentPage(),
            'per_page' => $slidesTitles->perPage(),
            'total' => $slidesTitles->total(),
            'last_page' => $slidesTitles->lastPage(),
            'next_page_url' => $slidesTitles->nextPageUrl(),
            'prev_page_url' => $slidesTitles->previousPageUrl(),
            'data' => $slidesTitles->items(),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::create($request->all());
        return response()->json($seputarDinusSlidesTitle, Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::find($id);
        if ($seputarDinusSlidesTitle) {
            return response()->json($seputarDinusSlidesTitle, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::find($id);
        if ($seputarDinusSlidesTitle) {
            $seputarDinusSlidesTitle->update($request->all());
            return response()->json($seputarDinusSlidesTitle, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    public function destroy(string $id)
    {
        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::find($id);
        if ($seputarDinusSlidesTitle) {
            $seputarDinusSlidesTitle->delete();
            return response()->json(['message' => 'Data deleted'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }
}
