<?php

namespace App\Notifications;

use App\Contracts\Transaction as ContractsTransaction;
use App\Contracts\Account as ContractsAccount;
use Illuminate\Bus\Queueable;
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

        /** @var ContractsAccount $account */
        $account = $this->transaction->account;

        $accountName = $account->getNickname() ?: $account->getName();
        $hasWireMeta = $account->hasWireMeta();
        $hasAccountMeta = $account->hasAccountMeta();
        $wireMeta = $this->transaction->getWireMeta();

        $parentResourceIdentifier = $this->transaction->getParentResourceIdentifier();

        $message = new MailMessage;

        $message->subject('A new transaction arrived.');

        if ($hasWireMeta) {
            $message->line("Account: $accountName");
        }

        if ($hasAccountMeta) {
            $message->line("Account: ***$mask");
        }

        $message->line("Currency: $currency");
        $message->line("Amount: $amount");

        if ($pending !== null) $message->line("Pending: " . ($pending ? 'PENDING' : 'SETTLED'));

        if ($hasWireMeta) {
            $receiverReference = $wireMeta->receiver->reference_number;
            $senderReference = $wireMeta->sender->reference_number;
            $senderName = $wireMeta->sender->name;
            $senderAddress = $wireMeta->sender->address;

            $message->line("Receiver Reference: $receiverReference");
            $message->line("Sender Reference: $senderReference");
            $message->line("Sender Name: $senderName");
            $message->line("Sender Address: $senderAddress");
        }

        if ($hasAccountMeta) {
            $message->line("Description: $description");
        }

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
