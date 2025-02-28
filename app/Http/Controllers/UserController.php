<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class UserController extends Controller
{
    /**
     * Atualiza os dados do usuário.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email']));

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

        return response()->json(['message' => 'Usuário atualizado com sucesso!']);
    }
}
