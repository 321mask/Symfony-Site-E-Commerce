<?php

namespace App\Services;

use Stripe\Stripe;
use App\Entity\OrderDetails;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeSession extends AbstractController {
    public function getStripeSession($order, $products, $manager) {
        foreach ($products as $product) {
            $orderDetails = new OrderDetails();

            $orderDetails->setMyOrder($order)
                ->setMyOrder($order)
                ->setProduct($product['product'])
                ->setQuantity($product['quantity'])
                ->setPrice($product['product']->getPrice());

            $products_stripe[] = [
                'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'description' => $product['product']->getDescription(),
                        'images' => [$product['product']->getImage()],
                    ],
                    'unit_amount' => $product['product']->getPrice(),
                ],
                'quantity' => $product['quantity'],
            ];

            $manager->persist($orderDetails);
        }

        $products_stripe[] = [
            'price_data' => [
                'currency' => 'EUR',
                'product_data' => [
                    'name' => $order->getCarrier()->getName(),
                ],
                'unit_amount' => $order->getCarrier()->getPrice(),
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey($this->getParameter('Stripekey'));

        $checkout_session = Session::create([
            'line_items' => $products_stripe,
            'mode' => 'payment',
            'success_url' => $this->getParameter('Domaine') . '/commande/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->getParameter('Domaine') . '/commande/fail/{CHECKOUT_SESSION_ID}',
        ]);

        return $order->setUrl($checkout_session->id);
    }
}