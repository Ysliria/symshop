<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if(!$category) {
            throw $this->createNotFoundException('La catégorie demandé n\'existe pas !');
        }

        return $this->render('product/category.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if(!$product) {
            throw $this->createNotFoundException('Le produit n\'existe pas !');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManagerInterface, ValidatorInterface $validator)
    {
        // Test
        $age = 200;

        $resultat = $validator->validate($age, [
            new LessThanOrEqual([
                'value' => 120,
                'message' => 'L\'âge doit être unférieur à {{ compared_value }}, mais vou avez donné {{ value }}.'
            ]),
            new GreaterThan([
                'value' => 0,
                'message' => 'L\'âge doit être supérieur à 0.'
            ])
        ]);

        if ($resultat->count() > 0) {
            dd('Il y a des erreurs', $resultat);
        }

        dd('Tout va bien !');
        // Fin test

        $product = $productRepository->findOneById($id);
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $entityManagerInterface->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/edit/html.twig', [
            'product' => $product,
            'formView' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $sluggerInterface, EntityManagerInterface $entityManagerInterface)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $product->setSlug(strtolower($sluggerInterface->slug($product->getName())));
            
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
