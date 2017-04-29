<?php

namespace FlorilFlowersBundle\Controller\Admin;

use FlorilFlowersBundle\Entity\Promotion\Promotion;
use FlorilFlowersBundle\Form\Promotion\PromotionFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;

class PromotionController extends Controller
{
    /**
     * @Route("/admin/promotions/all", name="show_all_promotions")
     * @Security("is_granted('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllAction()
    {
        if ($this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {
            /** @var Promotion[] $promotions */
            $promotions = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Promotion\Promotion')->getAllOrderedByDate();
//            dump($promotions);exit;
            return $this->render(':FlorilFlowers/Admin/Promotion:all.html.twig', array(
                'promotions' => $promotions
            ));
        } else {
            $this->addFlash('error', "You don't have access to that page!");
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/admin/promotions/create", name="create_promotion")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createPromotionAction()
    {
        if ($this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {
            $form = $this->createForm(PromotionFormType::class);
            return $this->render(':FlorilFlowers/Admin/Promotion:create.html.twig', array(
                'form' => $form->createView()
            ));
        } else {
            $this->addFlash('error', "You don't have access to that page!");
            return $this->redirectToRoute('homepage');
        }

    }

    /**
     * @Route("/admin/promotions/create", name="create_promotion_process")
     * @Method("POST")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createPromotionActionProcess(Request $request)
    {
        $form = $this->createForm(PromotionFormType::class);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            /** @var Promotion $promotion */
            $promotion = $form->getData();
            if($promotion->getStartDate()>=$promotion->getEndDate()){
                $this->addFlash('success', 'Promotion start date cannot be greater than promotion end date!');
                return $this->render(':FlorilFlowers/Admin/Promotion:create.html.twig', array(
                    'form' => $form->createView()
                ));
            }
            if(($promotion->getPercent()<=0 && $promotion->getPercent()>100)
                || filter_var($promotion->getPercent(), FILTER_VALIDATE_INT)){
                $this->addFlash('success', 'Promotion percent must be whole number between 1 and 100!');
                return $this->render(':FlorilFlowers/Admin/Promotion:create.html.twig', array(
                    'form' => $form->createView()
                ));
            }
            $promotion->setUser($this->getUser());
//            dump($promotion);exit;
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();

            $this->addFlash('success', 'You successfully created a promotion!');

            return $this->redirectToRoute('show_all_promotions');
        } else {
            return $this->render(':FlorilFlowers/Admin/Promotion:create.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/admin/promotions/{id}/edit", name="edit_promotion")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editPromotion($id, Request $request)
    {
        $promotion = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Promotion\Promotion')->find($id);
        if($promotion){
            $form = $this->createForm(PromotionFormType::class, $promotion);

            $form->handleRequest($request);
            if($form->isValid() && $form->isSubmitted()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($promotion);
                $em->flush();

                $this->addFlash('success', 'You successfully edited a promotion!');

                return $this->redirectToRoute('show_all_promotions');
            }
            return $this->render(':FlorilFlowers/Admin/Promotion:create.html.twig', array(
                'form' => $form->createView()
            ));
        }else{
            $this->addFlash('error', 'You such promotion exists!');

            return $this->redirectToRoute('show_all_promotions');
        }

    }

    /**
     * @Route("/admin/promotions/{id}/delete", name="delete_promotion")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePromotion($id)
    {
        $promotion = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Promotion\Promotion')->find($id);
        if($promotion){
            $em = $this->getDoctrine()->getManager();
            $em->remove($promotion);
            $em->flush();

            $this->addFlash('success', 'You successfully deleted a promotion!');

            return $this->redirectToRoute('show_all_promotions');
        }else{
            $this->addFlash('error', 'You such promotion exists!');

            return $this->redirectToRoute('show_all_promotions');
        }
    }
}
