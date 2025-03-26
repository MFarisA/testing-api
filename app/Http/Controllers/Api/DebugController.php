<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DebugController
{
    public function debugAppConfig(): JsonResponse
    {
        $baseUrl = config('app.tvku_storage.base_url');
        $thumbnailPath = config('app.tvku_storage.thumbnail_berita_path');

        Log::info('Base URL: ' . $baseUrl);
        Log::info('Thumbnail Path: ' . $thumbnailPath);

        return response()->json([
            'base_url' => $baseUrl,
            'thumbnail_berita_path' => $thumbnailPath,
        ]);
    }
    
    public function debugTvkuStorageConfig(): JsonResponse
    {
        try {
            $diskRoot = config('filesystems.disks.tvku_storage.root');
            $diskUrl = config('filesystems.disks.tvku_storage.url');

            Log::info('Disk Root: ' . $diskRoot);
            Log::info('Disk URL: ' . $diskUrl);

            return response()->json([
                'disk_root' => $diskRoot,
                'disk_url' => $diskUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}