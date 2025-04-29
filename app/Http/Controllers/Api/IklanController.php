<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Iklan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class IklanController extends Controller
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

        $query = Iklan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                ->orWhere('isi', 'like', '%' . $search . '%');
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

        $iklan = $query->paginate($perPage);

        return response()->json([
            'current_page' => $iklan->currentPage(),
            'per_page' => $iklan->perPage(),
            'total' => $iklan->total(),
            'last_page' => $iklan->lastPage(),
            'next_page_url' => $iklan->nextPageUrl(),
            'prev_page_url' => $iklan->previousPageUrl(),
            'data' => $iklan->items(),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'isi' => 'nullable|string|max:1000',
                'video' => 'nullable|string|max:255',
            ]);

            $data = $request->except('foto');

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName(); 

                $folderPath = 'iklan';
                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;

                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                $data['foto'] = $filePath;
            }

            $iklan = Iklan::create($data);
            return response()->json($iklan, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $id)
    {
        $iklan = Iklan::find($id);
        if (!$iklan) {
            return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($iklan, Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        $iklan = Iklan::find($id);
        if (!$iklan) {
            return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'isi' => 'nullable|string|max:1000',
            'video' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'iklan';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($iklan->foto) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $iklan->foto);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $iklan->foto = $filePath;
        }

        $fields = ['judul', 'isi', 'video'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $iklan->{$field} = $validated[$field];
            }
        }

        try {
            $iklan->save();
            $iklan->foto_url = config('app.tvku_storage.base_url') . '/' . $iklan->foto;

            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $iklan,
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
        $iklan = Iklan::find($id);
        if (!$iklan) {
            return response()->json(['message' => 'Iklan not found'], Response::HTTP_NOT_FOUND);
        }

        $iklan->delete();
        return response()->json(['message' => 'Iklan deleted successfully'], Response::HTTP_OK);
    }
}
