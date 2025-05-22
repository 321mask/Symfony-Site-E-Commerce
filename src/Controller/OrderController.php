<?php

namespace App\Controller;

use DateTime;
use App\Entity\Order;
use App\Services\Cart;
use App\Form\OrderType;
use Stripe\StripeClient;

use App\Services\StripeSession;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(Request $request, Cart $cart, ProductRepository $repo, EntityManagerInterface $manager, StripeSession $stripeSession) : Response
    {
        $user = $this->getUser();

        $form = $this->createForm(OrderType::class, null, ['user' => $user]);
        $form->handleRequest($request);

        $products = [];
        foreach ($cart->get() as $id => $quantity) {
            $products[] = [
                'product' => $repo->findOneById($id),
                'quantity' => $quantity
            ];
        }

        if (count($user->getAddresses()) == 0) {
            $this->addFlash('warning', 'Vous n\'avait pas d\'adresses, veuillez en crÃ©er une.');
            return $this->redirectToRoute('add_account_address');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $date = new DateTime();

            $order = new Order();
            $order->setUser($user)
                ->setCreatedAt($date)
                ->setCarrier($form->getData()['carriers'])
                ->setStatus(0)
                ->setDelivery($form->getData()['delivery']);

            $date = $date->format("d-m-Y");
            $order->setReference($date . "_" . uniqid());
            
            $stripeSession->getStripeSession($order, $products, $manager);

            $manager->persist($order);

            $manager->flush();
            return $this->redirectToRoute('recap', [
                'reference' => $order->getReference(),
            ]);
        }

        return $this->render('order/index.html.twig', [
            'form' => $form,
            'products' => $products,
        ]);
    }

    #[Route('/commande/recapitulatif/{reference}', name: 'recap')]
    public function recap(Order $order): Response
    {
        $stripe = new StripeClient($this->getParameter('Stripekey'));

        $session = $stripe->checkout->sessions->retrieve($order->getUrl());

        if ($this->getUser() != $order->getUser()) {
            $this->addFlash(
                'danger m-1 mx-5',
                'Non non non, petit malin :)'
            );
            return $this->redirectToRoute('home');
        };

        return $this->render('order/recap.html.twig', [
            'order' => $order,
            'session' => $session,
        ]);
    }
}