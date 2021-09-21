<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

class HelloController extends AbstractController
{
    protected $twig;


    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/hello/{prenom?World}", name="hello")
     */
    public function hello($prenom, Environment $twig)
    {
        return $this->render('hello.html.twig', [
            'prenom' => $prenom
        ]);
    }

    protected function render($path, $variables = [])
    {
        $html = $this->twig->render($path, $variables);

        return new Response($html);
    }
}