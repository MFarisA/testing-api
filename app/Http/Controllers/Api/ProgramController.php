<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Program::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'video' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|string|max:1000',
            'deskripsi' => 'nullable|string',
            'deskripsi_pendek' => 'nullable|string',
            'id_acara' => 'required|exists:tb_acara,id_acara',
            'tanggal' => 'nullable|date',
        ]);

        $program = Program::create($request->all());
        return response()->json($program, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($program, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'video' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|string|max:1000',
            'deskripsi' => 'nullable|string',
            'deskripsi_pendek' => 'nullable|string',
            'id_acara' => 'sometimes|required|exists:tb_acara,id_acara',
            'tanggal' => 'nullable|date',
        ]);

        $program->update($request->all());
        return response()->json($program, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program not found'], Response::HTTP_NOT_FOUND);
        }

        $program->delete();
        return response()->json(['message' => 'Program deleted successfully'], Response::HTTP_OK);
    }
}
