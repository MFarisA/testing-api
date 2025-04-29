<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OurPrograms as ModelsOurPrograms;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class OurProgramsController extends Controller
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

        $query = ModelsOurPrograms::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('title', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
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
        try {
            $request->validate([
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'link' => 'required|url',
                'urutan' => 'nullable|integer|min:0',
            ]);

            $data = $request->except('thumbnail');
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'ourprograms';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }
                $filePath = $folderPath . '/' . $filename;

                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                $data['thumbnail'] = $filePath;
            }
            $ourPrograms = ModelsOurPrograms::create($data);

            return response()->json([
                'message' => 'Our Programs successfully created',
                'data' => $ourPrograms,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $id)
    {
        $ourPrograms = ModelsOurPrograms::find($id);
        if ($ourPrograms) {
            return response()->json($ourPrograms, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Our Programs not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, string $id)
    {
        $ourPrograms = ModelsOurPrograms::find($id);
        if (!$ourPrograms) {
            return response()->json(['message' => 'Our Programs not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'link' => 'required|url',
            'urutan' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'ourprograms';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($ourPrograms->thumbnail) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $ourPrograms->thumbnail);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

            $ourPrograms->thumbnail = $filePath;
        }

        $fields = ['judul', 'deskripsi', 'link', 'urutan'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $ourPrograms->{$field} = $validated[$field];
            }
        }

        try {
            $ourPrograms->save();
            $ourPrograms->thumbnail_url = config('app.tvku_storage.base_url') . '/' . $ourPrograms->thumbnail;

            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $ourPrograms,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            if (isset($filePath) && Storage::disk('tvku_storage')->exists($filePath)) {
                Storage::disk('tvku_storage')->delete($filePath);
            }
            return response()->json([
                'message' => 'Failed to update data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

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
