<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Ticket $ticket,
        public Comment $comment) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $commenterName = $this->comment->user?->name ?? 'A user';
        $ticketCode = $this->ticket->reference_code;
        $ticketUrl = route('tickets.show', $this->ticket);

        return (new MailMessage)
            ->subject("[{$ticketCode}] New reply by {$commenterName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("**{$commenterName}** commented on ticket **{$this->ticket->title}** ({$ticketCode})");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
