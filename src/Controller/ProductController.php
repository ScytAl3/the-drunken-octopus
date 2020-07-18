<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="app_product_index", methods={"GET"})
     * @param ProductRepository $repo
     * 
     * @return Response
     */
    public function index(ProductRepository $repo): Response
    {
        // $product = $repo->findAllAvailable();
        // dump($product);

        return $this->render('product/index.html.twig', [
            'current_page' => 'products',
            'products' => $repo->findAllAvailable(),
        ]);
    }

    /**
     * @Route("/products/{slug<[a-z0-9\-]*>}-{id<[0-9]+>}", name="app_product_show", methods={"GET"})
     * @param Product $product
     * 
     * @return Response
     */
    public function show(Product $product, string $slug): Response
    {
        // Redirection vers le lien canonique si le slug a été modifié dans l'URL
        if ($product->getSlug() !== $slug) {
            return $this->redirectToRoute('app_product_index', [
                'id' => $product->getId(),
                'slug' => $product->getSlug()
            ], 301);
        }

        return $this->render('product/show.html.twig', [
            'current_page' => 'products',
            'product' => $product,
        ]);
    }
}
