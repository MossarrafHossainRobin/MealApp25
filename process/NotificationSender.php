<?php
class NotificationSender
{
    private $serverKey;
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
        // Get this from Firebase Console → Project Settings → Cloud Messaging → Server Key
        $this->serverKey = 'AAAAj5qR7B4:APA91bHkL8v8Y7vY6W6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFwY6Xq9ZQ3m1dFw';
    }

    public function sendToUser($userId, $title, $body, $data = [])
    {
        try {
            // Get user's FCM token
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT fcm_token FROM members WHERE id = ? AND fcm_token IS NOT NULL");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || empty($user['fcm_token'])) {
                return ['status' => 'error', 'message' => 'User token not found'];
            }

            return $this->sendPushNotification($user['fcm_token'], $title, $body, $data);
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function sendToAllUsers($title, $body, $data = [])
    {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->query("SELECT fcm_token FROM members WHERE fcm_token IS NOT NULL");
            $tokens = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($tokens)) {
                return ['status' => 'error', 'message' => 'No tokens found'];
            }

            $results = [];
            foreach ($tokens as $token) {
                $results[] = $this->sendPushNotification($token, $title, $body, $data);
            }

            return ['status' => 'success', 'message' => 'Notifications sent to ' . count($tokens) . ' users'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function sendPushNotification($token, $title, $body, $data = [])
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $title,
            'body' => $body,
            'icon' => '/assets/img/logo.png',
            'click_action' => 'https://mealapp25.unaux.com' // Your website URL
        ];

        $message = [
            'to' => $token,
            'notification' => $notification,
            'data' => array_merge($data, ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'])
        ];

        $headers = [
            'Authorization: key=' . $this->serverKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $response = json_decode($result, true);
            if ($response['success'] == 1) {
                return ['status' => 'success', 'message' => 'Notification sent'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to send: ' . $result];
            }
        } else {
            return ['status' => 'error', 'message' => 'HTTP Error: ' . $httpCode];
        }
    }
}
?>