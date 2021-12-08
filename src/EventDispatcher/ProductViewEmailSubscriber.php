<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'product.view' => 'sendViewEmail'
        ];
    }

    public function sendViewEmail(ProductViewEvent $productViewEvent)
    {
        $this->logger->info('Le produit ' . $productViewEvent->getProduct()->getName() . ' a été vu !');
    }
}