<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function index()
    {
        var_dump("Ca fonctionne");
        die;
    }

    public function test()
    {
        $request = Request::createFromGlobals();

        // dump($request);
        // query = get
        // request = post

        $age = $request->query->get('age', 0);
        
        return new Response("Vous avez $age ans !");
    }
/* similaire à 
    public function test(Request $request)
    {
        $age = $request->query->get('age', 0);
        
        return new Response("Vous avez $age ans !");
    }
*/
}