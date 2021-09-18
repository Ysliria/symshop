<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(Slugify $slugify)
    {
        $slug = $slugify->slugify('Cooucou les amis');
        dd($slug);
        $tva = $this->calculator->calcul(100);

        return new Response($tva);
    }

    /**
     * @Route("/test/{age</d+>?0}", name="test", methods={"GET", "POST"}, host="localhost", schemes={"http", "https"})
     */
    public function test(Request $request, $age)
    {
        return new Response("Vous avez $age ans !");
    }

}