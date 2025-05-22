<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function add($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $cart = $this->requestStack->getSession()->set('cart', $cart);
    }

    public function minus($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]--;
        }

        if ($cart[$id] === 0) {
            unset($cart[$id]);
        }

        $cart = $this->requestStack->getSession()->set('cart', $cart);
    }

    public function delete($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        unset($cart[$id]);

        $cart = $this->requestStack->getSession()->set('cart', $cart);
    }

    public function get()
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }
}
