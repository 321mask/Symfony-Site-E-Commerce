<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private TransportInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function sendEmailConfirmation(string $routeName, User $user): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $routeName,
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $email = (new Email())
            ->from(new Address('noreply@monsite.com', 'Ma Boutique'))
            ->to($user->getEmail())
            ->subject('Confirmez votre adresse email, '.$user->getFirstName())
            ->html('<p>Merci de confirmer votre compte en cliquant <a href="' . $signatureComponents->getSignedUrl() . '">ici</a>.</p>');

        $this->mailer->send($email);
    }
    public function verifyEmailConfirmationFromRequest(Request $request, User $user): void
    {
        try {
            $this->verifyEmailHelper->validateEmailConfirmationFromRequest(
                $request,
                (string) $user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $e) {
            throw new \RuntimeException('Email verification failed: ' . $e->getReason());
        }
    }
}
