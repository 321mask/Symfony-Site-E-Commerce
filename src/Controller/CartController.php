<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Services\Cart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart, ProductRepository $repo): Response
    {
        $products = [];
        foreach ($cart->get() as $id => $quantity) {
            $products[] = [
                'product' => $repo->find($id),
                'quantity' => $quantity
            ];
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->get(),
            'products' => $products,
        ]);
    }

    // Ajouter un produit
    #[Route('/cart/add/{id}', name: 'add_cart')]
    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }
    
    // Supprimer panier
    #[Route('/cart/remove', name: 'remove_cart')]
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('cart');
    }

    // Supprimer un produit
    #[Route('/cart/delete/{id}', name: 'delete_item')]
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('cart');
    }

    // Décrémenter un produit
    #[Route('/cart/minus/{id}', name: 'minus_cart')]
    public function minus(Cart $cart, $id): Response
    {
        $cart->minus($id);

        return $this->redirectToRoute('cart');
    }
}
