<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSidebarBanner;
use App\Models\SeputarDinusSidebarBannerTranslation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;

class SeputarDinusSidebarBannerController extends Controller
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

        $query = SeputarDinusSidebarBanner::query();

        if ($search) {
            $query->where('gambar', 'like', '%' . $search . '%');
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('gambar', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $banners = $query->paginate($perPage);

        return response()->json([
            'current_page' => $banners->currentPage(),
            'per_page' => $banners->perPage(),
            'total' => $banners->total(),
            'last_page' => $banners->lastPage(),
            'next_page_url' => $banners->nextPageUrl(),
            'prev_page_url' => $banners->previousPageUrl(),
            'data' => $banners->items(),
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

            $query = SeputarDinusSidebarBannerTranslation::with('translation');

            if ($search) {
                $query->where('gambar', 'like', '%' . $search . '%');
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

    public function getOnlyTranslationData($id_banner)
    {
        try {
            $banner = SeputarDinusSidebarBanner::with('translations.translation')->find($id_banner);

            if (!$banner) {
                return response()->json(['message' => 'Banner not found'], Response::HTTP_NOT_FOUND);
            }

            $translations = $banner->translations;

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
            $request->validate([
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = $request->except('gambar');

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'sptdinus/sidebarbanner';

                if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                    Storage::disk('tvku_storage')->makeDirectory($folderPath);
                }
                $filePath = $folderPath . '/' . $filename;
                Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

                $data['gambar'] = $filePath;
            }
            $banner = SeputarDinusSidebarBanner::create($data);

            return response()->json([
                'message' => 'Banner successfully created',
                'data' => $banner,
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
        $banner = SeputarDinusSidebarBanner::find($id);

        if ($banner) {
            return response()->json(['data' => $banner], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Banner not found'], Response::HTTP_NOT_FOUND);
        }
    }


    public function update(Request $request, $id)
    {
        $banner = SeputarDinusSidebarBanner::find($id);
        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
            $folderPath = 'sptdinus/sidebarbanner';
            $filePath = $folderPath . '/' . $filename;

            if (!Storage::disk('tvku_storage')->exists($folderPath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderPath);
            }

            if ($banner->gambar) {
                $oldPath = str_replace(config('app.tvku_storage.base_url') . '/', '', $banner->gambar);
                if (Storage::disk('tvku_storage')->exists($oldPath)) {
                    Storage::disk('tvku_storage')->delete($oldPath);
                }
            }
            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));
            $banner->gambar = $filePath;
        }

        try {
            $banner->save();
            $banner->gambar_url = config('app.tvku_storage.base_url') . '/' . $banner->gambar;

            return response()->json([
                'message' => 'Banner updated successfully!',
                'data' => $banner,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            if (isset($filePath) && Storage::disk('tvku_storage')->exists($filePath)) {
                Storage::disk('tvku_storage')->delete($filePath);
            }

            return response()->json([
                'message' => 'Failed to update banner',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $banner = SeputarDinusSidebarBanner::findOrFail($id);
            if ($banner->gambar && Storage::disk('tvku_storage')->exists($banner->gambar)) {
                Storage::disk('tvku_storage')->delete($banner->gambar);
            }
            $banner->delete();

            return response()->json(['message' => 'Banner deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
