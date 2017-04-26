<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.4.2017 г.
 * Time: 11:17 ч.
 */

namespace FlorilFlowersBundle\Service\Cart;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use FlorilFlowersBundle\Entity\Cart\Cart;
use FlorilFlowersBundle\Entity\Cart\CartProduct;
use FlorilFlowersBundle\Entity\Cart\Order;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\User\User;
use Symfony\Component\HttpFoundation\Session\Session;

class CartManager
{
    private $em;
    private $session;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function calculateCartTotalPrice(Cart $cart){
        $totalSum = 0;
        foreach ($cart->getCartProducts() as $cartProduct){
            $totalSum+=$cartProduct->getQuantity()*$cartProduct->getOffer()->getRetailPrice()*$cartProduct->getOffer()->getCurrency()->getExchangeRate();
        }

        return $totalSum;

    }

    private function hasEnoughMoney(User $user, Cart $cart, ProductOffer $productOffer, int $number = 1)
    {
//        $number = 1 is needed because it indicates that this is initial adding of a product to the cart
//        and that only one product is added
//        If number is set to 1, this indicates that this is editing of a product in the cart and
//        it is not known how many products would be added
        return ($user->getCash() - $this->calculateCartTotalPrice($cart))>= $productOffer->getRetailPrice()*$number;
    }

    private function deleteFromCart(Cart $cart, CartProduct $productForDeletion, CartProduct $originalProduct)
    {
        // for updating product available quantities after deleting a product form cart
        $num = $originalProduct->getQuantity();

        $productOffer = $this->em->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($productForDeletion->getOffer()->getId());
        $productOffer->increaseQuantityForSale($num);

        $this->em->persist($productOffer);

        $this->em->remove($productForDeletion);

    }

    public function deleteFromCartIfNeeded(Cart $cart, ArrayCollection $originalProducts)
    {
        /**
         * @var CartProduct $originalProducts
         */
        foreach ($originalProducts as $originalProduct){
//     originalProducts contains clones and their existence in $cart->getCartProducts() cannot be checked with "contains"
            $criteria = Criteria::create()
                ->where(Criteria::expr()->eq('id', $originalProduct->getId()))
                ->setFirstResult(0)
                ->setMaxResults(1);
            /**
             * @var CartProduct $originalProduct
             */
            if($cart->getCartProducts()->matching($criteria)->count()==0){
                $productForDeletion = $this->em->find(CartProduct::class, $originalProduct->getId());
                $this->deleteFromCart($cart, $productForDeletion, $originalProduct);
                $this->em->flush();
//                $this->em->remove($productForDeletion);
//
//                // for updating product available quantities after deleting a product form cart
//                $num = $this->em->getRepository('FlorilFlowersBundle:Cart\CartProduct')->findByCartAndProductOffer($cart, $productForDeletion->getOffer())[0]->getQuantity();
//                $productForDeletion->getOffer()->increaseQuantityForSale($num);
            }
        }
    }



    public function editCartProductsQuantities(User $user, Cart $cart, ArrayCollection $originalProducts)
    {
        /**
         * @var CartProduct $product
         */
        foreach ($cart->getCartProducts() as $product){
//     originalProducts contains clones and their existence in $cart->getCartProducts() cannot be checked with "contains"
            $criteria = Criteria::create()
                ->where(Criteria::expr()->eq('id',$product->getId()))
                ->setFirstResult(0)
                ->setMaxResults(1);

            /**
             * @var CartProduct $originalProduct
             */
            $originalProduct = $originalProducts->matching($criteria)[0];

            if($originalProduct->getQuantity() != $product->getQuantity()){
//                dump($originalProduct);exit;
                if($product->getQuantity()>0){
//                    dump($product->getQuantity());exit;
                    $quantityAvailable = $product->getOffer()->getQuantityForSale();
                    $diff = $product->getQuantity() - $originalProduct->getQuantity();
                    if($quantityAvailable>=$diff){
//                        check if user has enough money to afford this quantity of products before adding it to cart
                        if($this->hasEnoughMoney($user, $cart, $originalProduct->getOffer(), 0)){
//                            dump($diff);exit;
                            $product->getOffer()->decreaseQuantityForSale($diff);
                        }else{
                            $product->setQuantity($originalProduct->getQuantity());
                            $this->session->getFlashBag()
                                ->add('info', "You don't have enough money to buy "
                                    . $diff . ' more of product '
                                    . $product->getOffer()->getProduct()->getName()
                                    . '! You have '
                                    . number_format($user->getCash() - $this->calculateCartTotalPrice($cart), 2)
                                    . ' BGN left!'
                                );
                        }

                    }else{
                        $product->setQuantity($originalProduct->getQuantity());
                        $this->session->getFlashBag()->add('info', 'Available quantity for sale of product '
                            . $product->getOffer()->getProduct()->getName() . ' is ' . $product->getOffer()->getQuantityForSale()
                            . '! You cannot add more to your cart');
                    }
                    $this->em->persist($product);
                }else{
                    $this->deleteFromCart($cart, $product, $originalProduct);
                }

            }
        }
    }

    public function addToCart(User $user, ProductOffer $productOffer)
    {


        /** @var Order $order */
        $order = $this->em->getRepository('FlorilFlowersBundle:Cart\Order')->findByUserAndNotConfirmed($user);
        if(!!$order){
//            This logic is in case user already has order which is not confirmed and without passing through edit,
// they want to add more products to the cart   ?!?! why not call editFinalisedCartAction function in CartController
            $this->em->remove($order[0]);
            $cart = $order[0]->getCart();
            $cart->setOrder(null);
            $this->em->persist($cart);
            $this->em->flush($order[0]);
//            return $this->addToCart($user, $productOffer);
        }

        $cart = $this->em->getRepository('FlorilFlowersBundle:Cart\Cart')->findCartByUser($user);

//            dump(!$cart);exit;
        if(!$cart){
            $cart = new Cart();
            $cart->setUser($user);

            $user->getCarts()->add($cart);
            $this->em->persist($cart);
            $this->em->flush();
        }else{
//                The query returns array. If it is not null we are sure that it returns only one match
            $cart = $cart[0];
        }
//            dump($cart);exit;
        $cartProduct = $this->em->getRepository('FlorilFlowersBundle:Cart\CartProduct')->findByCartAndProductOffer($cart, $productOffer);
//            dump($cartProduct);exit;
        if(!$cartProduct){
            if($this->hasEnoughMoney($user, $cart, $productOffer)){
                $cartProduct = new CartProduct();
                $cartProduct->setOffer($productOffer);
                $cartProduct->setQuantity(1);
                $cartProduct->setCart($cart);

                // for decreasing available quantity for sale of the addet to cart product
                $productOffer->decreaseQuantityForSale(1);

                $this->em->persist($cartProduct);
                $this->em->persist($productOffer);

                $cart->getCartProducts()->add($cartProduct);
                $this->em->persist($cart);
                $this->em->flush();

                $this->session->getFlashBag()->add('info', 'You just added product to your cart!');
            }else{

                $this->session->getFlashBag()
                    ->add('info', "You don't have enough money to buy add this "
                        . $productOffer->getProduct()->getName()
                        . ' to your cart! You have '
                        . number_format($user->getCash() - $this->calculateCartTotalPrice($cart), 2)
                        . ' BGN left!'
                    );
            }

        }else{
            $this->session->getFlashBag()->add('info', 'You already have this product in your cart!');
        }
    }
}