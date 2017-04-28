<?php

namespace FlorilFlowersBundle\Controller\Admin;

use FlorilFlowersBundle\Entity\Cart\CartProduct;
use FlorilFlowersBundle\Entity\Cart\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{

    /**
     * @Route("/admin/orders", name="show_incomplete_orders")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function showAdminNotCompletedOrders()
    {
        if ($this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {
            /** @var Order[] $allOrdersNotConfirmed */
            $allOrdersNotConfirmed = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Order')->findAllNotConfirmed();
            return $this->render(':FlorilFlowers/Admin/Orders:list.html.twig', array(
                'allOrders' =>$allOrdersNotConfirmed
            ));

        }else{
            $this->addFlash('error', "You don't have access to this page!");
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/admin/orders/{idOrder}/complete", name="confirm_order_process")
     * @Method("POST")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param $idOrder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function completeOrderAction($idOrder)
    {
        if ($this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {
            $order = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Order')->find($idOrder);
            $em = $this->getDoctrine()->getManager();
//            dump($order->getCart());exit;
            /** @var CartProduct $cartProduct */
            foreach ($order->getCart()->getCartProducts() as $cartProduct) {
                $owner = $cartProduct->getOffer()->getUser();
                $price = $this->get('app.price_calculator')->calculate($cartProduct->getOffer())* $cartProduct->getQuantity();
                $owner->earnMoneyWhenSelling($price);
                $em->persist($owner);
            }
            $order->setCompletedOn(new \DateTime());
            $em->persist($order);
            $em->flush();
            $this->addFlash('success', 'You successfully confirmed this order!');
            return $this->redirectToRoute('show_incomplete_orders');
        }else{
            $this->addFlash('error', "You don't have access to this page!");
            return $this->redirectToRoute('homepage');
        }
    }
}
