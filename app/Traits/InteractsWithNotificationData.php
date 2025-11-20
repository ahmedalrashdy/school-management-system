<?php

namespace App\Traits;

use Illuminate\Notifications\DatabaseNotification;

trait InteractsWithNotificationData
{

    public function prepareNotificationData(DatabaseNotification $notification): array
    {
        $notificationClass = $notification->type;
        $data = $notification->data;
        $extras = $data['extra'] ?? [];

        return [
            'id' => $notification->id,
            'title' => $this->_resolveValue($notificationClass, 'getNotificationTitle', $notification, 'verb'),
            'body' => $this->_resolveValue($notificationClass, 'getNotificationBody', $notification, 'description'),
            'icon' => $this->_resolveValue($notificationClass, 'getNotificationIcon', $notification),
            'level' => $this->_resolveValue($notificationClass, 'getNotificationLevel', $notification) ?? 'info',
            'read_at' => $notification->read_at,
            'is_read' => $notification->read_at != null,
            'created_at' => $notification->created_at?->diffForHumans(),
            'raw_data' => [...$data ?? [], ...$extras],
        ];
    }

    public function getActionUrl(DatabaseNotification $notification): string
    {
        $notificationClass = $notification->type;
        return $this->_resolveValue($notificationClass, 'getNotificationActionUrl', $notification) ?? url('/#');
    }
    private function _resolveValue(string $class, string $methodOrPropertyName, DatabaseNotification $notification, ?string $fallbackKey = null)
    {
        $data = $notification->data ?? [];
        if (class_exists($class) && method_exists($class, $methodOrPropertyName)) {
            return $class::$methodOrPropertyName($notification);
        }
        if (class_exists($class) && property_exists($class, $methodOrPropertyName)) {
            return $class::$methodOrPropertyName;
        }

        if ($fallbackKey && isset($data[$fallbackKey])) {
            return $this->resolveTranslation($data[$fallbackKey], $data);
        }

        return null;
    }

    protected function resolveTranslation(?string $key, array $data): string
    {
        if (!$key)
            return '';
        return __($key, $data);
    }
}
