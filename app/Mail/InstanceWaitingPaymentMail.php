<?php
namespace App\Mail;

use App\Models\Instance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstanceWaitingPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $instance;

    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    public function build()
    {
        return $this->subject('Finalisez votre paiement pour activer votre espace MonAsso')
            ->view('emails.instance_waiting_payment');
    }
}
