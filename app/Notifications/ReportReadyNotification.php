<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReadyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $reportName,
        public User $user,
    ) {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'actor' => $this->user->first_name . ' ' . $this->user->last_name,
            'target_type' => 'reports.ready',
            'target_id' => $this->reportName,
        ];
    }

    // --- Static Methods for View Logic ---

    public static function getNotificationTitle(DataBaseNotification $notification)
    {
        return __('notifications.report_ready');
    }

    public static function getNotificationBody(DataBaseNotification $notification)
    {
        $data = $notification->data ?? [];
        $attributes = [
            'target_id' => $data['target_id'] ?? "",
            'actor' => $data['actor'] ?? "",
        ];

        return __('notifications.report_ready_description', $attributes);
    }

    public static function getNotificationActionUrl(DataBaseNotification $notification)
    {
        $data = $notification->data ?? [];
        return route('dashboard.reports.download', ['filename' => $data['target_id'] ?? '']);
    }

    public static function getNotificationLevel(DataBaseNotification $notification)
    {
        return 'success';
    }

    public static function getNotificationIcon(DataBaseNotification $notification)
    {
        return 'file-text';
    }
}
