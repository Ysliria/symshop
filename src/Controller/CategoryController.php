<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CategoryController extends AbstractController
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository; 
    }

    public function renderMenuList()
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface): Response
    {
        $category = new Category;
        $categoryCreateForm =  $this->createForm(CategoryType::class, $category);

        $categoryCreateForm->handleRequest($request);

        if($categoryCreateForm->isSubmitted() && $categoryCreateForm->isValid()) {
            $category->setSlug(strtolower($sluggerInterface->slug($category->getName())));
            
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/create.html.twig', [
            'categoryCreateForm' => $categoryCreateForm->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface, Security $security): Response
    {        
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Vous n\'avez pas le doit d\'acceder à cette ressource !');

        $category = $categoryRepository->find($id);
        $categoryEditForm = $this->createForm(CategoryType::class, $category);

        if($categoryEditForm->isSubmitted() && $categoryEditForm->isValid()) {
            $category->setSlug(strtolower($sluggerInterface->slug($category->getName())));
            $entityManagerInterface->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'categoryEditForm' => $categoryEditForm->createView()
        ]);
    }
}
