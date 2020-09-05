<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\PurchaseOrder;
use App\Entity\PurchaseProduct;
use App\Entity\ShippingAddresses;
use App\Service\Cart\CartService;
use App\Form\ShippingAddressFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ShippingAddressesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class CheckoutController extends AbstractController
{
    /**
     * @Route("/cart/checkout/shipping", name="app_cart_checkout", methods={"GET", "POST"})
     * @return Response 
     * @throws AccessDeniedException 
     * @throws LogicException 
     * @throws UnexpectedValueException 
     */
    public function index(ShippingAddressesRepository $repo, Request $request, EntityManagerInterface $em): Response
    {
        // L'utilisateur doit être authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $addresses = $repo->findBy(['user' => $this->getUser()]);
        // dd($addresses);

        // Si l'utilisateur n'a enregistré aucune adresse
        if (empty($addresses)) {
            // Instanciation d'une nouvelle class ShippingAddresses
            $address = new ShippingAddresses;

            $form = $this->createForm(ShippingAddressFormType::class, $address);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $address->setUser($this->getUser());
                $em->persist($address);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Address created successfully!'
                );

                // redirige vers la page de sélection des adresses associées au compte
                return $this->redirectToRoute('app_cart_checkout', []);
            }
            return $this->render('cart/address/address_create.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            // Sinon retourne sur la page de sélection des adresses
            return $this->render('cart/address/select.html.twig', [
                'addresses' => $addresses,
            ]);
        }
    }

    /**
     * @Route("/cart/shipping/{id<[0-9]+>}/order", name="app_cart_order", methods="GET")
     * @param EntityManagerInterface $em 
     * @param CartService $cartService 
     * @return RedirectResponse 
     * @throws AccessDeniedException 
     * @throws LogicException 
     */
    public function confirmOrder(EntityManagerInterface $em, ShippingAddresses $address, CartService $cartService)
    {
        // L'utilisateur doit être authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupère l'utilisateur authentifié
        $user = $this->getUser();
        // Récupère l'identifiant de l'adresse de livraison 
        $shipping = $address;
        // Appelle de la méthode qui retourne les informations associées au produit du panier
        $cartProductData = $cartService->getDataCart();

        // Instanciation d'une nouvelle commande
        $order = new PurchaseOrder;

        try {
            // Passage des paramètres pour la création de la commande
            $order
                ->setUser($user)
                ->setShippingAddress($shipping)
                ->setBillingAddress($shipping)
                ->setTotalPrice($cartService->getTotalCart())
                ->setPayement(false);
            $em->persist($order);
            $em->flush();

            // Parcours la liste des produits dans la panier
            foreach ($cartProductData as $productData) {
                // Instanciation d'une nouvelle ligne de commade associée au produit et à la commande
                // à chaque boucle
                $orderItem = new PurchaseProduct;
                $orderItem
                    ->setPurchaseOrder($order)
                    ->setProduct($productData['product'])
                    ->setQuantity($productData['quantity']);
                $em->persist($orderItem);
                $em->flush();

                // Instanciation d'un produit pour la mise à jour du stock
                $stockProduct = new Product;
                // Récupère les données du produit
                $stockProduct = $productData['product'];
                // Mise à jour du stock
                $stockProduct->setQuantity($stockProduct->getQuantity() - $productData['quantity']);
                $em->persist($stockProduct);
                $em->flush();
            }
            // Création du message flash pour informer que tout c'est bien déroulé
            $this->addFlash(
                'success',
                'Checkout completed. Your order will be shipped soon.'
            );
            // Vidage du panier de la session
            $cartService->clearCart();
        } catch (\Throwable $th) {
            $this->addFlash(
                'danger',
                'Checkout error: ' . $th
            );
        }
        // redirige vers la page du panier
        return $this->redirectToRoute('app_cart_index');
    }
}
