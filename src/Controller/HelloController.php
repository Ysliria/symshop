<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Detector;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{

    /**
     * @Route("/hello/{prenom?World}", name="hello")
     */
    public function hello($prenom, LoggerInterface $logger, Calculator $calculator, Slugify $slugify, Environment $twig, Detector $detector)
    {
        dump($detector->detect(101));
        dump($detector->detect(10));

        return new Response('HelloController');
    }
}