<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificatorService
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail(string $fromEmail, string $toEmail): void
    {
        $host = $_SERVER['HTTP_HOST'];

        $html = <<<html
            <!DOCTYPE html>
            <html>
                <head>
                    <style>
                    address {
                      display: block;
                    }
                    </style>
                </head>
                <body>
                    <address>
                    Written by <a href="mailto:$toEmail">$toEmail</a>.<br>
                    Visit us at: <a href="$host">$host</a><br>
                    </address>
                </body>
            </html>
            html;

        $email = (new Email())
            ->from($fromEmail)
            ->sender($fromEmail)
            ->to('a.o.ermolenko@gmail.com') // $toEmail

            ->priority(Email::PRIORITY_HIGH)
            ->subject('OK for you')
            ->html($html);

        $this->mailer->send($email);
    }
}
