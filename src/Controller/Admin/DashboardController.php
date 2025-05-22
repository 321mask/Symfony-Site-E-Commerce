<?php

namespace App\Controller\Admin;

use App\Controller\OrderController;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Config;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index(): Response
    {
        return $this->render('admin/dashBoard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Myboutique');
    }

    public function configureMenuItems(): iterable
{  
    $paidOrdersCount = count($this->orderRepository->findByStatus(1)); 
    $unpaidOrdersCount = count($this->orderRepository->findByStatus(0)); 

    yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

    yield MenuItem::section(('Configuration'));
    yield MenuItem::linkToCrud('Config', 'fas fa-cogs', Config::class);

    yield MenuItem::section(('Utilisateurs'));
    yield MenuItem::linkToCrud('Administrateurs', 'fas fa-users-gear', User::class)->setController(AdminCrudController::class);
    yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

    yield MenuItem::section(('Produits'));
    yield MenuItem::linkToCrud('Catégories', 'fas fa-clipboard-list', Category::class);
    yield MenuItem::linkToCrud('Produits', 'fas fa-boxes-stacked', Product::class);
    yield MenuItem::linkToCrud('Transporteurs', 'fas fa-truck', Carrier::class);
    yield MenuItem::linkToCrud('Commentaires', 'fas fa-comments', Comment::class);

    yield MenuItem::section(('Commandes'));
    yield MenuItem::linkToCrud('Commandes Payées', 'fas fa-check-circle', Order::class)->setController(PayeOrderCrudController::class)
        ->setQueryParameter('filters[status]', 1)
        ->setBadge($paidOrdersCount, $paidOrdersCount > 0 ? 'success' : 'secondary');
    yield MenuItem::linkToCrud('Commandes Non Payées', 'fas fa-times-circle', Order::class)->setController(OrderCrudController::class)
        ->setQueryParameter('filters[status]', 0)
        ->setBadge($unpaidOrdersCount, $unpaidOrdersCount > 0 ? 'danger' : 'secondary');
}

}

