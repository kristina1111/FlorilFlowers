<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.4.2017 г.
 * Time: 14:42 ч.
 */

namespace FlorilFlowersBundle\Service\Cart;


use Doctrine\ORM\EntityManager;
use FlorilFlowersBundle\Entity\Cart\Cart;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\User\User;
use Symfony\Component\HttpFoundation\Session\Session;

class CartCalculator
{
//    private $em;
//    private $session;
//
//    public function __construct(EntityManager $em, Session $session)
//    {
//        $this->em = $em;
//        $this->session = $session;
//    }

//    public function calculateCartTotalPrice(Cart $cart){
//        $totalSum = 0;
//        foreach ($cart->getCartProducts() as $cartProduct){
//            $totalSum+=$cartProduct->getQuantity()*$cartProduct->getOffer()->getRetailPrice()*$cartProduct->getOffer()->getCurrency()->getExchangeRate();
//        }
//
//        return $totalSum;
//
//    }
//
//    public function hasEnoughMoney(User $user, Cart $cart, ProductOffer $productOffer, int $number = 1)
//    {
//        return ($user->getCash()-$this->calculateCartTotalPrice($cart))>=$productOffer->getRetailPrice()*$number;
//
//    }
}