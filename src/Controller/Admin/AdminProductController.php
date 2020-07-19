<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
{
    /**
     * 
     * @var ProductRepository
     */
    private $repository;

    /**
     * 
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ProductRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/product", name="app_admin_product", methods={"GET"})
     * 
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'current_page' => 'admin',
            'products' => $this->repository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/product/create", name="app_admin_product_create", methods={"GET", "POST"})
     * @param Request $request
     * 
     * @return void 
     */
    public function create(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product, [
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($product);
            $this->em->flush();

            $this->addFlash(
                'success',
                'Product created successfully!'
            );

            // redirige vers la page des produits à administrer
            return $this->redirectToRoute('app_admin_product', []);
        }

        return $this->render('admin/product/create.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product/{id<[0-9]+>}/edit", name="app_admin_product_edit", methods={"GET", "PUT"})
     * @param Product $product
     * @param Request $request
     * 
     * @return Response 
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash(
                'success',
                'Product updated successfully!'
            );

            // redirige vers la page qui montre les produits à administrer
            return $this->redirectToRoute('app_admin_product', []);
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
