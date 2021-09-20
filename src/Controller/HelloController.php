<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

class HelloController extends AbstractController
{

    /**
     * @Route("/hello/{prenom?World}", name="hello")
     */
    public function hello($prenom, Environment $twig)
    {
        $html = $twig->render('hello.html.twig', [
            'prenom' => $prenom,
            'age' => 33,
            'prenoms' => [
                'Lior',
                'Magalie',
                'Elise'
            ]
        ]);

        return new Response($html);
    }
}