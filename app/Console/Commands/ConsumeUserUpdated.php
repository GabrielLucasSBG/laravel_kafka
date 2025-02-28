<?php

namespace App\Console\Commands;

use App\Events\UserUpdated;
use Illuminate\Console\Command;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Facades\Kafka;

class ConsumeUserUpdated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume-user-updated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consome mensagens do Kafka sobre atualizações de usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $consumer = Kafka::consumer(['user_updated'], env('KAFKA_CONSUMER_GROUP_ID', 'default-consumer-group'))
            ->withBrokers(env('KAFKA_BROKERS', 'localhost:9092'))
            ->withHandler(function (ConsumerMessage $message) {
                $body = $message->getBody();
                $userId = $body['user_id'] ?? null;

                if ($userId) {
                    $user = \App\Models\User::find($userId);

                    if ($user) {
                        event(new UserUpdated($user, true));
                    }
                }
            })
            ->build();

        try {
            $consumer->consume();
        } catch (\Exception $e) {
            $this->error('Erro ao consumir mensagens do Kafka: ' . $e->getMessage());
        }
    }
}
