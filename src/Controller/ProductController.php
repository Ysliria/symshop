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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Collection;
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
        $client = [
            'nom' => 'Chamla',
            'prenom' => 'Lior',
            'voiture' => [
                'marque' => 'Hyundai',
                'couleur' => 'Noire'
            ]
        ];

        $collection = new Collection([
            'nom' => new NotBlank(['message' => 'Le nom ne doit pas être vide']),
            'prenom' => [
                new NotBlank(['message' => 'Le prénom ne doit pas être vide']),
                new Length(['min' => 3, 'minMessage' => 'Le prénom ne doit pas avoir moins de 34 caractères'])
            ],
            'voiture' => new Collection([
                'marque' => new NotBlank(['message' => 'La marque de la voiture est obligatoire']),
                'couleur' => new NotBlank(['message' => 'La couleur de la voiture est obligatoire'])
            ])
        ]);

        $resultat = $validator->validate($client, $collection);

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
