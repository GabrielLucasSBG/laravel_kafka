<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class UserUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public bool $fromKafka;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, bool $fromKafka = false)
    {
        $this->user = $user;
        $this->fromKafka = $fromKafka;

        if (!$fromKafka) {
            $message = new Message(
                headers: ['event' => 'user_updated'],
                body: [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]
            );

            Kafka::publish()
                ->onTopic('user_updated')
                ->withMessage($message)
                ->send();
        }
    }
}
