<?php

namespace FlorilFlowersBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $query = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->getBestSellingProductsWithQuantities();
//        dump($query);exit;
//        $funFact = "I wrote this to check if *MARKDOWN* works! It's so cool! This must be *NEW*";
//
//        $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
//
//        $key = md5($funFact);
//        if($cache->contains($key)){
//            $funFact = $cache->fetch($key);
//        }else{
////            sleep(1);
//            $funFact = $this->get('markdown.parser')->transform($funFact);
//            $cache->save($key, $funFact);
//        }

        return $this->render('FlorilFlowers/index.html.twig', ['products' => $query]);
    }
}
