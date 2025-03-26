<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSlider as ModelsSeputarDinusSlider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class SeputarDinusSliderController extends Controller
{
    /**
     * Display a listing of the resource with pagination and search.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20); 
        $currentPage = $request->input('current_page', 1); 
        $search = $request->input('search', null);

        // Resolve current page for pagination
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        // Query with optional search
        $query = ModelsSeputarDinusSlider::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('teks', 'like', '%' . $search . '%')->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        // Paginate the results
        $sliders = $query->paginate($perPage);

        return response()->json([
            'current_page' => $sliders->currentPage(),
            'per_page' => $sliders->perPage(),
            'total' => $sliders->total(),
            'last_page' => $sliders->lastPage(),
            'next_page_url' => $sliders->nextPageUrl(),
            'prev_page_url' => $sliders->previousPageUrl(),
            'data' => $sliders->items(),
        ], Response::HTTP_OK);
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
        if (!$seputarDinusSlider) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $seputarDinusSlider->update($request->all());
        return response()->json($seputarDinusSlider, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $seputarDinusSlider = ModelsSeputarDinusSlider::find($id);
        if (!$seputarDinusSlider) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $seputarDinusSlider->delete();
        return response()->json(['message' => 'Data deleted successfully'], Response::HTTP_OK);
    }
}