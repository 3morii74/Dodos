<?php

namespace App\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $resetCode;

    public function __construct($name, $resetCode)
    {
        $this->name = $name;
        $this->resetCode = $resetCode;
    }

    public function build()
    {
        return $this->subject('Your password reset code (valid for 10 min)')
            ->view('emails.reset_password')
            ->with([
                'name' => $this->name,
                'resetCode' => $this->resetCode,
            ]);
    }
}
