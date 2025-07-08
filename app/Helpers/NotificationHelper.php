<?php
namespace App\Helpers;
use App\Models\NotificationCategory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationHelper
{
    protected static function messaging()
    {
        static $messaging = null;
        if (!$messaging) {
            $factory = (new Factory)->withServiceAccount(storage_path('firebase/tvkumobile-firebase-adminsdk-fbsvc-7bc9bf2ec0.json'));
            $messaging = $factory->createMessaging();
        }
        return $messaging;
    }

    /**
    * Send a notification to a specific category.
    *
    * @param int $categoryId
    * @param string $title
    * @param string $body
    * @param string $newsId
    * @return void
    */
    public static function sendToCategory(int $categoryId, string $title, string $body, string $newsId)
    {
        $category = NotificationCategory::findOrFail($categoryId);
        $messaging = self::messaging();
        foreach ($category->tokens as $token) {
            $message = CloudMessage::new()
                ->toToken($token->token)
                ->withNotification(Notification::create($title, $body))
                ->withData([
                    'category_id' => (string)$categoryId,
                    'news_id' => $newsId,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]);
            try {
                $messaging->send($message);
            } catch (\Throwable $e) {
                // Optional: log error
                Log::error("FCM send error: " . $e->getMessage());
            }
        }
    }
}