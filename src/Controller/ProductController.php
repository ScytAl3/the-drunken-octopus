<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
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
    public function index(ProductRepository $repo, Request $request): Response
    {
        // Initialisation des données de recherche
        $data = new SearchData();
        // Recupère dans la requête la valeur de la page de paginator - (par défaut si elle n'est pas défini = 1)
        $data->page = $request->get('page', 1);
        // Initialisation du formulaire de recherche
        $form = $this->createForm(SearchType::class, $data);
        // Gestion de la requête qui est soumise par le formulaire de filtre
        $form->handleRequest($request);
        // dd($data);

        $products = $repo->findSearch($data);

        return $this->render('product/index.html.twig', [
            'current_page' => 'products',
            'products' => $products,
            'form' => $form->createView(),
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
