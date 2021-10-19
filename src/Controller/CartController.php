<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id": "\d+"})
     */
    public function add($id, ProductRepository $productRepository, CartService $cartService, Request $request): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw new NotFoundHttpException("Le produit $id n'existe pas !");
        }

        $cartService->add($id);

        $this->addFlash('success', 'Le produit a bien été ajouté au panier');

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(CartService $cartService)
    {
        $detailedCart = $cartService->getFetailedCardItems();
        $total = $cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id": "\d+"})
     */
    public function delete($id, ProductRepository $productRepository, CartService $cartService)
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }

        $cartService->remove($id);

        $this->addFlash('success', 'Le produit a bien été supprimé du panier !');

        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id": "\d+"})
     */
    public function decrement($id, CartService $cartService, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);   

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être décréménté !");
        }

        $cartService->decrement($id);

        $this->addFlash('success', 'Le produit a bien été décrémenté');

        return $this->redirectToRoute('cart_show');

    }
}
