<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;
    protected MailerInterface $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'product.view' => 'sendViewEmail'
        ];
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendViewEmail(ProductViewEvent $productViewEvent)
    {
        $email = new TemplatedEmail();
        $email
            ->from(new Address('contact@mail.com', 'Infos de la boutique'))
            ->to('admin@mail.com')
            ->htmlTemplate('emails/product_view.html.twig')
            ->context([
                'product' => $productViewEvent->getProduct()
            ])
            ->subject('Visite du produit n° ' . $productViewEvent->getProduct()->getid())
        ;
        $this->mailer->send($email);

        $this->logger->info('Le produit ' . $productViewEvent->getProduct()->getName() . ' a été vu !');
    }
}