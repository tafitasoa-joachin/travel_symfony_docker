<?php

namespace App\Twig;

use App\Repository\OfferRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice'])
        ];
    }

    public function formatPrice($number)
    {
        // Formater le nombre avec 2 décimales et utiliser une virgule comme séparateur décimal
        return number_format($number, decimals: '0', decimal_separator: '.') . ' Ar';
    }

    public function getGlobals(OfferRepository $offerRepository): array
    {
        return [
            'allOffers' => $offerRepository->findAll(),
        ];
    }
}
