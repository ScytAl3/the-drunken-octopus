<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart_index", methods={"GET"})
     */
    public function index(CartService $cartService)
    {
        // Appelle de la méthode qui retourne les informations associées au produit du panier
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
     * @Route("/cart/add/{id}", name="app_cart_add", methods={"GET"})
     * @var int $id
     */
    public function add(int $id, CartService $cartService)
    {
        // Appelle de la methode add() associé au cartService et récupération du message
        $message = $cartService->add($id);
        // Création du message flash
        $this->addFlash(
            $message['type'],
            $message['text']
        );

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * @Route("/cart/remove/{id}", name="app_cart_remove")
     * @param int $id
     * 
     * @return void 
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
     * 
     * @return void 
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
     * @IsGranted("ROLE_USER")
     * @Route("/cart/checkout", name="app_cart_checkout", methods="GET")
     * @return Response 
     */
    public function checkout(): Response
    {
        // L'utilisateur doit être authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('cart/checkout.html.twig', []);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/cart/order", name="app_cart_order", methods="GET")
     * @return Response 
     */
    public function confirmOrder(EntityManagerInterface $em, CartService $cartService)
    {
        // L'utilisateur doit être authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Récupère l'utilisateur authentifié
        $user = $this->getUser();
        // Appelle de la méthode qui retourne les informations associées au produit du panier
        $cartProductData = $cartService->getDataCart();
        // Instanciation d'une nouvelle commande
        // $order = new Order;
        // Instanciation d'une nouvelle ligne commande
        // $orderItem = new OrderItem;
        // Passage des paramètres
        // $order->setUser($user);
        // $em->persist($order);
        // $em->flush();

        // redirige vers la page d'accueil
        return $this->redirectToRoute('app_home');
    }
}
