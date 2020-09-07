<?php

namespace App\Controller\Account;

use App\Entity\PurchaseOrder;
use App\Entity\ShippingAddresses;
use App\Repository\PurchaseOrderRepository;
use App\Repository\PurchaseProductRepository;
use App\Repository\ShippingAddressesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrdersHistoryController extends AbstractController
{
    /**
     * @Route("/account/orders-history", name="app_account_order_history", methods={"GET"})
     */
    public function index(PurchaseOrderRepository $repo): Response
    {
        // Recupère l'utilisateur courant
        $user = $this->getUser();
        // Récupère l'historique des commandes
        $orders = $repo->findOrderHistory($user->getId());
        // dd($orders);
        return $this->render('account/orders/order_history.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/account/order-history/{id<[0-9]+>}/show", name="app_account_order_show", methods={"GET"})
     */
    public function showOrder(PurchaseProductRepository $repo, PurchaseOrder $order, ShippingAddressesRepository $shippingAddressesRepository): Response
    {
        // Récupère la liste des produits associée à la commande
        $purchasedProducts = $repo->findPurchasedProducts($order->getId());
        // dd($purchasedProducts);

        // Récupère l'adresse de livraison
        $shipping_address = new ShippingAddresses();
        $shipping_address = $shippingAddressesRepository->find($order->getShippingAddress());
        // dd($shipping_address);
            
        // $shipping_address = $order->getShippingAddress();

        return $this->render('account/orders/order_show.html.twig', [
            'purchasedProducts' => $purchasedProducts,
            'orderId' => $order->getId(),
            'date' => $order->getCreatedAt(),
            'total_ht' => $order->getTotalPrice(),
            'tva' => $order->getTotalPrice() * 0.2,
            'total_ttc' => $order->getTotalPrice() * 1.2,
            'shipping_address' => $shipping_address,
        ]);
    }

}
