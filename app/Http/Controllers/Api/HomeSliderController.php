<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider as ModelsHomeSlider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeSliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ModelsHomeSlider::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'sub_judul' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'urutan' => 'nullable|integer',
            'url' => 'nullable|url',
        ]);

        $data = $request->all();
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('home_sliders', 'public');
            $data['gambar'] = $path;
        }

        $homeslider = ModelsHomeSlider::create($data);

        try {
            return response()->json(['message' => 'Home Slider successfully created', 'data' => $homeslider], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create Home Slider', 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $homeslider = ModelsHomeSlider::find($id);
        if($homeslider) {
            return response()->json($homeslider, Response::HTTP_OK);
        }
        else {
            return response()->json(['message' => 'Home Slider not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsHomeSlider $homeslider)
    {
        try {
            $request->validate([
                'judul' => 'nullable|string|max:255',
                'sub_judul' => 'nullable|string|max:255',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'urutan' => 'nullable|integer',
                'url' => 'nullable|url',
            ]);

            $data = $request->all();
            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('home_sliders', 'public');
                $data['gambar'] = $path;
            }

            $homeslider->update($data);

            return response()->json(['message' => 'Home Slider successfully updated', 'data' => $homeslider], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update Home Slider', 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $homeslider = ModelsHomeSlider::find($id);
        if ($homeslider) {
            $homeslider->delete();
            return response()->json(['message' => 'Home Slider successfully deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Home Slider not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
