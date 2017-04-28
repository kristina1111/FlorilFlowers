<?php

namespace FlorilFlowersBundle\Controller\Order;

use FlorilFlowersBundle\Entity\Cart\Cart;
use FlorilFlowersBundle\Entity\Cart\Order;
use FlorilFlowersBundle\Entity\User\User;
use FlorilFlowersBundle\Form\Cart\OrderFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    /**
     * @Route("/user/profile/{id}/orders", name="user_all_orders_show")
     */
    public function showAllUserOrdersAction($id)
    {
        if($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {

            /** @var Order[] $notConfirmedOrders */
            $notConfirmedOrders = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Order')->findByUserAndNotConfirmed($this->getUser());
            /** @var Order[] $confirmedButNotCompletedOrders */
            $confirmedButNotCompletedOrders = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Order')->findByUserAndConfirmedNotCompleted($this->getUser());
            /** @var Order[] $completedOrders */
            $completedOrders = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Order')->findByUserAndCompleted($this->getUser());

            return $this->render(':FlorilFlowers/Cart:user-all-orders.html.twig', array(
                'user' => $this->getUser(),
                'notConfirmedOrdersByDatetime' => $notConfirmedOrders,
                'confirmedNotCompletedOrdersByDatetime' =>$confirmedButNotCompletedOrders,
                'completedOrders' => $completedOrders
            ));



        }
        return $this->redirectToRoute('user_edit_profile', array(
            'id' => $this->getUser()->getId()
        ));
//        return $this->redirectToRoute('user_profile_show');
    }



    /**
     * @Route("/user/{idUser}/cart/{idCart}/order/edit", name="edit_order_before_finalised")
     * @Method("POST")
     * @Security("is_granted('ROLE_USER')")
     * @param $idUser
     * @param $idCart
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editFinalisedCartAction($idUser, $idCart)
    {
        if($this->getUser()->getId() == $idUser
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){
            /**
             * @var Cart $cart
             */
            $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->find($idCart);

            $order = $cart->getOrder();
//            dump($order);exit;

            if(!!$order){
                if($order->getConfirmedOn()===null){
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($order);
                    $em->flush();

                    $this->addFlash('info', 'You can edit your cart! After finishing, finalise your order!');
                }else{
                    $this->addFlash('info', 'You cannot edit finalised order!');
                }
            }else{
                $this->addFlash('info', "You don't have any order to edit!");
            }

        }else{
            $this->addFlash('info', "You can edit only your order!");
        }
        return $this->redirectToRoute('show_edit_current_cart', array(
            'id' => $this->getUser()->getId()
        ));
    }

    /**
     * @Route("/user/{idUser}/cart/{idCart}/order/confirm", name="confirm_order_current_cart_process")
     * @Method("POST")
     * @Security("is_granted('ROLE_USER')")
     * @param $idUser
     * @param $idCart
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmOrderAction($idUser, $idCart, Request $request)
    {
        if($this->getUser()->getId() == $idUser
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){

            /** @var User $user */
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($idUser);

            /** @var Cart $cart */
            $cart = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Cart')->find($idCart);
            /** @var Order $order */
            $order = $cart->getOrder();
            if($order->getConfirmedOn()!=null){
                $this->addFlash('success', "You have already confirmed that order!");
                return $this->redirectToRoute('products_list');
            }
            $form = $this->createForm(OrderFormType::class, null, array('user'=>$user));
            $form->handleRequest($request);
//            dump($form->isValid());exit;
            if($form->isValid() && $form->isSubmitted()){
                $em = $this->getDoctrine()->getManager();
//                dump($form->get('address')->getData());exit;

                if($form->get('address')->getData()!=null || $form->get('addresses')->getData()!= null){
                    if($form->get('address')->getData()!=null){
                        $order->setAddress($form->get('address')->getData());
                        $order->getAddress()->setUser($user);
                        $em->persist($order->getAddress());
                    }else{
                        $order->setAddress($form->get('addresses')->getData());
                    }
                }else{
                    $this->addFlash('error', 'One of the address fields must be completed!');
                    return $this->redirectToRoute('order_current_cart_process', array(
                        'idUser' => $this->getUser()->getId(),
                        'idCart' => $cart->getId()
                    ));
                }

                if($form->get('phone')->getData()!=null || $form->get('phones')->getData()!= null){
                    if($form->get('phone')->getData()!=null){
                        $order->setPhone($form->get('phone')->getData());
                        $order->getPhone()->setUser($user);
                        $em->persist($order->getPhone());
                    }else{
                        $order->setPhone($form->get('phones')->getData());
                    }
                }else{
                    $this->addFlash('error', 'One of the phone fields must be completed!');
                    return $this->redirectToRoute('order_current_cart_process', array(
                        'idUser' => $this->getUser()->getId(),
                        'idCart' => $cart->getId()
                    ));
                }

//                $order;
//                dump($order);exit;
                $order->setConfirmedOn(new \DateTime());
                $totalSum = $this->get('app.cart_manager')->calculateCartTotalPrice($cart);
                $user->setCash($user->getCash()-$totalSum);
//                dump($order);exit;

                $em->persist($order);
                $em->persist($order->getAddress());
                $em->persist($order->getPhone());
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', "You confirmed your order! You can see it's status from your profile! You have "
                    . $user->getCash() . "BGN money left!");
                return $this->redirectToRoute('products_list');
            }
            return $this->redirect('order_current_cart_process', array(
                'idUser' => $this->getUser()->getId(),
                'idCart' => $cart->getId()
            ));
//            return $this->render(':FlorilFlowers/Cart:order.html.twig', array(
//                'order' => $order,
//                'cartTotalSum' => $totalSum,
//                'form' => $form->createView()
//            ));

        }
        return $this->redirectToRoute('homepage');
    }

}
