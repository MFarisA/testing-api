<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Acara;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AcaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Acara::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_acara' => 'nullable|string|max:250',
            'thumbnail_acara' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:1000',
            'path' => 'nullable|string|max:255',
        ]);

        $acara = Acara::create($request->all());
        return response()->json($acara, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_acara)
    {
        $acara = Acara::where('id_acara', $id_acara)->first();
        if (!$acara) {
            return response()->json(['message' => 'Acara not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($acara, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_acara)
    {
        $acara = Acara::where('id_acara', $id_acara)->first();
        if (!$acara) {
            return response()->json(['message' => 'Acara not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'nama_acara' => 'nullable|string|max:250',
            'thumbnail_acara' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:1000',
            'path' => 'nullable|string|max:255',
        ]);

        $acara->update($request->all());
        return response()->json($acara, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_acara)
    {
        $acara = Acara::where('id_acara', $id_acara)->first();
        if (!$acara) {
            return response()->json(['message' => 'Acara not found'], Response::HTTP_NOT_FOUND);
        }

        $acara->delete();
        return response()->json(['message' => 'Acara deleted successfully'], Response::HTTP_OK);
    }
}
