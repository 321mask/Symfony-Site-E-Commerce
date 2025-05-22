<?php

namespace App\Services;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport\TransportInterface;

class MailerService
{
    private TransportInterface $mailer;
    private Environment $twig;

    public function __construct(TransportInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(string $to, string $subject, string $template, array $context = []): void
    {
        $html = $this->twig->render($template, $context);

        $email = (new Email())
            ->from('mask390@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($html);

        $this->mailer->send($email);
    }
}

