<?php

namespace App\Support;

use App\Models\Tenant\User;
use Illuminate\Notifications\Notification;

class NotificationHelper
{
    public static function send(User $user, string $title, string $message, string $type = 'info', ?string $url = null): void
    {
        try {
            $user->notify(new class($title, $message, $type, $url) extends Notification
            {
                public function __construct(
                    private string $title,
                    private string $message,
                    private string $type,
                    private ?string $url
                ) {}

                public function via($notifiable): array
                {
                    return ['database'];
                }

                public function toDatabase($notifiable): array
                {
                    return [
                        'title' => $this->title,
                        'message' => $this->message,
                        'type' => $this->type,
                        'url' => $this->url,
                    ];
                }
            });
        } catch (\Exception $e) {
            // Silently fail — notifications table might not exist
        }
    }

    public static function sendToRole(string $role, string $title, string $message, string $type = 'info', ?string $url = null): void
    {
        $users = User::role($role)->get();
        foreach ($users as $user) {
            self::send($user, $title, $message, $type, $url);
        }
    }

    public static function sendToAllStaff(string $title, string $message, string $type = 'info', ?string $url = null): void
    {
        $roles = ['owner', 'admin', 'manager', 'chef', 'waiter', 'cashier'];
        foreach ($roles as $role) {
            self::sendToRole($role, $title, $message, $type, $url);
        }
    }
}
