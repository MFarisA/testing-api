<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSidebarBanner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SeputarDinusSidebarBannerController extends Controller
{
    public function index()
    {
        return response()->json(SeputarDinusSidebarBanner::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('banners', 'public');
            $banner = SeputarDinusSidebarBanner::create(['gambar' => $path]);

            return response()->json(['message' => 'Banner succesfully created','data' => $banner], Response::HTTP_CREATED);
        }
        else {
            return response()->json(['message' => 'Banner not created', Response::HTTP_BAD_REQUEST]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SeputarDinusSidebarBanner $banner)
    {
        return response()->json($banner, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeputarDinusSidebarBanner $banner)
    {
        $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            Storage::disk(('public'))->delete($banner->gambar);
            $path = $request->file('gambar')->store('banners', 'public');
            $banner->update(['gambar' => $path]);
            
            return response()->json(['message' => 'Banner succesfully updated', 'data' => $banner], Response::HTTP_OK);
        }
        else 
        {
            return response()->json(['message' => 'Banner not updated'], Response::HTTP_BAD_REQUEST);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
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
