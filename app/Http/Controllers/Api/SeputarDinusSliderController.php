<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSlider as ModelsSeputarDinusSlider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeputarDinusSliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ModelsSeputarDinusSlider::all(),Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_slides_title' => 'required|integer',
            'thumbnail' => 'nullable|string|max:255',
            'thumbnail_hover' => 'nullable|string|max:255',
            'teks' => 'nullable|string|max:255',
            'link' => 'nullable|string',
            'deskripsi' => 'required|string',
        ]);

        $seputarDinusSlider = ModelsSeputarDinusSlider::create($request->all());
        return response()->json($seputarDinusSlider, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seputarDinusSlider = ModelsSeputarDinusSlider::find($id);
        if ($seputarDinusSlider) {
            return response()->json($seputarDinusSlider, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_slides_title' => 'required|integer',
            'thumbnail' => 'nullable|string|max:255',
            'thumbnail_hover' => 'nullable|string|max:255',
            'teks' => 'nullable|string|max:255',
            'link' => 'nullable|string',
            'deskripsi' => 'required|string',
        ]);

        $seputarDinusSlider = ModelsSeputarDinusSlider::find($id);
        if ($seputarDinusSlider) {
            $seputarDinusSlider->update($request->all());
            return response()->json($seputarDinusSlider, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $seputarDinusSlider = ModelsSeputarDinusSlider::find($id);
        if ($seputarDinusSlider) {
            $seputarDinusSlider->delete();
            return response()->json(['message' => 'Data deleted'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }
}
