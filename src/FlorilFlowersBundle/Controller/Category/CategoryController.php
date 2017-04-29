<?php

namespace FlorilFlowersBundle\Controller\Category;

use FlorilFlowersBundle\Entity\Category\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{id}", name="show_all_in_category")
     */
    public function showCategoryAction($id)
    {
        /**
         * @var Category $category
         */
        $category = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Category\Category')->find($id);
        if (!$category) {
            $this->addFlash('info', 'There are no such category!');
            return $this->redirectToRoute('homepage');
        }
        $priceCalculator = $this->get('app.price_calculator');
        return $this->render(':FlorilFlowers/Category:list.html.twig', array(
            'category' => $category,
            'priceCalculator' => $priceCalculator
        ));
    }
}
