<?php

namespace FlorilFlowersBundle\Controller\Order;

use FlorilFlowersBundle\Entity\Cart\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Expression;

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
}
