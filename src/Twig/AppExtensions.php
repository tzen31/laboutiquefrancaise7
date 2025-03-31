<?php

namespace App\Twig;

use App\Classe\Cart;
use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;
    private $cart;

    public function __construct(CategoryRepository $categoryRepository, Cart $cart){
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;
    }

    public function getFilters() {        
        return [ 
            new TwigFilter( 'price',  [$this, 'formatPrice'])
            ];
    } 

    public function formatPrice($number){
        return number_format($number, '2',',', ' '). ' €';
    }

    public function getGlobals(): array
    {
        return [
            'allCategories' => $this->categoryRepository->findAll(),
            'fullCartQuantity' => $this->cart->fullQuantity()
        ];
    }

}


