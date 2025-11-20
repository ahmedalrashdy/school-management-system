<?php

namespace App\View\Components\Ui;

use App\Traits\InteractsWithNotificationData;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\Component;

class NotificationItem extends Component
{
    use InteractsWithNotificationData;
    public array $payload;

    /**
     * Create a new component instance.
     */
    public function __construct(public DatabaseNotification $notification)
    {
        $this->payload = $this->prepareNotificationData($notification);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.notification-item');
    }
}
