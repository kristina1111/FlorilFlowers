<?php

namespace FlorilFlowersBundle\Controller\Product;

use FlorilFlowersBundle\Entity\Product\ProductOfferReview;
use FlorilFlowersBundle\Form\Product\ProductOfferReviewFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductOfferReviewController extends Controller
{
    /**
     * @Route("/product/{id}/review/create", name="create_review")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function reviewCreateAction($id, Request $request)
    {
        $productOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($id);
        if (!$productOffer) {
            $this->addFlash('info', 'There is no such product!');
            return $this->redirectToRoute('products_list');
        }
        $form = $this->createForm(ProductOfferReviewFormType::class);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            /**
             * @var ProductOfferReview $review
             */
            $review = $form->getData();
            $review->setUser($this->getUser());
            $review->setProductOffer($productOffer);
            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'You just added new review!');
            return $this->redirectToRoute('product_show', array('id' => $productOffer->getId()));
        }

        $this->addFlash('info', 'Something went wrong!');
        return $this->redirectToRoute('product_show', array('id' => $productOffer->getId()));
    }

    /**
     * @Route("/product/{idProduct}/review/edit/{idReview}", name="edit_review")
     * @param $idProduct
     * @param $idReview
     * @param Request $request
     * @return Response
     */
    public function reviewEditAction($idProduct, $idReview, Request $request)
    {
//        dump($this->getUser()->isGranted("ROLE_EDITOR"));exit;
        $review = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOfferReview')->find($idReview);
        if($review->getUser() == $this->getUser() || $this->getUser()->isGranted("ROLE_EDITOR")){
            $productOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($idProduct);

            $form = $this->createForm(ProductOfferReviewFormType::class, $review);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($review);
                $em->flush();

                $this->addFlash('success', 'You just edited a review by ' . $review->getUser()->getNickname());
                return $this->redirectToRoute('product_show', array('id' => $idProduct));
            }
            $priceCalculator = $this->get('app.price_calculator');
            return $this->render(':FlorilFlowers/Product:show.html.twig',
                [
                    'productOffer'=> $productOffer,
                    'reviewForm' => $form->createView(),
                    'priceCalculator' => $priceCalculator
//                'reviews' => $product->getReviews(),
//                'recentNotes' => $recentNotes,
//                'funfact' => $funfact,
                ]);
        }else{
            $this->addFlash('error', 'You can edit only your comments');
            return $this->redirectToRoute('product_show', array('id' => $idProduct));
        }
    }
}
