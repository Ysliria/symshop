<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseConfirmationController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected CartService $cartService;
    protected PurchasePersister $persister;

    public function __construct(CartService $cartService, EntityManagerInterface $entityManager, PurchasePersister $persister)
    {
        $this->cartService   = $cartService;
        $this->entityManager = $entityManager;
        $this->persister = $persister;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmez une commande")
     */
    public function confirm(Request $request)
    {
        $purchase = new Purchase();
        $form = $this->createForm(CartConfirmationType::class, $purchase);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confrmation');

            return $this->redirectToRoute('cart_show');
        }

        $cartItems = $this->cartService->getDetailedCardItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide !');

            return $this->redirectToRoute('cart_show');
        }

        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);

        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }
}