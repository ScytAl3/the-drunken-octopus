<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
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
        ];
    }

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
}
