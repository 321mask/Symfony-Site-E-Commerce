<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\Cart;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AfterPayController extends AbstractController
{
    #[Route('/commande/success/{url}', name: 'app_after_pay')]
    public function success(Order $order, EntityManagerInterface $manager, Cart $cart, MailerService $mailer): Response
    {
          $order->setStatus(1);
          $manager->persist($order);
          $manager->flush();
          $cart->remove();
          $user = $this->getUser();

          $mailer->send(
            $user->getEmail(),
            'Confirmation de votre commande',
            'emails/order_confirmation.html.twig',
            [
                'user' => $user,
                'order' => $order
            ]
        );

        return $this->render('order/after_pay.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/commande/fail/{sessionId}', name: 'app_after_pay_fail')]
    public function fail(): Response
    {
        return $this->render('order/after_pay_fail.html.twig', [
            
        ]);
    }
}
