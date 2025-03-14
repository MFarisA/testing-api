<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecentTrailer as ModelsRecentTrailer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecentTrailerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ModelsRecentTrailer::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'date' => 'required|date',
            'youtube_id' => 'required|string|max:255',
        ]);

        $recentTrailer = ModelsRecentTrailer::create($request->all());

        return response()->json($recentTrailer, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $recentTrailer = ModelsRecentTrailer::find($id);
        if ($recentTrailer) {
            return response()->json($recentTrailer, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'date' => 'required|date',
            'youtube_id' => 'required|string|max:255',
        ]);

        $recentTrailer = ModelsRecentTrailer::find($id);
        if ($recentTrailer) {
            $recentTrailer->update($request->all());
            return response()->json($recentTrailer, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $recentTrailer = ModelsRecentTrailer::find($id);
        if ($recentTrailer) {
            $recentTrailer->delete();
            return response()->json(['message' => 'Recent Trailer successfully deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
