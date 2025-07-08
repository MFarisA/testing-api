<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSlider;
use App\Models\SeputarDinusSliderTranslation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Log;

class SeputarDinusSliderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');
        $idSlidesTitle = $request->input('id_slides_title', null);

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = SeputarDinusSlider::query();

        if ($idSlidesTitle) {
            $query->where('id_slides_title', $idSlidesTitle);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('teks', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('teks', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

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

    public function indexTranslations(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $currentPage = $request->input('current_page', 1);
            $search = $request->input('search', null);
            $sort = $request->input('sort', 'id_desc');
            $languageCode = $request->input('language_code', null);

            $query = SeputarDinusSliderTranslation::with('translation');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('teks', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            if ($languageCode) {
                $query->whereHas('translation', function ($q) use ($languageCode) {
                    $q->where('code', $languageCode);
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


    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_slides_title' => 'required|integer',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'thumbnail_hover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'teks' => 'nullable|string|max:255',
                'link' => 'nullable|string',
                'deskripsi' => 'required|string',
            ]);

            $allLangs = Translation::all();
            foreach ($allLangs as $lang) {
                $request->validate([
                    'teks' . $lang->code => 'nullable|string|max:255',
                    'deskripsi' . $lang->code => 'nullable|string',
                ]);
            }
            $data = $request->except(['thumbnail', 'thumbnail_hover']);

            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'sptdinus/slider';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['thumbnail'] = $filePath;
            }
            if ($request->hasFile('thumbnail_hover')) {
                $file = $request->file('thumbnail_hover');
                $filename = now()->format('d-m-Y') . '_hover_' . $file->getClientOriginalName();
                $folderPath = 'sptdinus/slider';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }

                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
                $data['thumbnail_hover'] = $filePath;
            }
            $seputarDinusSlider = SeputarDinusSlider::create($data);
            foreach ($allLangs as $lang) {
                $teksKey = 'teks' . $lang->code;
                $deskripsiKey = 'deskripsi' . $lang->code;

                SeputarDinusSliderTranslation::create([
                    'id_slides_title' => $seputarDinusSlider->id_slides_title,
                    'spt_dinus_slider_id' => $seputarDinusSlider->id,
                    'translation_id' => $lang->id,
                    'teks' => $request->input($teksKey, $data['teks']),
                    'deskripsi' => $request->input($deskripsiKey, $data['deskripsi']),
                ]);
            }

            try {
                $categoryId = 2;
                $title = "Update Seputar Dinus";
                $body = $data['teks'] ?? $data['deskripsi'] ?? 'Konten baru tersedia';
                $newsId = (string)$seputarDinusSlider->id;

                NotificationHelper::sendToCategory($categoryId, $title, $body, $newsId);
                
                Log::info("Firebase notification sent for Seputar Dinus Slider ID: {$seputarDinusSlider->id}, Category: {$categoryId}");
            } catch (\Exception $notificationError) {
                Log::error("Failed to send Firebase notification for Seputar Dinus Slider ID: {$seputarDinusSlider->id}. Error: " . $notificationError->getMessage());
            }

            return response()->json([
                'message' => 'Seputar Dinus Slider successfully created',
                'data' => $seputarDinusSlider->load('translations.translation'),
                'notification_sent' => true,
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
        $seputarDinusSlider = SeputarDinusSlider::find($id);
        if ($seputarDinusSlider) {
            return response()->json($seputarDinusSlider, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    public function update(Request $request, string $id)
    {
        $seputarDinusSlider = SeputarDinusSlider::find($id);
        if (!$seputarDinusSlider) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'id_slides_title' => 'required|integer',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_hover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'teks' => 'nullable|string|max:255',
            'link' => 'nullable|string',
            'deskripsi' => 'required|string',
        ]);

        $allLangs = Translation::all();
        foreach ($allLangs as $lang) {
            $request->validate([
                'teks' . $lang->code => 'nullable|string|max:255',
                'deskripsi' . $lang->code => 'required|string',
            ]);
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'sptdinus/slider';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($seputarDinusSlider->thumbnail) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $seputarDinusSlider->thumbnail);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $seputarDinusSlider->thumbnail = $filePath;
        }

        if ($request->hasFile('thumbnail_hover')) {
            $file = $request->file('thumbnail_hover');
            $filename = now()->format('d-m-Y') . '_hover_' . $file->getClientOriginalName();
            $folderPath = 'sptdinus/slider';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($seputarDinusSlider->thumbnail_hover) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $seputarDinusSlider->thumbnail_hover);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }

            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $seputarDinusSlider->thumbnail_hover = $filePath;
        }
        $fields = ['id_slides_title', 'teks', 'link', 'deskripsi'];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $seputarDinusSlider->{$field} = $validated[$field];
            }
        }

        try {
            $seputarDinusSlider->update();
            // $seputarDinusSlider->thumbnail_url = config('app.tvku_storage.base_url') . '/' . $seputarDinusSlider->thumbnail;
            // $seputarDinusSlider->thumbnail_hover_url = config('app.tvku_storage.base_url') . '/' . $seputarDinusSlider->thumbnail_hover;
            foreach ($allLangs as $lang) {
                $teksKey = 'teks' . $lang->code;
                $deskripsiKey = 'deskripsi' . $lang->code;

                $translation = SeputarDinusSliderTranslation::where('spt_dinus_slider_id', $seputarDinusSlider->id)
                    ->where('translation_id', $lang->id)
                    ->first();

                if ($translation) {
                    $translation->update([
                        'teks' => $request->input($teksKey, $seputarDinusSlider->teks),
                        'deskripsi' => $request->input($deskripsiKey, $seputarDinusSlider->deskripsi),
                    ]);
                } else {
                    SeputarDinusSliderTranslation::create([
                        'id_slides_title' => $seputarDinusSlider->id_slides_title,
                        'spt_dinus_slider_id' => $seputarDinusSlider->id,
                        'translation_id' => $lang->id,
                        'teks' => $request->input($teksKey, $seputarDinusSlider->teks),
                        'deskripsi' => $request->input($deskripsiKey, $seputarDinusSlider->deskripsi),
                    ]);
                }
            }
            $seputarDinusSlider->load('translations.translation');
            return response()->json([
                'message' => 'Updated successfully!',
                'data' => $seputarDinusSlider,
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
        try {
            $seputarDinusSlider = SeputarDinusSlider::findOrFail($id);

            if ($seputarDinusSlider->thumbnail && Storage::disk('tvku_storage')->exists($seputarDinusSlider->thumbnail)) {
                Storage::disk('tvku_storage')->delete($seputarDinusSlider->thumbnail);
            }
            if ($seputarDinusSlider->thumbnail_hover && Storage::disk('tvku_storage')->exists($seputarDinusSlider->thumbnail_hover)) {
                Storage::disk('tvku_storage')->delete($seputarDinusSlider->thumbnail_hover);
            }
            $seputarDinusSlider->delete();

            return response()->json(['message' => 'Seputar Dinus Slider deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyTranslation($id)
    {
        $translation = SeputarDinusSliderTranslation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        $translation->delete();

        return response()->json(['message' => 'Translation deleted successfully'], 200);
    }
}
