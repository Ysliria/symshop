<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id": "\d+"})
     */
    public function add($id, ProductRepository $productRepository, SessionInterface $sessionInterface): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw new NotFoundHttpException("Le produit $id n'existe pas !");
        }

        $cart = $sessionInterface->get('cart', []);

        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        /** @var FlashBag */
        $flashBag = $sessionInterface->getBag('flashes');
        
        $flashBag->add('success', 'Le produit a bien été ajouté au panier');

        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }
}
