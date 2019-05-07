<?php

namespace App\Mail;

use App\Models\Mailer\MailerDriver;
use App\Models\Mailer\UserMailerDrivers;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class Mailer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $content;

    /**
     * Create a new message instance.
     * DistributionSend constructor.
     * @param $message - Crude message
     */
    public function __construct($to, $from, $subject, $message, $fromName = '')
    {

        $userMailerData = UserMailerDrivers::where('user_id', Auth::user()->id)->first();
        $mailerDriver = MailerDriver::where('id', $userMailerData->driver_id)->first();

        Config::set('mail.driver', 'smtp');
        Config::set('mail.host', $mailerDriver->host);
        Config::set('mail.port', $mailerDriver->port);
        Config::set('mail.encryption', $mailerDriver->encryption);

        Config::set('mail.username', $userMailerData->login);
        Config::set('mail.password', $userMailerData->password);

        $this->content = $message;
        $this->to($to)
            ->from($from, $fromName)
            ->subject($subject);

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->withSwiftMessage(function ($message) {
            $headers = $message->getHeaders();
            $headers->addTextHeader('charset', 'UTF-8');
            $headers->addTextHeader('Content-type', 'text/html');
        });

        return $this->view('emails.template')
            ->with(['content' => $this->content]);
    }
}
