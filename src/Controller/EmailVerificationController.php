<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class EmailVerificationController extends AbstractController
{
    #[Route('/verify/email', name: 'verify_email')]
    public function verifyUserEmail(
        Request $request,
        EmailVerifier $emailVerifier,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $id = $request->get('id');

        if (!$id || !($user = $userRepository->find($id))) {
            $this->addFlash('danger', 'Lien de vérification invalide ou utilisateur introuvable.');
            return $this->redirectToRoute('register');
        }

        try {
            $emailVerifier->verifyEmailConfirmationFromRequest($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', 'Erreur de vérification : ' . $exception->getReason());
            return $this->redirectToRoute('register');
        }

        $user->setActive(true);
        $entityManager->flush();

        $this->addFlash('success', 'Votre adresse email a bien été confirmée.');

        return $this->redirectToRoute('login');
    }
}
