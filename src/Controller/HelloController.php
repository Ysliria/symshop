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
            'formateur1' => [
                'prenom' => 'Lior',
                'nom' => 'Chamla',
                'age' => 33
            ],
            'formateur2' => [
                'prenom' => 'Mickaël',
                'nom' => 'Auger',
                'age' => 43
            ]
        ]);

        return new Response($html);
    }
}