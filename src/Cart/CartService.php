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

    public function add(int $id)
    {
        $cart = $this->sessionInterface->get('cart', []);

        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->sessionInterface->set('cart', $cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->sessionInterface->get('cart', []) as $id => $qty) {
            $product = $this->productRepository->find($id);

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    public function getFetailedCardItems(): array
    {
        $detailedCart = [];

        foreach ($this->sessionInterface->get('cart', []) as $id => $qty) {
            $product = $this->productRepository->find($id);

            $detailedCart[] = [
                'product' => $product,
                'qty' => $qty
            ];
        }

        return $detailedCart;
    }
}