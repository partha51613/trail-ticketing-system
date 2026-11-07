<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket,
        public ?string $oldStatus = null
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
        $ticketCode = $this->ticket->reference_code;
        $ticketUrl = route('tickets.show', $this->ticket);
        $status = ucfirst(str_replace('_', ' ', $this->ticket->status));

        $mail = (new MailMessage)
            ->subject("[{$ticketCode}] Status updated to {$status}: {$this->ticket->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Ticket **{$this->ticket->title}** ({$ticketCode}) status has been updated to **{$status}**.");

        if ($this->oldStatus) {
            $oldStatus = ucfirst(str_replace('_', ' ', $this->oldStatus));
            $mail->line("**Previous Status:** {$oldStatus}");
        }

        return $mail
            ->action('View Ticket', $ticketUrl)
            ->line('Thank you for using our ticket system!');
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
            'status' => $this->ticket->status,
            'old_status' => $this->oldStatus,
        ];
    }
}
