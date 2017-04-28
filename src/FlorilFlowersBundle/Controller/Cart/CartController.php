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
//            /**
//             * @var CartProduct $originalProduct
//             */
//            foreach ($originalProducts as $originalProduct){
////     originalProducts contains clones and their existence in $cart->getCartProducts() cannot be checked with "contains"
//                $criteria = Criteria::create()
//                    ->where(Criteria::expr()->eq('id', $originalProduct->getId()))
//                    ->setFirstResult(0)
//                    ->setMaxResults(1);
//                /**
//                 * @var CartProduct $originalProduct
//                 */
//                if($cart->getCartProducts()->matching($criteria)->count()==0){
//                    $productForDeletion = $em->find(CartProduct::class, $originalProduct->getId());
//                    $em->remove($productForDeletion);
//
//                    // for updating product available quantities after deleting a product form cart
//                    $num = $em->getRepository('FlorilFlowersBundle:Cart\CartProduct')->findByCartAndProductOffer($cart, $productForDeletion->getOffer())[0]->getQuantity();
//                    $productForDeletion->getOffer()->increaseQuantityForSale($num);
//                }
//            }
                $this->get('app.cart_manager')->editCartProductsQuantities($user, $cart, $originalProducts);
//            /**
//             * @var CartProduct $product
//             */
//            foreach ($cart->getCartProducts() as $product){
//                $criteria = Criteria::create()
//                    ->where(Criteria::expr()->eq('id',$product->getId()))
//                    ->setFirstResult(0)
//                    ->setMaxResults(1);
//
//                $originalProduct = $originalProducts->matching($criteria)[0];
//
//                if($originalProduct->getQuantity() != $product->getQuantity()){
//                    if($product->getQuantity()>0){
//                        $quantityAvailable = $product->getOffer()->getQuantityForSale();
//                        $diff = $product->getQuantity() - $originalProduct->getQuantity();
//                        if($quantityAvailable>=$diff){
//                            $product->getOffer()->decreaseQuantityForSale($diff);
//                        }else{
//                            $product->setQuantity($originalProduct->getQuantity());
//                            $this->addFlash('info', 'Available quantity for sale of product '
//                                . $product->getOffer()->getProduct()->getName() . ' is ' . $product->getOffer()->getQuantityForSale()
//                                . '! You cannot add more that to your cart');
////                        return $this->redirectToRoute('show_edit_current_cart', array(
////                            'id' => $user->getId()
////                        ));
//                        }
//                    }else{
//
//                    }
//
//                }
//                $em->persist($product);
//            }
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
//            $em = $this->getDoctrine()->getManager();
//
//            /**
//             * @var $user User
//             */
//            $user = $this->getUser();
//
//            $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->findCartByUser($user);
////            dump(!$cart);exit;
//            if(!$cart){
//                $cart = new Cart();
//                $cart->setUser($user);
//
//                $user->getCarts()->add($cart);
//                $em->persist($cart);
//                $em->flush();
//            }else{
////                The query returns array. If it is not null we are sure that it returns only one match
//                $cart = $cart[0];
//            }
////            dump($cart);exit;
//            $cartProduct = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\CartProduct')->findByCartAndProductOffer($cart, $productOffer);
////            dump($cartProduct);exit;
//            if(!$cartProduct){
//                $cartProduct = new CartProduct();
//                $cartProduct->setOffer($productOffer);
//                $cartProduct->setQuantity(1);
//                $cartProduct->setCart($cart);
//
//                // for decreasing available quantity for sale of the addet to cart product
//                $productOffer->decreaseQuantityForSale(1);
//
//                $em->persist($cartProduct);
//                $em->persist($productOffer);
//
//                $cart->getCartProducts()->add($cartProduct);
//                $em->persist($cart);
//                $em->flush();
//
//                $this->addFlash('info', 'You just added product to your cart!');
//            }else{
//                $this->addFlash('info', 'You alreary have this product in your cart!');
//            }
//            dump($cart);exit;
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
            $user = $this->getUser();

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

