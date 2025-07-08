<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class TranslationController extends Controller
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

        $query = Translation::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('name', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $translations = $query->paginate($perPage);

        return response()->json([
            'current_page' => $translations->currentPage(),
            'per_page' => $translations->perPage(),
            'total' => $translations->total(),
            'last_page' => $translations->lastPage(),
            'next_page_url' => $translations->nextPageUrl(),
            'prev_page_url' => $translations->previousPageUrl(),
            'data' => $translations->items(),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:translations,code',
        ]);

        $translation = Translation::create($request->all());

        return response()->json($translation, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $translation = Translation::find($id);
        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($translation, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $translation = Translation::find($id);
        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:10|unique:translations,code,' . $id,
        ]);

        $translation->update($request->all());

        return response()->json($translation, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $translation = Translation::find($id);
        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        }

        $translation->delete();
        return response()->json(['message' => 'Translation deleted successfully'], Response::HTTP_OK);
    }
}
