<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeOurExpertise1;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Validator;

class HomeOurExpertise1Controller extends Controller
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

        $query = HomeOurExpertise1::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                ->orWhere('deskripsi', 'like', '%' . $search . '%');
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

        $expertise = $query->paginate($perPage);

        return response()->json([
            'current_page' => $expertise->currentPage(),
            'per_page' => $expertise->perPage(),
            'total' => $expertise->total(),
            'last_page' => $expertise->lastPage(),
            'next_page_url' => $expertise->nextPageUrl(),
            'prev_page_url' => $expertise->previousPageUrl(),
            'data' => $expertise->items(),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $data = $request->except('thumbnail');
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'ourexpertise';

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            $filePath = $folderPath . '/' . $filename;
            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $data['thumbnail'] = $filePath;
        }

        $expertise = HomeOurExpertise1::create($data);

        return response()->json([
            'message' => 'Expertise successfully created',
            'data' => $expertise,
        ], Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $expertise = HomeOurExpertise1::find($id);
        if (!$expertise) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($expertise, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $expertise = HomeOurExpertise1::find($id);
        if (!$expertise) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'ourexpertise';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($expertise->thumbnail) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $expertise->thumbnail);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

            $expertise->thumbnail = $filePath;
        }

        $fields = ['judul', 'deskripsi'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $expertise->{$field} = $validated[$field];
            }
        }

        try {
            $expertise->save();

            $expertise->thumbnail = config('app.tvku_storage.base_url') . '/' . $expertise->thumbnail;

            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $expertise,
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
        $expertise = HomeOurExpertise1::find($id);
        if (!$expertise) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $expertise->delete();
        return response()->json(['message' => 'Data deleted successfully'], Response::HTTP_OK);
    }
}
