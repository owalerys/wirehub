<?php

namespace App\Notifications;

use App\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserTeamInvite extends Notification
{
    use Queueable;

    private $token;
    private $team;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, Team $team)
    {
        $this->token = $token;
        $this->team = $team;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name'))
            ->line('You\'ve been invited to join the ' . $this->team->name . ' team.')
            ->line('Use the following link to activate your account within 24 hours.')
            ->action('Activate Account', url(config('app.url') . route('password.reset', $this->token, false)) . '?email=' . $notifiable->email)
            ->line('Thank you for using ' . config('app.name') . '.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
