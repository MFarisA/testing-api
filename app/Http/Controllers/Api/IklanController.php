<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Iklan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IklanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Iklan::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'foto' => 'nullable|string',
            'isi' => 'nullable|string',
            'video' => 'nullable|string',
        ]);

        $iklan = Iklan::create($request->all());
        return response()->json($iklan, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $iklan = Iklan::find($id);
        if (!$iklan) {
            return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($iklan, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $iklan = Iklan::find($id);
        if (!$iklan) {
            return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'foto' => 'nullable|string',
            'isi' => 'nullable|string',
            'video' => 'nullable|string',
        ]);

        $iklan->update($request->all());
        return response()->json($iklan, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $iklan = Iklan::find($id);
        if (!$iklan) {
            return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
        }

        $iklan->delete();
        return response()->json(['message' => 'Iklan deleted successfully'], Response::HTTP_OK);
    }
}
