<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);

        $message = new Message(
            headers: ['event' => 'user_registered'],
            body: [
                'email' => $user->email,
                'name' => $user->name,
            ]
        );

        Kafka::publish()
            ->onTopic('user_registered')
            ->withMessage($message)
            ->send();

        return $user;
    }

    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        return Auth::user()->createToken('auth_token')->plainTextToken;
    }
}
