<?php

namespace App\Notifications;

use App\Contracts\Transaction as ContractsTransaction;
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
    public function __construct(ContractsTransaction $transaction)
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
        $pending = $this->transaction->getPending();
        $currency = $this->transaction->getCurrencyCode();
        $amount = $this->transaction->getAmount();
        $mask = $this->transaction->account->getMask();
        $description = $this->transaction->getName();
        $date = $this->transaction->getDate();

        $parentResourceIdentifier = $this->transaction->getParentResourceIdentifier();

        $message = new MailMessage;

        $message->subject('A new transaction arrived.');

        $message->line("Account: ***$mask");
        $message->line("Currency: $currency");
        $message->line("Amount: $amount");

        if ($pending !== null) $message->line("Pending: " . $pending ? 'YES' : 'NO');

        $message->line("Description: $description");
        $message->line("Date: $date");

        $message->action('View in Account', url(config('app.url') . "/app/accounts/$parentResourceIdentifier"));

        return $message;
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
