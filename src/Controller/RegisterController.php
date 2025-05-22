<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Services\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'register')]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, EmailVerifier $emailVerifier): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $manager->flush();

            $emailVerifier->sendEmailConfirmation('verify_email', $user);

            $this->addFlash('success', 'Un email de confirmation vous a été envoyé.');

            $this->addFlash(
                'success',
                'L\'enregistrement s\'est effectué correctement'
            );

            return $this->redirectToRoute('login');
        }

        return $this->render('register/index.html.twig', [
            'form' => $form,
        ]);
    }
}
