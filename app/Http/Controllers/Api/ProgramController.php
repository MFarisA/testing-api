<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = Program::query();

        if ($search) {
            $query->where('program', 'like', '%' . $search . '%')
                  ->orWhere('path', 'like', '%' . $search . '%')
                  ->orWhere('link', 'like', '%' . $search . '%');
        }

        if ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } elseif ($sort === 'program_asc') {
            $query->orderBy('program', 'asc');
        } elseif ($sort === 'program_desc') {
            $query->orderBy('program', 'desc');
        }

        $programs = $query->paginate($perPage);

        return response()->json([
            'current_page' => $programs->currentPage(),
            'per_page' => $programs->perPage(),
            'total' => $programs->total(),
            'last_page' => $programs->lastPage(),
            'next_page_url' => $programs->nextPageUrl(),
            'prev_page_url' => $programs->previousPageUrl(),
            'data' => $programs->items(),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'program' => 'required|string|max:255',
            'path' => 'required|string|max:255',
            'link' => 'required|string|max:255',
        ]);

        try {
            $program = Program::create($request->all());
            return response()->json([
                'message' => 'Program created successfully',
                'data' => $program,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create program',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Program retrieved successfully',
            'data' => $program,
        ], Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'program' => 'nullable|string|max:255',
            'path' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
        ]);

        try {
            $program->update($request->all());
            return response()->json([
                'message' => 'Program updated successfully',
                'data' => $program,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update program',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(string $id)
    {
        try {
            $program = Program::findOrFail($id);
            $program->delete();

            return response()->json(['message' => 'Program deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete program',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
