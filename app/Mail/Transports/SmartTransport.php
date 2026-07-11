<?php

namespace App\Mail\Transports;

use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Str;

class SmartTransport implements TransportInterface
{
    protected TransportInterface $defaultTransport;
    protected TransportInterface $gmailTransport;

    public function __construct(TransportInterface $defaultTransport, TransportInterface $gmailTransport)
    {
        $this->defaultTransport = $defaultTransport;
        $this->gmailTransport = $gmailTransport;
    }

    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        $transport = $this->defaultTransport;
        $recipients = [];

        if ($envelope) {
            $recipients = $envelope->getRecipients();
        } elseif ($message instanceof Email) {
            $recipients = $message->getTo();
        }

        foreach ($recipients as $recipient) {
            if (Str::endsWith(strtolower($recipient->getAddress()), '@gmail.com')) {
                $transport = $this->gmailTransport;
                break;
            }
        }

        return $transport->send($message, $envelope);
    }

    public function __toString(): string
    {
        return 'smart';
    }
}
