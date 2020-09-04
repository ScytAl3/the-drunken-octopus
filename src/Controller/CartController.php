<?php

namespace App\Controller;

use LogicException;
use UnexpectedValueException;
use App\Service\Cart\CartService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart_index", methods={"GET"})
     * @param CartService $cartService 
     * @return Response 
     * @throws LogicException 
     * @throws UnexpectedValueException 
     */
    public function index(CartService $cartService): Response
    {
        // Appelle de la méthode qui retourne les informations associées aux produits du panier
        $cartProductData = $cartService->getDataCart();
        // Appelle de la méthode qui calcul le nombre total de produit dans le panier
        $count = $cartService->getQuantityCart();
        // Appelle de la méthode qui calcul le montant total du panier
        $total = $cartService->getTotalCart();
        // dd($cartProductData);
        return $this->render('cart/index.html.twig', [
            'items' => $cartProductData,
            'count' => $count,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/cart/add/{id<[0-9]+>}", name="app_cart_add", methods={"GET", "POST"})
     * @param int $id 
     * @param Request $request 
     * @param CartService $cartService 
     * @return RedirectResponse 
     * @throws SuspiciousOperationException 
     * @throws LogicException 
     */
    public function add(int $id, Request $request, CartService $cartService)
    {
        // Verifie la method utilisée pour ajouter le produit :
        // - depuis la page produit -> GET
        // - depuis la page détail -> POST 
        $qty = ($request->isMethod('POST')) ? $_POST['quantity'] : 1;
        // Appelle de la methode add() associé au cartService et récupération du message
        $message = $cartService->add($id, $qty);
        // Création du message flash
        $this->addFlash(
            $message['type'],
            $message['text']
        );
        // Redirection vers le panier et focus sur le dernier produit ajouté
        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * @Route("/cart/remove/{id<[0-9]+>}", name="app_cart_remove")
     * @param int $id 
     * @param CartService $cartService 
     * @return RedirectResponse 
     * @throws LogicException 
     */
    public function remove(int $id, CartService $cartService)
    {
        // Appelle de la methode add() associé au cartService et récupération du message
        $message = $cartService->remove($id);
        // Création du message flash
        $this->addFlash(
            $message['type'],
            $message['text']
        );

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * @Route("/cart/{id<\d+>}/quantity/{direction<up|down>}", name="app_cart_update", methods="POST")
     * @param int $id 
     * @param string $direction 
     * @param CartService $cartService 
     * @return JsonResponse 
     * @throws LogicException 
     */
    public function updateQuantity(int $id, string $direction, CartService $cartService): JsonResponse
    {
        return $this->json([
            'newQuantity' => $cartService->updateQuantity($id, $direction),
            'newTotal' => $this->renderView('cart/_newTotalProduct.html.twig', [
                'montantProduct' => $cartService->getTotalProduct($id)
            ]),
            'panierNewTotal' => $this->renderView('cart/_newTotalCart.html.twig', [
                'montantCart' => $cartService->getTotalCart()
            ]),
            'newProductCount' => $this->renderView('cart/_quantity.html.twig', [
                'count' => $cartService->getQuantityCart()
            ]),
            'productCoundHeader' => $cartService->getQuantityCart(),
        ]);
    }

    /**
     * @Route("/cart/clear", name="app_cart_clear", methods="GET")
     * @param CartService $cartService 
     * @return RedirectResponse 
     * @throws LogicException 
     */
    public function clearAction(CartService $cartService)
    {
        $cartService->clearCart();
        // Création du message flash pour informer que tout c'est bien déroulé
        $this->addFlash(
            'success',
            'The basket has been emptied successfully.'
        );
        // redirige vers la page du panier
        return $this->redirectToRoute('app_product_index');
    }
}
