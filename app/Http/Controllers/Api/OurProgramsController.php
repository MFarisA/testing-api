<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OurPrograms as ModelsOurPrograms;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OurProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ModelsOurPrograms::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'link' => 'required|url',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $data = $request->except('thumbnail');
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('our_programs', 'public');
            $data['thumbnail'] = $path;
        }
        return response()->json(ModelsOurPrograms::create($data), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ourPrograms = ModelsOurPrograms::find($id);
        if ($ourPrograms) {
            return response()->json($ourPrograms, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Our Programs not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'link' => 'required|url',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $data = $request->except('thumbnail');
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('our_programs', 'public');
            $data['thumbnail'] = $path;
        }

        $ourPrograms = ModelsOurPrograms::find($id);
        if ($ourPrograms) {
            $ourPrograms->update($data);
            return response()->json($ourPrograms, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Our Programs not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ourPrograms = ModelsOurPrograms::find($id);
        if ($ourPrograms) {
            $ourPrograms->delete();
            return response()->json(['message' => 'Our Programs deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Our Programs not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
