<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TravelRequest;

class TravelRequestStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public TravelRequest $travelRequest)
    {
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Atualização do pedido de viagem')
                    ->line("Seu pedido para {$this->travelRequest->destination} foi atualizado para: {$this->travelRequest->status}.")
                    ->action('Ver pedido', url('/'));
    }

    public function toArray($notifiable)
    {
        return [
            'travel_request_id' => $this->travelRequest->id,
            'status' => $this->travelRequest->status,
            'destination' => $this->travelRequest->destination,
        ];
    }
}
