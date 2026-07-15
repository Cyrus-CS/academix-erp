<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceNotification extends Notification
{
    use Queueable;
    public Student $student;
    public string $date;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, string $date)
    {
        $this->student = $student;
        $this->date    = $date;
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
            'type'    => 'attendance',
            'title'   => "Absence de {$this->student->user->name}",
            'message' => "Votre enfant {$this->student->user->name} a été marqué absent le " .
                         \Carbon\Carbon::parse($this->date)->translatedFormat('l d F Y') . '.',
            'url'     => route('attendance.index', [
                'student_id' => $this->student->id,
                'date'       => $this->date,
            ]),
            'student_id' => $this->student->id,
            'date'       => $this->date,
        ];
    }
}