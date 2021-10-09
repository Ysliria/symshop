<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface): Response
    {
        $category = new Category;
        $categoryCreateForm =  $this->createForm(CategoryType::class, $category);

        $categoryCreateForm->handleRequest($request);

        if($categoryCreateForm->isSubmitted()) {
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
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface): Response
    {
        $category = $categoryRepository->find($id);
        $categoryEditForm = $this->createForm(CategoryType::class, $category);

        if($categoryEditForm->isSubmitted()) {
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
