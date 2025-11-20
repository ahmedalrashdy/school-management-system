<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Traits\InteractsWithNotificationData;
use Illuminate\Notifications\DatabaseNotification;
use Laravel\Telescope\AuthorizesRequests;

class NotificationController extends Controller
{

    use InteractsWithNotificationData, AuthorizesRequests;

    public function index()
    {
        $sidebar = auth()->user()->can(\Perm::AccessAdminPanel) ? 'layouts.dashboard' : 'layouts.portal';
        $notifications = auth()->user()->notifications()
            ->reorder('read_at')
            ->orderBy('created_at')
            ->paginate(15);
        return view('common.notifications-index', [
            'notifications' => $notifications,
            'sidebar' => $sidebar,
        ]);
    }
    public function markAsReadAndRedirect(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== auth()->user()->id) {
            return abort(403);
        }
        if ($notification->unread()) {
            $notification->markAsRead();
        }
        $actionUrl = $this->getActionUrl($notification);
        return redirect()->to($actionUrl);
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== auth()->user()->id) {
            return abort(403);
        }
        if ($notification->unread()) {
            $notification->markAsRead();
        }
        return back()->with('success', 'تم تعليم الإشعار كمقروء');
    }

    public function destroy(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== auth()->user()->id) {
            return abort(403);
        }
        $notification->delete();
        return back()->with('success', 'تم حذف الإشعار بنجاح');
    }
}
