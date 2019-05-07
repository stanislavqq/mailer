<?php

namespace App\Jobs;

use App\Mail\Mailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $model;
    public $to;
    public $mailData;
    protected $isTest;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $emailSender, $isTest = false)
    {

        $this->mailData = (object)$emailSender;
        $this->to = $this->mailData->to;

        $this->isTest = $isTest;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->isTest) {
            $headers = "From: " . strip_tags($this->mailData->from) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            mail($this->to, $this->mailData->subject, $this->mailData->body, $headers);
        } else {
            Mail::send(new Mailer($this->to, $this->mailData->from, $this->mailData->subject, $this->mailData->body));
        }
    }
}
