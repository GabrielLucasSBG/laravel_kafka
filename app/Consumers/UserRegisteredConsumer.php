<?php

namespace App\Consumers;

use App\Mail\UserRegisteredMail;
use Illuminate\Support\Facades\Mail;
use Junges\Kafka\Contracts\ConsumerMessage;

class UserRegisteredConsumer
{
    public function __invoke(ConsumerMessage $message): void
    {
        $data = $message->getBody();

        $email = $data['email'] ?? null;
        $name = $data['name'] ?? 'Usuário';

        if ($email) {
            Mail::to($email)->send(new UserRegisteredMail($name));
        }
    }
}
