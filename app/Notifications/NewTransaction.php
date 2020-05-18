<?php

namespace App\Notifications;

use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTransaction extends Notification
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
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
        $accountId = $this->transaction->account_id;

        $pending = $this->transaction->pending ? 'YES' : 'NO';
        $currency = $this->transaction->iso_currency_code;
        $amount = $this->transaction->amount;
        $mask = $this->transaction->account->mask;
        $description = $this->transaction->name;
        $date = $this->transaction->date;

        return (new MailMessage)
                    ->subject('A new transaction arrived.')
                    ->line("Account: ***$mask")
                    ->line("Currency: $currency")
                    ->line("Amount: $amount")
                    ->line("Pending: $pending")
                    ->line("Description: $description")
                    ->line("Date: $date")
                    ->action('View in Account', url(config('app.url') . "/app/accounts/$accountId"));
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
