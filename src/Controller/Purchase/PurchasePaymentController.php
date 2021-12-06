<?php

namespace App\Controller\Purchase;

use App\Repository\PurchaseRepository;
use App\Entity\Purchase;
use App\Stripe\StripeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
        $purchase = $purchaseRepository->find($id);

        if (!$purchase || $purchase->getStatus() === Purchase::STATUS_PAID || $purchase->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('cart_show');
        }

        $paymentIntent = $stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'purchase' => $purchase
        ]);
    }

}