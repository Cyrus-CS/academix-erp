<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Announcement $announcement,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'announcement',
            'title'   => $this->announcement->title,
            'message' => \Illuminate\Support\Str::limit(
                strip_tags($this->announcement->message), 100
            ),
            'url'     => route('announcements.show', $this->announcement),
        ];
    }
}