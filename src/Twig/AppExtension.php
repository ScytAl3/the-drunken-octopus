<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;

class AppExtension extends AbstractExtension
{
    /**
     * 
     * @var RequestStack
     */
    private $requestStack;

    /**
     * 
     * @param RequestStack $requestStack 
     * @return void 
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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

    public function setActiveRoute(string $route): string
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        return $currentRoute == $route ? 'active' : '' ;
    }
}
