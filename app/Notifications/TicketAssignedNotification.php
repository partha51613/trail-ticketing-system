<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket,
        public User|Authenticatable|null $assignedBy = null
    ) {}

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
        $byName = $this->assignedBy?->name ?? 'A team member';
        $ticketCode = $this->ticket->reference_code;
        $ticketUrl = route('tickets.show', $this->ticket);

        return (new MailMessage)
            ->subject("[{$ticketCode}] You have been assigned: {$this->ticket->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("**{$byName}** assigned ticket **{$this->ticket->title}** ({$ticketCode}) to you.")
            ->line('**Priority:** '.ucfirst($this->ticket->priority))
            ->action('View Ticket', $ticketUrl)
            ->line('Please review the ticket and take necessary action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'reference_code' => $this->ticket->reference_code,
            'title' => $this->ticket->title,
            'assigned_by' => $this->assignedBy?->name,
        ];
    }
}
