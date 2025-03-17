<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeWhoWeAre as ModelsHomeWhoWeAre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeWhoWeAreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ModelsHomeWhoWeAre::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'motto1' => 'nullable|string|max:100',
            'motto2' => 'nullable|string|max:100',
            'motto3' => 'nullable|string|max:100',
            'motto1sub' => 'nullable|string',
            'motto2sub' => 'nullable|string',
            'motto3sub' => 'nullable|string',
        ]);

        $data = $request->except('gambar');
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('home_whoweare', 'public');
            $data['gambar'] = $path;
        }

        $homeWhoWeAre = ModelsHomeWhoWeAre::create($data);

        return response()->json($homeWhoWeAre, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $homeWhoWeAre = ModelsHomeWhoWeAre::find($id);
        if ($homeWhoWeAre) {
            return response()->json($homeWhoWeAre, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Home Who We Are not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'motto1' => 'nullable|string|max:100',
            'motto2' => 'nullable|string|max:100',
            'motto3' => 'nullable|string|max:100',
            'motto1sub' => 'nullable|string',
            'motto2sub' => 'nullable|string',
            'motto3sub' => 'nullable|string',
        ]);

        $homeWhoWeAre = ModelsHomeWhoWeAre::find($id);
        if (!$homeWhoWeAre) {
            return response()->json(['message' => 'Home Who We Are not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->except('gambar');
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('home_whoweare', 'public');
            $data['gambar'] = $path;
        }

        $homeWhoWeAre->update($data);

        return response()->json($homeWhoWeAre, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $homeWhoWeAre = ModelsHomeWhoWeAre::find($id);
        if (!$homeWhoWeAre) {
            return response()->json(['message' => 'Home Who We Are not found'], Response::HTTP_NOT_FOUND);
        }

        $homeWhoWeAre->delete();

        return response()->json(['message' => 'Home Who We Are deleted'], Response::HTTP_OK);
    }
}
