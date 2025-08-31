<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $codigo;
    public $ttl;

    public function __construct($user, $codigo, $ttl = 60)
    {
        $this->user = $user;
        $this->codigo = $codigo;
        $this->ttl = $ttl;
    }

    public function build()
    {
        return $this->subject('🔐 Tu código de verificación')
                    ->markdown('emails.otp');
    }
}
