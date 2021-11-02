<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController extends AbstractController
{
    protected $formFactory;
    protected $routerInterface;
    protected $security;
    protected $cartService;

    public function __construct(FormFactoryInterface $FormFactory, RouterInterface $routerInterface, Security $security, CartService $cartService)
    {
        $this->formFactory = $FormFactory;  
        $this->routerInterface = $routerInterface; 
        $this->security =$security;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request, FlashBagInterface $flashBag)
    {
        $form = $this->formFactory->create(CartConfirmationType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $flashBag->add('warning', 'Vous devez remplir le formulaire de confrmation');
            return new RedirectResponse($this->routerInterface->generate('cart_show'));
        }

        $user = $this->security->getUser();

        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour confirmez une commande');
        }

        $cartItems = $this->cartService->getDetailedCardItems();

        if (count($cartItems) === 0) {
            $flashBag->add('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide !');
            return new RedirectResponse($this->routerInterface->generate('cart_show'));
        }
    }
}