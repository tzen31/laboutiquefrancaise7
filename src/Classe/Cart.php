<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(private RequestStack $requestStack) {

    }

    /*
    * add()
    * Fonction permettant l'ajout d'un produit au panier
    */
    public function add($product)
    {
        // Appeler la session CART de symfony
        $cart = $this->get('cart');

        //Ajouter une quantité +1 à mon produit
        if (isset($cart[$product->getId()])) {

            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
            
        } else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }

        // Créer ma session Cart
        $this->requestStack->getSession()->set('cart', $cart);       
    }

    public function get()
    {
        $session=$this->requestStack->getCurrentRequest()->getSession();

        return $session->get('cart');
    }

    /*
    * remove()
    * Fonction permettant de supprimer le panier
    */
    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }

    /*
    * decrease()
    * Fonction permettant la suppression d'une quantité d'un produit au panier
    */
    public function decrease($id)
    {
        $cart = $this->getCart();
        
        //Vérifier si la quantité de notre produit != 1
        if ($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] - 1;
        } else {
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }
    
    /*
    * getCart()
    * Fonction retournant le panier
    */
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }

    /*
     * fullQuantity()
     * Fonction retournant le nombre total de produit au panier
     */
    public function fullQuantity()
    {
        $cart = $this->getCart();
        $quantity = 0;

        if (!isset($cart)) {
            return $quantity;
        }

        foreach ($cart as $product) {
            $quantity = $quantity + $product['qty'];
        }

        return $quantity;
    }

    /*
     * getTotalWt()
     * Fonction retournant le prix total des produits au panier
     */
    public function getTotalWt()
    {
        $cart = $this->getCart();
        $price = 0;

        if (!isset($cart)) {
            return $price;
        }

        foreach ($cart as $product) {
            $price = $price + ($product['object']->getPriceWt() * $product['qty']);
        }

        return $price;
    }
}