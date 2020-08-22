<?php

namespace App\Service\Cart;

use App\Repository\ProductRepository;
use PhpParser\Node\Expr\Cast\Int_;
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
     * Methode d'ajout de dépendances à la méthode __construct
     * injection de dépendance
     * @param SessionInterface $session 
     * @param ProductRepository $productRepository 
     * 
     * @return void 
     */
    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    /**
     * Ajoute un produit dans le panier ou augmente sa quantité de 1 s'il est déjà présent
     * @param int $id
     * 
     * @return array 
     */
    public function add(int $id): array
    {
        // Récupération du panier de la session s'il existe - la valeur par défaut est un tableau vide
        $cart = $this->session->get('cart', []);
        // Initialisation du tableau pour le message flash
        $message = [];
        // Verifie si l'identifiant du produit est déjà dans le panier - si oui ajoute 1 à la quantité
        if (!empty($cart[$id])) {
            $cart[$id]++;
            // Ajout d'un message de confirmation
            $message = [
                'type' => 'success',
                'text' => 'The quantity of the product has been increased by 1 successfully!'
            ];
        } else {
            // Sinon ajoute au panier l'identifiant du produit associé à la quantité 1
            $cart[$id] = 1;
            // Ajout d'un message de confirmation
            $message = [
                'type' => 'success',
                'text' => 'The product was added successfully!'
            ];
        }
        // Sauvegarde le panier en cours dans la session
        $this->session->set('cart', $cart);
        // Retourne le message à afficher
        return $message;
    }

    /**
     * Supprime un produit du panier
     * @param int $id 
     * 
     * @return void 
     */
    public function remove(int $id): array
    {
        // Récupération du panier
        $cart = $this->session->get('cart', []);
        // Initialisation du tableau pour le message flash
        $message = [];
        // Si l'identifiant du produit existe dans le panier
        if (!empty($cart[$id])) {
            // Suppression de cette variable de session
            unset($cart[$id]);
            // Ajout d'un message de confirmation
            $message = [
                'type' => 'danger',
                'text' => 'The product was successfully deleted!'
            ];
        }
        // Actualise le nouveau panier
        $this->session->set('cart', $cart);
        // Retourne le message à afficher
        return $message;
    }

    /**
     * Ajoute ou enlève 1 à la quantité du produit
     * @param int $id 
     * @param string $direction 
     * 
     * @return int 
     */
    public function updateQuantity(int $id, string $direction): int
    {
        // Récupération du panier de la session s'il existe - la valeur par défaut est un tableau vide
        $cart = $this->session->get('cart', []);

        // Verifie si l'identifiant du produit est déjà dans le panier - si oui modifie la quantité
        if (!empty($cart[$id])) {
            // Suivant la direction la quantité augmente ou diminue de 1
            $qte = ($direction === "up") ? 1 : -1;
            // Met à jour la quantité du produit
            $cart[$id] += $qte;
            // Si la quantité devient égale à zéro le produit est retiré du panier
            if ($cart[$id] < 1) {
                // Suppression de cette variable de session
                $cart[$id] = 1;
            }
        }
        // Sauvegarde le panier en cours dans la session
        $this->session->set('cart', $cart);
        return $cart[$id];
    }

    /**
     * Renvoie les informations concernant les produits contenus dans le panier
     * 
     * @return array 
     */
    public function getDataCart(): array
    {
        // Récupere le panier en cours
        $cart = $this->session->get('cart', []);
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

    /**
     * Renvoie la quantité de produit dans le panier
     * 
     * @return int 
     */
    public function getQuantityCart(): int
    {
        // Initialisation du montant du panier
        $count = 0;
        // Boucle sur le panier
        foreach ($this->getDataCart() as $productData) {
            // Calcul du montant du panier avant de l'envoyer sur la page twig
            $count += $productData['quantity'];
        }
        return $count;
    }

    /**
     * Renvoie le montant total d'un article
     * @param int $id
     * 
     * @return float 
     */
    public function getTotalProduct(int $id): float
    {
        // Récupere le panier en cours
        $cart = $this->session->get('cart', []);
        // Initialisation du montant de la ligne produit
        $total = 0;
        // Recupère le prix du produit
        $price = $this->productRepository->find($id)->getPrice();
        $total = $price * $cart[$id];
        return $total;
    }

    public function validCart()
    {
        // Récupere le panier en cours
        $cart = $this->session->get('cart', []);
    }
}
