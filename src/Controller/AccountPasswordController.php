<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AccountPasswordController extends AbstractController
{
    #[Route('/compte/modifier-mot-de-passe', name: 'resetPassword')]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$passwordHasher->isPasswordValid($user, $user->getOldPassword())) {
                $this->addFlash(
                    'danger',
                    'L\'ancien mot de passe est incorrect.'
                );
                return $this->redirectToRoute('resetPassword');
            } else {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $user->getNewPassword()
                );
                $user->setPassword($hashedPassword);

                $manager->persist($user);
                $manager->flush();
                
                $this->addFlash(
                    'success',
                    'Le mot de passe a bien été modifié.'
                );
                return $this->redirectToRoute('account');
            }
        }

        return $this->render('account/resetPassword.html.twig', [
            'form' => $form,
        ]);
    }
}
