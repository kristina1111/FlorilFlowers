<?php

namespace FlorilFlowersBundle\Controller\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FlorilFlowersBundle\Entity\Cart\Cart;
use FlorilFlowersBundle\Entity\Cart\CartProduct;
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
     * @Route("/user/cart", name="show_edit_current_cart")
     * @Method("GET")
     * @Security("is_granted('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showEditCartAction()
    {
        $user = $this->getUser();
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
     * @Route("/user/cart", name="show_edit_current_cart_process")
     * @Method("POST")
     * @Security("is_granted('ROLE_USER')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showEditCartActionProcess(Request $request)
    {
        $user = $this->getUser();
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
                if(false === $cart->getCartProducts()->contains($originalProduct)){
                    $em->remove($originalProduct);
                }
            }

            foreach ($cart->getCartProducts() as $product){
//                dump($product);exit;
                $em->persist($product);
            }
            $em->persist($cart);
            $em->flush();

            $this->addFlash('info', 'You just edited your cart!');
            return $this->redirectToRoute('show_edit_current_cart');
        }

        $this->addFlash('info', 'You entered invalid data!');
        return $this->redirectToRoute('show_edit_current_cart');
    }


    /**
     * @Route("/product/{id}/addtocart", name="product_add_to_cart")
     * @Security("is_granted('ROLE_USER')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCartAction($id)
    {
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

                $em->persist($cartProduct);

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
