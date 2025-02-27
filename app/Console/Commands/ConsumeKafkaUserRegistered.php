<?php

namespace App\Console\Commands;

use App\Consumers\UserRegisteredConsumer;
use Carbon\Exceptions\Exception;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;

class ConsumeKafkaUserRegistered extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume-kafka-user-registered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume user registered messages from Kafka';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $consumer2 = new UserRegisteredConsumer();

        $consumer = Kafka::consumer(['user_registered'], env('KAFKA_CONSUMER_GROUP_ID', 'default-consumer-group'))
            ->withBrokers(env('KAFKA_BROKERS', 'localhost:9092'))
            ->withHandler($consumer2)
            ->build();

        try {
            $consumer->consume();
        } catch (\Exception $e) {
            $this->error('Erro ao consumir mensagens do Kafka: ' . $e->getMessage());
        }
    }
}
