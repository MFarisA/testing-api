<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramAcara;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class ProgramAcaraController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');
        $idAcara = $request->input('id_acara', null);

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = ProgramAcara::query();

        if ($idAcara) {
            $query->where('id_acara', $idAcara);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('judul', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id_program', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id_program', 'desc');
        } else {
            $query->orderBy('id_program', 'desc');
        }

        $program = $query->paginate($perPage);

        return response()->json([
            'current_page' => $program->currentPage(),
            'per_page' => $program->perPage(),
            'total' => $program->total(),
            'last_page' => $program->lastPage(),
            'next_page_url' => $program->nextPageUrl(),
            'prev_page_url' => $program->previousPageUrl(),
            'data' => $program->items(),
        ], Response::HTTP_OK);
    }

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

        $program = ProgramAcara::create($request->all());
        return response()->json($program, Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $program = ProgramAcara::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program Acara not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($program, Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        $program = ProgramAcara::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program Acara not found'], Response::HTTP_NOT_FOUND);
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

    public function destroy(string $id)
    {
        $program = ProgramAcara::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program Acara not found'], Response::HTTP_NOT_FOUND);
        }

        $program->delete();
        return response()->json(['message' => 'Program Acara deleted successfully'], Response::HTTP_OK);
    }
}
