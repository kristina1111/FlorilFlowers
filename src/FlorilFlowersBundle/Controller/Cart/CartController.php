<?php

namespace FlorilFlowersBundle\Controller\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FlorilFlowersBundle\Entity\Cart\Cart;
use FlorilFlowersBundle\Entity\Cart\CartProduct;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\User\User;
use FlorilFlowersBundle\Form\Cart\CartTypeForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    /**
     * @Route("/user/{id}/cart", name="show_edit_current_cart")
     * @Method("GET")
     * @Security("is_granted('ROLE_USER')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showEditCartAction($id)
    {
        $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);
        if(!$user){
            $this->addFlash('info', 'You cannot see this cart!');
            return $this->redirectToRoute('products_list');
        }

        /**
         * @var Cart $cart
         */
        $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->findCartByUser($user);
        if(!$cart || $cart[0]->getCartProducts()->count()==0){
            $this->addFlash('info', 'Your cart is empty!');
            return $this->redirectToRoute('products_list');
        }
        $formCart = $this->createForm(CartTypeForm::class, $cart[0]);
        return $this->render(':FlorilFlowers/Cart:current.html.twig', [
            'formCart' => $formCart->createView()
        ]);
    }

    /**
     * @Route("/user/{id}/cart", name="show_edit_current_cart_process")
     * @Method("POST")
     * @Security("is_granted('ROLE_USER')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showEditCartActionProcess($id, Request $request)
    {
        $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);
        if(!$user){
            $this->addFlash('info', 'You cannot see this cart!');
            return $this->redirectToRoute('products_list');
        }

        /**
         * @var Cart $cart
         */
        $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->findCartByUser($user);
        if(!$cart || $cart[0]->getCartProducts()->count()==0){
            $this->addFlash('info', 'Your cart is empty!');
            return $this->redirectToRoute('products_list');
        }
        $cart = $cart[0];

//        this is necessary for the removal of products from the cart
        /**
         * @var CartProduct[]|ArrayCollection
         */
        $originalProducts = new ArrayCollection();
        foreach ($cart->getCartProducts() as $product){
            $originalProducts->add($product);
        }

        $formCart = $this->createForm(CartTypeForm::class, $cart);
        $formCart->handleRequest($request);
//dump($formCart->isValid());exit;
        if($formCart->isValid() && $formCart->isSubmitted()){
            $em = $this->getDoctrine()->getManager();

            foreach ($originalProducts as $originalProduct){
                /**
                 * @var CartProduct $originalProduct
                 */
                if(false === $cart->getCartProducts()->contains($originalProduct)){
                    $em->remove($originalProduct);

                    // for updating product available quantities after deleting a product form cart
                    $num = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\CartProduct')->findByCartAndProductOffer($cart, $originalProduct->getOffer())[0]->getQuantity();
                    $originalProduct->getOffer()->increaseQuantityForSale($num);
                }
            }

            foreach ($cart->getCartProducts() as $product){
//                dump($product);exit;
                $em->persist($product);
            }
            $em->persist($cart);
            $em->flush();

            $this->addFlash('info', 'You just edited your cart!');
            return $this->redirectToRoute('show_edit_current_cart', array(
                'id' => $user->getId()
            ));
        }

        $this->addFlash('info', 'You entered invalid data!');
        return $this->redirectToRoute('show_edit_current_cart', array(
            'id' => $this->getUser()->getId()
        ));
    }


    /**
     * @Route("/product/{id}/addtocart", name="product_add_to_cart")
     * @Security("is_granted('ROLE_USER')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCartAction($id)
    {
        /**
         * @var ProductOffer $productOffer
         */
        $productOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($id);
        if($productOffer){
            $em = $this->getDoctrine()->getManager();
            /**
             * @var $user User
             */
            $user = $this->getUser();

            $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->findCartByUser($user);
//            dump(!$cart);exit;
            if(!$cart){
                $cart = new Cart();
                $cart->setUser($user);

                $user->getCarts()->add($cart);
                $em->persist($cart);
                $em->flush();
            }else{
//                The query returns array. If it is not null we are sure that it returns only one match
                $cart = $cart[0];
            }
//            dump($cart);exit;
            $cartProduct = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\CartProduct')->findByCartAndProductOffer($cart, $productOffer);
//            dump($cartProduct);exit;
            if(!$cartProduct){
                $cartProduct = new CartProduct();
                $cartProduct->setOffer($productOffer);
                $cartProduct->setQuantity(1);
                $cartProduct->setCart($cart);

                // for decreasing available quantity for sale of the addet to cart product
                $productOffer->decreaseQuantityForSale(1);

                $em->persist($cartProduct);
                $em->persist($productOffer);

                $cart->getCartProducts()->add($cartProduct);
                $em->persist($cart);
                $em->flush();

                $this->addFlash('info', 'You just added product to your cart!');
            }else{
                $this->addFlash('info', 'You alreary have this product in your cart!');
            }
//            dump($cart);exit;
        }else{
            $this->addFlash('info', 'You cannot add this product!');
        }

        return $this->redirectToRoute('products_list');
    }


}
