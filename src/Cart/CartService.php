<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected SessionInterface $sessionInterface;
    protected ProductRepository $productRepository;
    
    public function __construct(SessionInterface $sessionInterface, ProductRepository $productRepository)
    {
        $this->sessionInterface = $sessionInterface;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->sessionInterface->get('cart', []);
    }

    protected function setCart(array $cart)
    {
        $this->sessionInterface->set('cart', $cart);
    }

    public function add(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }
            
        $cart[$id] += 1;
        

        $this->setCart($cart);
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->setCart($cart);
    }

    public function decrement($id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->setCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    public function getDetailedCardItems(): array
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
    }
}