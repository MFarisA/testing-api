<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use App\Models\Program;
use App\Models\Translation;
use App\Models\BeritaTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Log;

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

        $query = Berita::with(['kategori', 'program']);

        if ($idKategori) {
            $query->where('id_kategori', $idKategori);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'populer') {
            $query->where('publish', 1)
                  ->whereBetween('waktu', [now()->subDays(14), now()])
                  ->orderByDesc('open');
        }elseif ($sort === 'latest') {
            $query->where('publish', 1) 
                  ->orderBy('waktu_publish', 'desc');
        } elseif ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('judul', $sort);
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

    public function indexTranslations(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $currentPage = $request->input('current_page', 1);
            $search = $request->input('search', null);
            $sort = $request->input('sort', 'id_desc');
            $languageCode = $request->input('language_code', null);
            $idKategori = $request->input('id_kategori', null);

            $query = BeritaTranslation::with([
                'translation',
                'berita.kategori.translations.translation',
                'berita.program'
            ]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            if ($languageCode) {
                $query->whereHas('translation', function ($q) use ($languageCode) {
                    $q->where('code', $languageCode);
                });
            
                // $query->whereHas('berita.kategori.translations.translation', function ($q) use ($languageCode) {
                //     $q->where('code', $languageCode);
                // });
            }

            if ($idKategori) {
                $query->whereHas('berita', function ($q) use ($idKategori) {
                    $q->where('id_kategori', $idKategori);
                });
            }

            if ($sort === 'id_asc') {
                $query->orderBy('id', 'asc');
            } elseif ($sort === 'id_desc') {
                $query->orderBy('id', 'desc');
            }

            $translations = $query->paginate($perPage, ['*'], 'page', $currentPage);

            return response()->json([
                'current_page' => $translations->currentPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
                'last_page' => $translations->lastPage(),
                'next_page_url' => $translations->nextPageUrl(),
                'prev_page_url' => $translations->previousPageUrl(),
                'data' => $translations->items(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getOnlyTranslationData($id_berita)
    {
        try {
            $berita = Berita::with('translations.translation')->find($id_berita);

            if (!$berita) {
                return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
            }

            $translations = $berita->translations;

            return response()->json([
                'message' => 'Translations retrieved successfully',
                'data' => $translations,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve translations',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'link' => 'nullable|url|max:1000',
                'deskripsi' => 'required|string',
                'id_kategori' => 'required|exists:tb_kategori,id_kategori',
                'publish' => 'nullable|boolean',
                'cover' => 'file|mimes:jpg,jpeg,png|max:2048',
                'keyword' => 'nullable|string|max:500',
                'program_id' => 'nullable|exists:program,id',
            ]);

            $allLangs = Translation::all();

            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'deskripsi_' . $lang->code => 'nullable|string',
                    'keyword_' . $lang->code => 'nullable|string|max:500',
                    'link_' . $lang->code => 'nullable|url|max:1000',
                    'cover_' . $lang->code => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                ]);
            }

            $data = $request->except(['cover', 'path_media', 'id_uploader', 'waktu', 'waktu_publish', 'editor', 'library', 'redaktur']);

            $data['editor'] = 1;
            $data['library'] = 1;
            $data['redaktur'] = 1;
            $data['id_uploader'] = Auth::check() ? Auth::id() : null;
            $data['waktu'] = now()->format('Y-m-d H:i:s');
            $data['waktu_publish'] = isset($validated['publish']) && $validated['publish'] == 1
                ? now()->format('Y-m-d H:i:s')
                : '1000-01-01 00:00:00';

            if ($request->filled('program_id')) {
                $program = Program::find($request->program_id);
                if ($program) {
                    $data['path_media'] = $program->path;
                }
            }

            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $data['cover'] = Berita::storeCover($file, null, $data['path_media'] ?? null);
            }

            $berita = Berita::create($data);

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $deskripsiKey = 'deskripsi_' . $lang->code;
                $keywordKey = 'keyword_' . $lang->code;
                $linkKey = 'link_' . $lang->code;
                $coverKey = 'cover_' . $lang->code;

                $hasTranslationData = $request->filled($judulKey) || 
                                    $request->filled($deskripsiKey) || 
                                    $request->filled($keywordKey) || 
                                    $request->filled($linkKey) ||
                                    $request->hasFile($coverKey);

                if (!$hasTranslationData) {
                    continue;
                }

                $coverPath = null;
                if ($request->hasFile($coverKey)) {
                    $file = $request->file($coverKey);
                    $coverPath = Berita::storeCover($file, null, $data['path_media'] ?? null);
                }

                BeritaTranslation::create([
                    'berita_id' => $berita->id,
                    'translation_id' => $lang->id,
                    'judul' => $request->input($judulKey) ?? $data['judul'] ?? null,
                    'deskripsi' => $request->input($deskripsiKey) ?? $data['deskripsi'] ?? null,
                    'keyword' => $request->input($keywordKey) ?? $data['keyword'] ?? null,
                    'link' => $request->input($linkKey) ?? $data['link'] ?? null,
                    'cover' => $coverPath ?? $data['cover'] ?? null,
                    'path_media' => $data['path_media'] ?? null,
                ]);
            }

            if (isset($validated['publish']) && $validated['publish'] == 1) {
                try {
                    $categoryId = 1;
                    $title = "Berita Terbaru";
                    $body = $data['judul'];
                    $newsId = (string)$berita->id;

                    NotificationHelper::sendToCategory($categoryId, $title, $body, $newsId);
                    
                    Log::info("Firebase notification sent for berita ID: {$berita->id}, Category: {$categoryId}");
                } catch (\Exception $notificationError) {
                    Log::error("Failed to send Firebase notification for berita ID: {$berita->id}. Error: " . $notificationError->getMessage());
                }
            }

            return response()->json([
                'message' => 'Berita created successfully',
                'data' => $berita->load('translations.translation'),
                'notification_sent' => isset($validated['publish']) && $validated['publish'] == 1,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Berita',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $berita = Berita::with(['kategori', 'translations.translation'])
            ->where('publish', 1)
            ->find($id);
    
        if (!$berita) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        }
    
        $berita->increment('open');
    
        return response()->json($berita, Response::HTTP_OK);
    }

    public function showAdmin($id)
    {
        $berita = Berita::with(['kategori', 'translations.translation'])->find($id);
    
        if (!$berita) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        }
    
        return response()->json($berita, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        try {
            $berita = Berita::find($id);
            if (!$berita) {
                return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'judul' => 'nullable|string|max:255',
                'link' => 'nullable|url|max:1000',
                'deskripsi' => 'nullable|string',
                'id_uploader' => 'nullable|exists:users,id',
                'id_kategori' => 'nullable|exists:tb_kategori,id_kategori',
                'publish' => 'nullable|boolean',
                'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'keyword' => 'nullable|string|max:500',
                'program_id' => 'nullable|exists:program,id',
            ]);

            $allLangs = Translation::all();
            foreach ($allLangs as $lang) {
                $request->validate([
                    'judul_' . $lang->code => 'nullable|string|max:255',
                    'deskripsi_' . $lang->code => 'nullable|string',
                    'keyword_' . $lang->code => 'nullable|string|max:500',
                    'link_' . $lang->code => 'nullable|url|max:1000',
                    'cover_' . $lang->code => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                ]);
            }

            if (Auth::check() && Auth::id() !== $berita->id_uploader) {
                $berita->id_uploader = Auth::id();
            }

            $oldCoverPath = null;

            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                if ($berita->cover) {
                    $oldCoverFileName = basename(parse_url($berita->getRawOriginal('cover'), PHP_URL_PATH));
                    $oldCoverPath = config('app.tvku_storage.thumbnail_berita_path') . '/' . trim($berita->path_media, '/') . '/' . $oldCoverFileName;
                }
                $berita->cover = Berita::storeCover($file, null, $berita->path_media);
            }

            if (array_key_exists('program_id', $validated)) {
                $berita->program_id = $validated['program_id'];

                $program = Program::find($validated['program_id']);
                if ($program) {
                    $newPathMedia = $program->path;

                    if ($berita->path_media !== $newPathMedia && $berita->cover) {
                        $oldCoverFileName = basename(parse_url($berita->getRawOriginal('cover'), PHP_URL_PATH));
                        $oldFilePath = config('app.tvku_storage.thumbnail_berita_path') . '/' . trim($berita->path_media, '/') . '/' . $oldCoverFileName;
                        $newFilePath = config('app.tvku_storage.thumbnail_berita_path') . '/' . trim($newPathMedia, '/') . '/' . $oldCoverFileName;

                        $newFolderPath = config('app.tvku_storage.thumbnail_berita_path') . '/' . trim($newPathMedia, '/');
                        if (!Storage::disk('tvku_storage')->exists($newFolderPath)) {
                            Storage::disk('tvku_storage')->makeDirectory($newFolderPath);
                        }

                        if (Storage::disk('tvku_storage')->exists($oldFilePath)) {
                            Storage::disk('tvku_storage')->move($oldFilePath, $newFilePath);
                        } else {
                            return response()->json([
                                'message' => 'Old file not found in the expected location.',
                                'old_path' => $oldFilePath,
                            ], Response::HTTP_BAD_REQUEST);
                        }
                    }

                    $berita->path_media = $newPathMedia;
                }
            }

            $berita->waktu_publish = isset($validated['publish']) && $validated['publish'] == 1
                ? now()->format('Y-m-d H:i:s')
                : '1000-01-01 00:00:00';

            $validated['editor'] = $validated['editor'] ?? 1;
            $validated['library'] = $validated['library'] ?? 1;
            $validated['redaktur'] = $validated['redaktur'] ?? 1;

            $fields = [
                'judul', 'link', 'filename', 'deskripsi',
                'id_kategori', 'publish', 'open', 'keyword',
                'editor', 'library', 'redaktur', 'type'
            ];

            foreach ($fields as $field) {
                if (array_key_exists($field, $validated)) {
                    $berita->{$field} = $validated[$field];
                }
            }

            $berita->waktu = now()->format('Y-m-d H:i:s');

            $berita->save();

            if ($oldCoverPath && Storage::disk('tvku_storage')->exists($oldCoverPath)) {
                Storage::disk('tvku_storage')->delete($oldCoverPath);
            }

            foreach ($allLangs as $lang) {
                $judulKey = 'judul_' . $lang->code;
                $deskripsiKey = 'deskripsi_' . $lang->code;
                $keywordKey = 'keyword_' . $lang->code;
                $linkKey = 'link_' . $lang->code;
                $coverKey = 'cover_' . $lang->code;

                $hasTranslationData = $request->filled($judulKey) || 
                                    $request->filled($deskripsiKey) || 
                                    $request->filled($keywordKey) || 
                                    $request->filled($linkKey) ||
                                    $request->hasFile($coverKey);

                $translation = BeritaTranslation::where('berita_id', $berita->id)
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $updateData = [];
                    
                    if ($request->filled($judulKey)) {
                        $updateData['judul'] = $request->input($judulKey);
                    }
                    
                    if ($request->filled($deskripsiKey)) {
                        $updateData['deskripsi'] = $request->input($deskripsiKey);
                    }
                    
                    if ($request->filled($keywordKey)) {
                        $updateData['keyword'] = $request->input($keywordKey);
                    }
                    
                    if ($request->filled($linkKey)) {
                        $updateData['link'] = $request->input($linkKey);
                    }

                    if ($request->hasFile($coverKey)) {
                        $file = $request->file($coverKey);
                        $updateData['cover'] = Berita::storeCover($file, null, $berita->path_media);
                        
                        if ($translation->getRawOriginal('cover')) {
                            $oldTranslationCoverPath = config('app.tvku_storage.thumbnail_berita_path') . '/' . trim($berita->path_media, '/') . '/' . $translation->getRawOriginal('cover');
                            if (Storage::disk('tvku_storage')->exists($oldTranslationCoverPath)) {
                                Storage::disk('tvku_storage')->delete($oldTranslationCoverPath);
                            }
                        }
                    }

                    $updateData['path_media'] = $berita->path_media;

                    if (!empty($updateData)) {
                        $translation->update($updateData);
                    }

                } else {
                    if ($hasTranslationData) {
                        $coverPath = null;
                        if ($request->hasFile($coverKey)) {
                            $file = $request->file($coverKey);
                            $coverPath = Berita::storeCover($file, null, $berita->path_media);
                        }

                        BeritaTranslation::create([
                            'berita_id' => $berita->id,
                            'translation_id' => $lang->id,
                            'judul' => $request->input($judulKey) ?? $berita->judul ?? null,
                            'deskripsi' => $request->input($deskripsiKey) ?? $berita->deskripsi ?? null,
                            'keyword' => $request->input($keywordKey) ?? $berita->keyword ?? null,
                            'link' => $request->input($linkKey) ?? $berita->link ?? null,
                            'cover' => $coverPath ?? $berita->getRawOriginal('cover') ?? null,
                            'path_media' => $berita->path_media,
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $berita->load('translations.translation'),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $berita = Berita::with('translations')->findOrFail($id);

            if ($berita->cover && Storage::disk('tvku_storage')->exists($berita->cover)) {
                Storage::disk('tvku_storage')->delete($berita->cover);
            }

            foreach ($berita->translations as $translation) {
                if ($translation->cover && Storage::disk('tvku_storage')->exists($translation->cover)) {
                    Storage::disk('tvku_storage')->delete($translation->cover);
                }
                $translation->delete();
            }

            $berita->delete();

            return response()->json(['message' => 'Berita deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Berita not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Berita',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyTranslation($id)
    {
        try {
            $translation = BeritaTranslation::findOrFail($id);

            if ($translation->cover && Storage::disk('tvku_storage')->exists($translation->cover)) {
                Storage::disk('tvku_storage')->delete($translation->cover);
            }

            $translation->delete();

            return response()->json(['message' => 'Translation deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Translation',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}