<?php

namespace FlorilFlowersBundle\Controller\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use FlorilFlowersBundle\Entity\Cart\Cart;
use FlorilFlowersBundle\Entity\Cart\CartProduct;
use FlorilFlowersBundle\Entity\Cart\Order;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\User\User;
use FlorilFlowersBundle\Form\Cart\CartTypeForm;
use FlorilFlowersBundle\Form\Cart\OrderFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ExpressionLanguage\Expression;

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
        if($this->getUser()->getId() == $id || $this->get('security.authorization_checker')->isGranted(new Expression(
                '"ROLE_ADMIN" in roles'
            ))){
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);

            /**
             * @var Cart $cart
             */
            $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->findCartByUser($user);
            if(!$cart || $cart[0]->getCartProducts()->count()==0){
                $this->addFlash('info', 'Your cart is empty!');
                return $this->redirectToRoute('products_list');
            }
            $formCart = $this->createForm(CartTypeForm::class, $cart[0]);
            $cartTotalSum = $this->get('app.cart_manager')->calculateCartTotalPrice($cart[0]);
            $priceCalculator = $this->get('app.price_calculator');
            return $this->render(':FlorilFlowers/Cart:current.html.twig', [
                'formCart' => $formCart->createView(),
                'cartTotalSum' => $cartTotalSum,
                'priceCalculator' => $priceCalculator
            ]);
        }

        $this->addFlash('info', 'You can only access your cart!');
        return $this->redirectToRoute('products_list');

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
        if($this->getUser()->getId() == $id || $this->get('security.authorization_checker')->isGranted(new Expression(
                '"ROLE_ADMIN" in roles'
            ))) {
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);

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
//        $originalCart = clone $cart;

            /**
             * @var CartProduct[]|ArrayCollection
             */
            $originalProducts = new ArrayCollection();
            foreach ($cart->getCartProducts() as $product){
                $originalProducts->add(clone $product);
//            dump($originalProducts[0]);exit;
            }

            $formCart = $this->createForm(CartTypeForm::class, $cart);
            $formCart->handleRequest($request);

            if($formCart->isValid() && $formCart->isSubmitted()){
//            dump($originalCart);
//            dump($cart);exit;
                $em = $this->getDoctrine()->getManager();
// the logic for checking if deletion of product from cart is needed (if user deleted product form the cart) and the deletion itself is in CartManager service
                $this->get('app.cart_manager')->deleteFromCartIfNeeded($cart, $originalProducts);
                $this->get('app.cart_manager')->editCartProductsQuantities($user, $cart, $originalProducts);

                $em->persist($cart);
                $em->flush();

                $this->addFlash('info', 'You just edited your cart!');
                $priceCalculator = $this->get('app.price_calculator');
                return $this->redirectToRoute('show_edit_current_cart', array(
                    'id' => $user->getId(),
                    'priceCalculator' => $priceCalculator
                ));
            }

            $this->addFlash('info', 'You entered invalid data!');
            return $this->redirectToRoute('show_edit_current_cart', array(
                'id' => $this->getUser()->getId()
            ));
        }

        $this->addFlash('info', 'You can only access your cart!');
        return $this->redirectToRoute('products_list');
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
        /**
         * @var $user User
         */
        $user = $this->getUser();
        if($productOffer && $productOffer->getUser()!= $this->getUser()){
            $this->get('app.cart_manager')->addToCart($user, $productOffer);

        }else{
            $this->addFlash('info', 'You cannot add this product!');
        }

        return $this->redirectToRoute('products_list');
    }

    /**
     * @Route("/user/{idUser}/cart/{idCart}/order", name="order_current_cart_process")
     * @Security("is_granted('ROLE_USER')")
     * @param $idUser
     * @param $idCart
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function finaliseCartAction($idUser, $idCart)
    {
        if($this->getUser()->getId() == $idUser
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){
            /**
             * @var User $user
             */
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($idUser);

            /**
             * @var Cart $cart
             */
            $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->find($idCart);

            $form = $this->createForm(OrderFormType::class,  null, array('user'=>$user));

            $order = $cart->getOrder();
            if(!$order){

                $em = $this->getDoctrine()->getManager();

                $order = new Order();
// we parse the form by hand
                $order->setUser($user);
                $order->setCart($cart);
                $order->setCreatedOn(new \DateTime());

                $em->persist($order);
                $cart->setOrder($order);
                $em->persist($cart);
                $em->flush();
            }

            $cartTotalSum = $this->get('app.cart_manager')->calculateCartTotalPrice($cart);
            $priceCalculator = $this->get('app.price_calculator');
            return $this->render(':FlorilFlowers/Cart:order.html.twig', array(
                'order' => $order,
                'cartTotalSum' => $cartTotalSum,
                'form' => $form->createView(),
                'priceCalculator' => $priceCalculator
            ));

        }
        $this->addFlash('info', 'You can finalise only your cart!');
        return $this->redirectToRoute('products_list');
    }

}
