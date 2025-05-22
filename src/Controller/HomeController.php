<?php

namespace App\Controller;

use App\Services\MailerService;
use Symfony\Component\Mime\Email;
use App\Repository\ProductRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\MailerService as ServiceMailerService;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(RequestStack $requestStack, MailerService $mailer, ProductRepository $repo): Response
    {
        // $panier = $requestStack->getSession()->get('cart');        
        // $panier[0]=70;
        // $panier[1]=75;
        // $requestStack->getSession()->set('cart', $panier);

        // $requestStack->getSession()->remove('cart');
        // dd($requestStack->getSession()->get('cart', []));

        // $mailer->send(
        //     'mask390@gmail.com',
        //     'Time for Symfony Mailer!',
        //     'email/example.html.twig',
        //     [
        //         'user' => ['firstname' => 'John']
        //     ]
            
        // );

        // $products = $repo->findBy([], null, 10);
        // shuffle($products);
        // $products = array_slice($products, 0, 3);

        $onHomePageProducts = $repo->findByOnHomePage(true);

        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $onHomePageProducts,
        ]);
    }
}
