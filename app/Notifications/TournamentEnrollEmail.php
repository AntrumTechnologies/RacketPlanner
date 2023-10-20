<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TournamentEnrollEmail extends Notification
{
    use Queueable;

    private $name;
    private $email;
    private $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($array)
    {
        $this->name = $array['name'];
        $this->email = $array['email'];
        $this->url = $array['url'];
        $this->tournament_id = $array['tournament_id'];
        $this->tournament_name = $array['tournament_name'];
    }

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
        return (new MailMessage)
                    ->subject('You are invited to '. $this->tournament_name .'!')
                    ->greeting('Hi '. $this->name .'!')
                    ->line('You are invited to join a tournament!')
                    ->line('If you want to join '. $this->tournament_name .' then click the link below.')
                    ->action('Join', url($this->url))
                    ->line('This link is valid for 72 hours. Never forward this email to anyone else.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'url' => $this->url,
            'tournament_id' => $this->tournament_id,
            'tournament_name' => $this->tournament_name,
        ];
    }
}
