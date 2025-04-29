<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSidebarBanner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $validated['gambar'];
            $filename = now()->format('d-m_Y') . '_' . $file->getClientOriginalName();
            $folderpath = 'sptdnsidebarbanner';

            if (!Storage::disk('tvku_storage')->exists($folderpath)) {
                Storage::disk('tvku_storage')->makeDirectory($folderpath);
            }
            $filePath = $folderpath . '/' .  $filename;
            Storage::disk('tvku_storage')->put($filePath, file_get_contents($file));

            try {
                SeputarDinusSidebarBanner::create([
                    'gambar' => Config('app.tvku_storage.base_url') . '/' . $filePath,
                ]);

                $latestBanner = SeputarDinusSidebarBanner::orderBy('id', 'desc')->first();

                return response()->json(['message' => 'Banner successfully created', 'data' => $latestBanner], Response::HTTP_CREATED);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Banner not created', 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }
        }
        return response()->json(['message' => 'File not found'], Response::HTTP_BAD_REQUEST);
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

         $request->validate([
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        try {
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = now()->format('d-m-Y') . '_' . $file->getClientOriginalName();
                $folderPath = 'SeputarDinusSideBarbanner';
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
                $banner->gambar = config('app.tvku_storage.base_url') . '/' . $filePath;
            }

            $banner->save();

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

    public function destroy(SeputarDinusSidebarBanner $banner)
    {
        if ($banner) {
            Storage::disk('public')->delete($banner->gambar);
            $banner->delete();
            return response()->json(['message' => 'Banner successfully deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Banner not deleted'], Response::HTTP_BAD_REQUEST);
        }
    }
}
