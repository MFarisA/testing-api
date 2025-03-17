<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\HomeOurExpertise2;

class HomeOurExpertise2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(HomeOurExpertise2::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'thumbnail' => 'nullable|string|max:255',
            'judul' => 'nullable|string|max:255',
        ]);

        $expertise = HomeOurExpertise2::create($request->all());
        return response()->json($expertise, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expertise = HomeOurExpertise2::find($id);
        if (!$expertise) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($expertise, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expertise = HomeOurExpertise2::find($id);
        if (!$expertise) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'thumbnail' => 'nullable|string|max:255',
            'judul' => 'nullable|string|max:255',
        ]);

        $expertise->update($request->all());
        return response()->json($expertise, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expertise = HomeOurExpertise2::find($id);
        if (!$expertise) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $expertise->delete();
        return response()->json(['message' => 'Data deleted successfully'], Response::HTTP_OK);
    }
}
