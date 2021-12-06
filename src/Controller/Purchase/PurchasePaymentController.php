<?php

namespace App\Controller\Purchase;

use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);

        if (!$purchase) {
            return $this->redirectToRoute('cart_show');
        }

        \Stripe\Stripe::setApiKey(
            'sk_test_51I5nGIHP4MlDkgKUfdWRVoEw6N6yzkSIlG9XOquCe5yGQFH24wPpdAlxWUJRuiEpczwNKZHdgEKwNM1uNAjof9nU00MXn9eRgS'
        );
        $paymentIntent = \Stripe\PaymentIntent::create(
            [
                'amount' => $purchase->getTotal(),
                'currency' => 'eur'
            ]
        );

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'purchase' => $purchase
        ]);
    }

}