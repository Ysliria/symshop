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
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confrmation');

            return $this->redirectToRoute('cart_show');
        }

        $user      = $this->getUser();
        $cartItems = $this->cartService->getDetailedCardItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide !');

            return $this->redirectToRoute('cart_show');
        }

        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);
        $this->cartService->empty();
        $this->addFlash('success', 'La commande a bien été enregistrée !');

        return $this->redirectToRoute('purchase_index');
    }
}