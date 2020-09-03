<?php

namespace App\Twig;

use App\Entity\Product;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AppExtension extends AbstractExtension
{
    /**
     * 
     * @var RequestStack
     */
    private $requestStack;

    /**
     * 
     * @var SessionInterface
     */
    private $session;

    /**
     * @param RequestStack $requestStack 
     * @param SessionInterface $session
     * @return void 
     */
    public function __construct(RequestStack $requestStack, SessionInterface $session)
    {
        $this->requestStack = $requestStack;
        $this->session = $session;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pluralize', [$this, 'pluralize']),
            new TwigFunction('set_active_route', [$this, 'setActiveRoute']),
            new TwigFunction('set_cart_counter', [$this, 'setCartCount']),
            new TwigFunction('available_or_sold_out', [$this, 'availableOrSoldOut']),
            new TwigFunction('is_a_new_product', [$this, 'isANewProduct'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Permet d'afficher le singulier ou le pluriel d'un mot en fonction du nombre
     * @param int $count 
     * @param string $singular 
     * @param null|string $plural 
     * @return string 
     */
    public function pluralize(int $count, string $singular, ?string $plural = null): string
    {
        // Si un pluriel a été défini on l'utilise sinon on utilise le singulier en ajoutant un "s"
        // Autre syntaxe $plural ?? = $singular . 's';
        $plural = $plural ?? $singular . 's';
        // Si le nombre est différent de 1 on utilise le pluriel
        $string = $count == 1 ? $singular : $plural;
        // Retourne le nombre avec la forme grammaticale correspondante
        return "$count $string";
    }

    /**
     * Si la route courrante correspond à la route demandée retourne la class qui peut être
     * passée en paramètres par défaut = active
     * @param string $route 
     * @param null|string $activeClass
     * 
     * @return string 
     */
    public function setActiveRoute(string $route, ?string $activeClass = 'active'): string
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        return (strpos($currentRoute, $route, 0) !== false) ? $activeClass : '';
    }

    /**
     * Si un panier existe renvoie le nombre total de produits
     * @return int 
     */
    public function setCartCount(): int
    {
        // Récupération du panier de la session s'il existe - la valeur par défaut est un tableau vide
        $cart = $this->session->get('cart', []);
        // Initialisation du compteur
        $count = 0;
        // Si le panier n'est pas vide
        if (!empty($cart)) {
            // Pour chaque couple ajoute la quantité correspondante
            foreach ($cart as $key => $value) {
                $count += $value;
            }
        }
        // retourne la quantité totale - sinon 0
        return $count;
    }

    /**
     * Vérifie la quantité en stock : le lien d'ajout au panier sera désactivé si la quantité = 0
     *
     * @param Product $product
     * @return string
     */
    public function availableOrSoldOut(Product $product): string
    {
        return ($product->getQuantity() == 0) ? 'disabled' : '';
    }

    /**
     * Affiche un badge sur le produit s'il est nouveau 
     *
     * @param Product $product
     * @return string
     */
    public function isANewProduct(Product $product, string $translation): string
    {
        if ($product->isNovelty()) {
            return '<span class="badge badge-success text-block">' . $translation . '</span>';
        } else {
            return '';
        }

        // return '<p class="text-block">'. $product->isNew().'</p>';
    }
}
