<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(EntityManagerInterface $em)
    {
        $productRepository = $em->getRepository(Product::class);
        $product = new Product;

        /*
        $product->setName('Table en métal');
        $product->setPrice(3000);
        $product->setSlug('table-en-metal');
        */
        
        // Grace au design pattern fluent qui incite à faire un retour de $this, on peut chainer les méthodes.
        $product
            ->setName('Table en métal')
            ->setPrice(3000)
            ->setSlug('table-en-metal');

        $em->persist($product);
        $em->flush();

        dd($product);

        // pour mettre a jour
        $product = $productRepository->find(3);
        $product->setPrice(2500);
        $em->flush(); // pas besoin de persister il est déjà en base

        // pour supprimer
        $em->remove($product);
        $em->flush();

        return $this->render('home.html.twig');
    }
}