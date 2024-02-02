<?php

namespace App\Services;

use Exception;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class NotifyService
{
    public function pushNotify($deviceTokens, $title, $content, $data = null)
    {
        try {
            if (!is_array($data)) {
                $data = ['description' => ''];
            }

            $notification = Notification::create($title, $content);
            $messaging = app('firebase.messaging');
            $message = CloudMessage::new()
                ->withData($data)
                ->withNotification($notification);

            $result = $messaging->sendMulticast($message, $deviceTokens);

            foreach ($result->getItems() as $value) {
                if ($value->error() !== null) {
                    Log::warning(__METHOD__ . ': FAIL. Message : ' . $title . ' - ' . $content . ' - Reason: ' . $value->error()->errors()['error']['message']);
                } else {
                    Log::info(__METHOD__ . ': SUCCESS. Message : ' . $title . ' - ' . $content);
                }
            }
        } catch (Exception $e) {
            Log::error(__METHOD__ . ': System error - ' . $e->getMessage());
        }

    }
}
