<?php

namespace App\Models\Mailer;

class Mail
{
    public $send = false;
    protected $message;
    protected $headers;

    protected $fromName;
    protected $fromEmail;

    protected $to;
    protected $from;
    protected $subject;

    public function __construct()
    {
        $headers = "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "From: $this->fromName <$this->from>\r\n";
        $headers .= "Bcc: $this->from\r\n";
        $headers .= "Reply-To: $this->from\r\n";

        $this->setHeaders($headers);
    }

    public function send()
    {
        if ($this->validate()) {
            $res = mail($this->to, $this->subject, $this->message, $this->headers);
            if ($res) $this->send = true;
        } else {
            return new \Exception('Отправка письма не удалась.');
        }

        return false;
    }

    public function to($emails): self
    {
        $this->to = $emails;
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function from(string $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function fromName(string $fromName): self
    {
        $this->fromName = $fromName;
        return $this;
    }

    public function fromEmail(string $fromEmail): self
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    public function message(string $message, array $templateVariables = []): self
    {
        $resMessage = $message;

        if (count($templateVariables)) {
            foreach ($templateVariables as $key => $variable) {
                if (is_null($variable)) {
                    $variable = "NONE";
                }
                $needle = "#" . strtoupper($key) . "#";
                $resMessage = str_replace($needle, $variable, $resMessage);
            }
        }

        $this->message = $resMessage;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setHeaders(string $headers)
    {
        $this->headers = $headers;
    }

    public function validate()
    {
        if (!$this->to) return new \Exception('');
        if (!$this->message) return new \Exception();
        if (!$this->headers) return new \Exception();
        if (!$this->fromName) return new \Exception();
        if (!$this->from) return new \Exception();
        if (!$this->subject) return new \Exception();

        return true;
    }
}
