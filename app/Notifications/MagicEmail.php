<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MagicEmail extends Notification
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
                    ->subject('Confirm it\'s you to login')
                    ->greeting('Hi '. $this->name .'!')
                    ->line('We need to confirm it\'s you. Click on the link below to login.')
                    ->action('Login', url($this->url))
                    ->line('This link is valid forever. Never forward this email to anyone else.');
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
        ];
    }
}
