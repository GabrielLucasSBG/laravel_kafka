<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    /**
     * Criar uma nova instância de mensagem.
     *
     * @param string $name
     * @return void
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Bem-vindo ao nosso sistema!')
            ->view('emails.user_registered') // O arquivo de view
            ->with(['name' => $this->name]);  // Passando a variável para a view
    }
}
