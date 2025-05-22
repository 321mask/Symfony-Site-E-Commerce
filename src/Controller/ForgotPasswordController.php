<?php

namespace App\Controller;

use DateTime;
use App\Entity\ForgotPassword;
use App\Form\ResetPasswordType;
use App\Services\MailerService;
use App\Form\ForgotPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ForgotPasswordController extends AbstractController
{
    #[Route('/mot-de-passe-oublie', name: 'forgot_password')]
    public function index(Request $request, UserRepository $userRepository, EntityManagerInterface $manager, MailerService $mailer): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }

        if ($request->get('email')) {
            $user = $userRepository->findOneByEmail($request->get('email'));
            if ($user) {
                $forgotPassword = new ForgotPassword();
                $forgotPassword->setUser($user)->setToken(uniqid())->setCreatedAt(new DateTime());
                $manager->persist($forgotPassword);
                $manager->flush();
                $this->addFlash('success', 'Un email a été envoyé à '.$user->getEmail().'.');

                $mailer->send(
                    $user->getEmail(),               // recipient
                    'Réinitialisation de mot de passe',  // subject
                    'email/reset_password.html.twig',    // Twig template
                    [
                        'user' => $user,
                        'token' => $forgotPassword->getToken(),
                    ]
                );
                
            } else {
                $this->addFlash('danger', 'le mail : '.$request->get('email').' est unconnu');
            }
            return $this->redirectToRoute('home');
        }
        

        return $this->render('forgot_password/index.html.twig', [
            'controller_name' => 'ForgotPasswordController',
        ]);
    }
    #[Route('/reset-password/{token}', name: 'reset_password')]
    public function reset(string $token, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $forgotPassword = $em->getRepository(ForgotPassword::class)->findOneBy(['token' => $token]);

        $createDate = $forgotPassword->getCreatedAt();
        $now = new DateTime();
        if (!$forgotPassword || ($now > $createDate->modify('+1 hour'))) {
            $this->addFlash('danger', 'Lien de réinitialisation invalide ou expiré.');
            return $this->redirectToRoute('forgot_password');
        }

        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $forgotPassword->getUser();
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('newPassword')->getData()));
            $em->remove($forgotPassword);
            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été modifié.');
            return $this->redirectToRoute('login');
        }

        // Si formulaire non soumis ou non valide
        if ($request->isMethod('POST') && $form->isSubmitted() && !$form->isValid()) {
            // Pour Turbo, on doit renvoyer un statut 422 en cas d'erreur de validation
            return $this->render('forgot_password/reset.html.twig', [
                'form' => $form->createView(),
            ], new Response(null, 422));
        }

        return $this->render('forgot_password/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
