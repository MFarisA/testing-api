<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationToken;
use App\Models\NotificationCategory;

class NotificationController extends Controller
{
    private $key = 'iniadalah_rahasiaNotifikasi_tvku';

    /**
    * Store the notification token and preferences.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'token' => 'required|string',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:notification_categories,id',
        ]);

        if ($request->key !== $this->key) {
            return response()->json(['message' => 'Invalid key'], 403);
        }
        
        $token = NotificationToken::firstOrCreate(['token' => $request->token]);
        if (empty($request->category_ids)) {
            $token->categories()->detach();
            return response()->json([
                'message' => 'All notifications disabled',
                'categories' => [],
            ], 200);
        }
        $token->categories()->sync($request->category_ids);
        return response()->json([
            'message' => 'Preferences updated',
            'categories' => $token->categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        }),
        ], 200);
    }

    /**
    * Get the notification preferences for a specific token.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function getPreferences(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);
        
        $token = NotificationToken::where('token', $request->token)->first();
        $activeCategoryIds = $token ? $token->categories->pluck('id')->toArray() : [];
        $allCategories = NotificationCategory::all()->map(function ($category)
            use ($activeCategoryIds) {
                return [
                'id' => $category->id,
                'name' => $category->name,
                'active' => in_array($category->id, $activeCategoryIds),
                ];
        });
        return response()->json([
            'categories' => $allCategories,
        ]);
    }
}