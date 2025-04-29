<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecentTrailer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class RecentTrailerController extends Controller
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

        $query = RecentTrailer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                ->orWhere('youtube_id', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('judul', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $recentTrailers = $query->paginate($perPage);

        return response()->json([
            'current_page' => $recentTrailers->currentPage(),
            'per_page' => $recentTrailers->perPage(),
            'total' => $recentTrailers->total(),
            'last_page' => $recentTrailers->lastPage(),
            'next_page_url' => $recentTrailers->nextPageUrl(),
            'prev_page_url' => $recentTrailers->previousPageUrl(),
            'data' => $recentTrailers->items(),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'date' => 'required|date',
            'youtube_id' => 'required|string|max:255',
        ]);

        $recentTrailer = RecentTrailer::create($request->all());

        return response()->json($recentTrailer, Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $recentTrailer = RecentTrailer::find($id);
        if ($recentTrailer) {
            return response()->json($recentTrailer, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'date' => 'required|date',
            'youtube_id' => 'required|string|max:255',
        ]);

        $recentTrailer = RecentTrailer::find($id);
        if ($recentTrailer) {
            $recentTrailer->update($request->all());
            return response()->json($recentTrailer, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function destroy(string $id)
    {
        $recentTrailer = RecentTrailer::find($id);
        if ($recentTrailer) {
            $recentTrailer->delete();
            return response()->json(['message' => 'Recent Trailer successfully deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Recent Trailer not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
