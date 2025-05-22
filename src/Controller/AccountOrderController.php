<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AccountOrderController extends AbstractController
{
    #[Route('/compte/mes-commandes', name: 'account_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->findBy(['user' => $user]);
        return $this->render('account/order.html.twig', [
            'orders' => $orders,
        ]);
    }
    #[Route('/compte/mes-commandes/{reference}', name: 'order_details')]
    public function orderDetails(Order $order): Response {
    
        return $this->render('account/details.html.twig', [
            'order' => $order, 
        ]);
    }
}