//    /**
//     * @Route("/user/{idUser}/cart/{idCart}/order/edit", name="edit_order_before_finalised")
//     * @Method("POST")
//     * @Security("is_granted('ROLE_USER')")
//     * @param $idUser
//     * @param $idCart
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     */
//    public function editFinalisedCartAction($idUser, $idCart)
//    {
//        if($this->getUser()->getId() == $idUser
//            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){
//            /**
//             * @var Cart $cart
//             */
//            $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->find($idCart);
//
//            $order = $cart->getOrder();
////            dump($order);exit;
//
//            if(!!$order){
//                if($order->getCompletedOn()===null){
//                    $em = $this->getDoctrine()->getManager();
//                    $em->remove($order);
//                    $em->flush();
//
//                    $this->addFlash('info', 'You can edit your cart! After finishing, finalise your order!');
//                }else{
//                    $this->addFlash('info', 'You cannot edit finalised order!');
//                }
//            }else{
//                $this->addFlash('info', "You don't have any order to edit!");
//            }
//
//        }else{
//            $this->addFlash('info', "You can edit only your order!");
//        }
//        return $this->redirectToRoute('show_edit_current_cart', array(
//            'id' => $this->getUser()->getId()
//        ));
//    }
//
//    /**
//     * @Route("/user/{idUser}/cart/{idCart}/order/confirm", name="confirm_order_current_cart_process")
//     * @Method("POST")
//     * @Security("is_granted('ROLE_USER')")
//     * @param $idUser
//     * @param $idCart
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     */
//    public function confirmOrderAction($idUser, $idCart, Request $request)
//    {
//        if($this->getUser()->getId() == $idUser
//            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){
//
//            /** @var User $user */
//            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($idUser);
//
//            /** @var Cart $cart */
//            $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->find($idCart);
//            $totalSum = $this->get('app.cart_manager')->calculateCartTotalPrice($cart);
//            /** @var Order $order */
//            $order = $cart->getOrder();
//            if($order->getConfirmedOn()!=null){
//                $this->addFlash('success', "You have already confirmed that order!");
//                return $this->redirectToRoute('products_list');
//            }
//            $form = $this->createForm(OrderFormType::class, null, array('user'=>$user));
//            $form->handleRequest($request);
////            dump($form->isValid());exit;
//            if($form->isValid() && $form->isSubmitted()){
//                $em = $this->getDoctrine()->getManager();
////                dump($form->get('address')->getData());exit;
//
//                if($form->get('address')->getData()!=null || $form->get('addresses')->getData()!= null){
//                    if($form->get('address')->getData()!=null){
//                        $order->setAddress($form->get('address')->getData());
//                        $order->getAddress()->setUser($user);
//                        $em->persist($order->getAddress());
//                    }else{
//                        $order->setAddress($form->get('addresses')->getData());
//                    }
//                }else{
//                    $this->addFlash('error', 'One of the address fields must be completed!');
//                    return $this->redirectToRoute('order_current_cart_process', array(
//                        'idUser' => $this->getUser()->getId(),
//                        'idCart' => $cart->getId()
//                    ));
//                }
//
//                if($form->get('phone')->getData()!=null || $form->get('phones')->getData()!= null){
//                    if($form->get('phone')->getData()!=null){
//                        $order->setPhone($form->get('phone')->getData());
//                        $order->getPhone()->setUser($user);
//                        $em->persist($order->getPhone());
//                    }else{
//                        $order->setPhone($form->get('phones')->getData());
//                    }
//                }else{
//                    $this->addFlash('error', 'One of the phone fields must be completed!');
//                    return $this->redirectToRoute('order_current_cart_process', array(
//                        'idUser' => $this->getUser()->getId(),
//                        'idCart' => $cart->getId()
//                    ));
//                }
//
////                $order;
////                dump($order);exit;
//                $order->setConfirmedOn(new \DateTime());
//
//                $user->setCash($user->getCash()-$totalSum);
////                dump($order);exit;
//
//                $em->persist($order);
//                $em->persist($order->getAddress());
//                $em->persist($order->getPhone());
//                $em->persist($user);
//                $em->flush();
//
//                $this->addFlash('success', "You confirmed your order! You can see it's status from your profile! You have "
//                . $user->getCash() . "BGN money left!");
//                return $this->redirectToRoute('products_list');
//            }
//            return $this->redirect('order_current_cart_process', array(
//                'idUser' => $this->getUser()->getId(),
//                'idCart' => $cart->getId()
//            ));
////            return $this->render(':FlorilFlowers/Cart:order.html.twig', array(
////                'order' => $order,
////                'cartTotalSum' => $totalSum,
////                'form' => $form->createView()
////            ));
//
//        }
//        return $this->redirectToRoute('homepage');
//    }

}
