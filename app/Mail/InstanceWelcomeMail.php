<?php
namespace App\Mail;

use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstanceWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $instance;

    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    public function build()
    {
        return $this->subject('Votre espace Paheko est prêt !')
            ->view('emails.instance_welcome');
    }
}
