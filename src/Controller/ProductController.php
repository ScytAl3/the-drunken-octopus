<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="app_product_index", methods={"GET"})
     * @param ProductRepository $repo
     * @param PaginatorInterface $paginator 
     * 
     * @return Response
     */
    public function index(ProductRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $repo->findAllAvailableQuery();

        $products = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );

        return $this->render('product/index.html.twig', [
            'current_page' => 'products',
            'products' => $products,
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
