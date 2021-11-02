<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
    protected $security;
    protected $routerInterface;
    protected $twig;

    public function __construct(Security $security, RouterInterface $routerInterface, Environment $twig)
    {
        $this->security = $security;
        $this->routerInterface = $routerInterface;
        $this->twig = $twig;
    }

    /**
     * @Route("/purchases", name="purchases_index")
     */
    public function index(): Response
    {
        /** @var User */
        $user =$this->security->getUser();

        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour accéder à vos commandes !');
        }

        $html = $this->twig->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);

        return new Response($html);
    }
}