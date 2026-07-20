<?php

namespace App\View\Composers;

use Illuminate\View\View;

class NotificationComposer
{
    public function compose(View $view): void
    {
        $unreadCount = 0;

        if (tenancy()->initialized && auth('tenant')->check()) {
            try {
                $unreadCount = auth('tenant')->user()->unreadNotifications()->count();
            } catch (\Exception $e) {
                $unreadCount = 0;
            }
        }

        $view->with('unreadNotifications', $unreadCount);
    }
}
