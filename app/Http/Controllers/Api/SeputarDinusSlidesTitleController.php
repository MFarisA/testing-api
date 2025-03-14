<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSlidesTitle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeputarDinusSlidesTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(SeputarDinusSlidesTitle::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::create($request->all());
        return response()->json($seputarDinusSlidesTitle, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::find($id);
        if ($seputarDinusSlidesTitle) {
            return response()->json($seputarDinusSlidesTitle, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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
