<?php

namespace App\Service\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    /**
     * 
     * @var SessionInterface
     */
    protected $session;

    /**
     * 
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * 
     * @var FlashBagInterface
     */
    protected $flashBag;

    /**
     * Methode d'ajout de dépendances à la méthode __construct
     * injection de dépendance
     * @param SessionInterface $session 
     * @param ProductRepository $productRepository 
     * @param FlashBagInterface $flashBag
     * 
     * @return void 
     */
    public function __construct(SessionInterface $session, ProductRepository $productRepository, FlashBagInterface $flashBag)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->flashBag = $flashBag;
    }

    /**
     * Ajoute un produit dans le panier ou augmente sa quantité de 1 s'il est déjà présent
     * @param int $id
     * 
     * @return void 
     */
    public function add(int $id)
    {
        // Récupération du panier de la session s'il existe - la valeur par défaut est un tableau vide
        $cart = $this->session->get('cart', []);

        // Verifie si l'identifiant du produit est déjà dans le panier - si oui ajoute 1 à la quantité
        if (!empty($cart[$id])) {
            $cart[$id]++;
            // Ajout d'un message de confirmation
            $this->flashBag->add(
                'success',
                'The quantity of the product has been increased by 1 successfully!'
            );
        } else {
            // Sinon ajoute au panier l'identifiant du produit associé à la quantité 1
            $cart[$id] = 1;
            // Ajout d'un message de confirmation
            $this->flashBag->add(
                'success',
                'The product was added successfully!'
            );
        }
        // Sauvegarde le panier en cours dans la session
        $this->session->set('cart', $cart);
    }

    /**
     * Supprime un produit du panier
     * @param int $id 
     * 
     * @return void 
     */
    public function remove(int $id)
    {
        // Récupération du panier
        $cart = $this->session->get('cart', []);
        // Si l'identifiant du produit existe dans le panier
        if (!empty($cart[$id])) {
            // Suppression de cette variable de session
            unset($cart[$id]);
            // Ajout d'un message de confirmation
            $this->flashBag->add(
                'danger',
                'The product was successfully deleted!'
            );
        }
        // Actualise le nouveau panier
        $this->session->set('cart', $cart);
    }

    /**
     * Renvoie les informations concernant les produits contenus dans le panier
     * 
     * @return array 
     */
    public function getDataCart(): array
    {
        // Récupere le panier en cours
        $cart = $this->session->get('cart');
        // Initialisation d'un tableau pour stocker les données d'un produit et sa quantité
        $cartProductData = [];
        // Parcours du panier pour récuperer tous les produits qu'il contient
        foreach ($cart as $id => $quantity) {
            // Ajoute dans le tableau des données le produit associé à sa quantité
            $cartProductData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        return $cartProductData;
    }

    /**
     * Renvoie le montant total du panier
     * 
     * @return float 
     */
    public function getTotalCart(): float
    {
        // Initialisation du montant du panier
        $total = 0;
        // Boucle sur le panier
        foreach ($this->getDataCart() as $productData) {
            // Calcul du montant du panier avant de l'envoyer sur la page twig
            $total += $productData['product']->getPrice() * $productData['quantity'];
        }
        return $total;
    }
}
