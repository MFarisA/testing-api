<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');
        $idKategori = $request->input('id_kategori', null);

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = Berita::with('kategori');

        if ($idKategori) {
            $query->where('id_kategori', $idKategori);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('waktu', $sort);
        } elseif ($sort === 'latest') {
            $query->orderBy('waktu_publish', 'desc');
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $berita = $query->paginate($perPage);

        return response()->json([
            'current_page' => $berita->currentPage(),
            'per_page' => $berita->perPage(),
            'total' => $berita->total(),
            'last_page' => $berita->lastPage(),
            'next_page_url' => $berita->nextPageUrl(),
            'prev_page_url' => $berita->previousPageUrl(),
            'data' => $berita->items(),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'path_media' => 'nullable|string|max:1000',
            'link' => 'nullable|url|max:1000',
            'filename' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'waktu' => 'required|date_format:Y-m-d H:i:s',
            'id_uploader' => 'required|exists:users,id',
            'id_kategori' => 'required|exists:tb_kategori,id_kategori',
            'publish' => 'nullable|boolean',
            'open' => 'nullable|boolean',
            'cover' => 'file|mimes:jpg,jpeg,png|max:2048',
            'keyword' => 'nullable|string|max:500',
            'editor' => 'nullable|boolean',
            'library' => 'nullable|boolean',
            'redaktur' => 'nullable|boolean',
            'waktu_publish' => 'nullable|date_format:Y-m-d H:i:s',
            'program_id' => 'nullable|exists:tb_program_acara,id_program',
            'type' => 'nullable|in:video,cetak,old',
        ]);

        $data = $request->except(['cover']);

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName(); 
            $filePath = config('app.tvku_storage.thumbnail_berita_path') . '/' . $filename;

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $data['cover'] = $filename; 
        }

        $berita = Berita::create($data);
        return response()->json($berita, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $berita = Berita::with('kategori')->find($id);

        if (!$berita) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        }

        if ($berita->cover) {
            $berita->cover_url = asset(config('app.tvku_storage.thumbnail_berita_path') . '/' . $berita->cover);
        }

        return response()->json($berita, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'path_media' => 'nullable|string|max:1000',
            'link' => 'nullable|url|max:1000',
            'filename' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'waktu' => 'nullable|date_format:Y-m-d H:i:s',
            'id_uploader' => 'nullable|exists:users,id',
            'id_kategori' => 'nullable|exists:tb_kategori,id_kategori',
            'publish' => 'nullable|boolean',
            'open' => 'nullable|boolean',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'keyword' => 'nullable|string|max:500',
            'editor' => 'nullable|boolean',
            'library' => 'nullable|boolean',
            'redaktur' => 'nullable|boolean',
            'waktu_publish' => 'nullable|date_format:Y-m-d H:i:s',
            'program_id' => 'nullable|exists:tb_program_acara,id_program',
            'type' => 'nullable|in:video,cetak,old',
        ]);

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $berita->cover = Berita::storeCover($file, $berita->cover);
        }

        $fields = [
            'judul', 'path_media', 'link', 'filename', 'deskripsi',
            'waktu', 'id_uploader', 'id_kategori', 'publish',
            'open', 'keyword', 'editor', 'library', 'redaktur',
            'waktu_publish', 'type'
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $berita->{$field} = $validated[$field];
            }
        }

        if (array_key_exists('program_id', $validated)) {
            $berita->program_id = $validated['program_id'];
        }

        try {
            $berita->save();

            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $berita,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $berita = Berita::find($id);
        if (!$berita) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        }

        if ($berita->cover) {
            Storage::disk('tvku_storage')->delete(config('app.tvku_storage.thumbnail_berita_path') . '/' . $berita->cover);
        }

        $berita->delete();
        return response()->json(['message' => 'Berita deleted successfully'], Response::HTTP_OK);
    }
}