<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Product;
use App\Form\SearchType;
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
        // Initialisation des données de recherche
        $data = new SearchData();
        // Initialisation du formulaire de recherche
        $form = $this->createForm(SearchType::class, $data);
        // Gestion de la requête qui est soumise par le formulaire de filtre
        $form->handleRequest($request);
        // dd($data);
        
        // $query = $repo->findAllAvailableQuery();
        $query = $repo->findSearchQuery($data);

        $products = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                12 /*limit per page*/
            );

        // set an array of custom parameters
        $products->setCustomParameters([
            'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination)
            'size' => 'small', # small|large (for template: twitter_bootstrap_v4_pagination)
            'style' => 'bottom',
            'span_class' => 'whatever',
        ]);

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
