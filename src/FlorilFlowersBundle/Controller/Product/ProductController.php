<?php

namespace FlorilFlowersBundle\Controller\Product;

use FlorilFlowersBundle\Entity\Product\Product;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Form\Product\ProductFormType;
use FlorilFlowersBundle\Form\Product\ProductOfferFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("admin/products")
 */
class ProductController extends Controller
{

    /**
     * @Route("/new", name="create_product")
     * @Security("is_granted('ROLE_EDITOR')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
//        $form = $this->createForm(ProductFormType::class);
        $form = $this->createForm(ProductOfferFormType::class);

//        dump($form);exit;

        // only handles data in POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
//            dump($form->getData());exit;
            /**
             * @var $productOffer ProductOffer
             */
            $productOffer = $form->getData();
            $productOffer->setUser($this->getUser());
//            dump($productOffer);exit;
            $em = $this->getDoctrine()->getManager();
            $em->persist($productOffer->getProduct());
            foreach ($productOffer->getProductPrices() as $price){
                $price->setProductOffer($productOffer);
                $em->persist($price);
            }
            $em->persist($productOffer);
            $em->flush();

            $this->addFlash('success', 'You created a new product!');
            return $this->redirectToRoute('products_list');
        }

        return $this->render(':FlorilFlowers/Product:create.html.twig', ['productForm' => $form->createView()]);

    }

    /**
     * @Route("/products/edit/{id}", name="edit_product")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if($product){
            $form = $this->createForm(ProductFormType::class, $product);

            return $this->render(':FlorilFlowers/Product:edit.html.twig', ['productForm' => $form->createView()]);
        }

        $this->addFlash('info', 'There is no such product!');
        return $this->redirectToRoute('products_list');
    }

    /**
     * @Route("/products/edit/{id}", name="edit_product_process")
     * @Method("POST")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editActionProcess($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if($product){
            $form = $this->createForm(ProductFormType::class, $product);
            $form->handleRequest($request);
            if($form->isValid() && $form->isSubmitted()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                $this->addFlash('info', 'You just edited a product!');
                return $this->redirectToRoute('products_list');
            }

            return $this->render(':FlorilFlowers/Product:edit.html.twig', ['productForm' => $form->createView()]);
        }

        $this->addFlash('info', 'There is no such product!');
        return $this->redirectToRoute('products_list');
    }

    /**
     * @Route("/products/{id}", name="product_show")
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('FlorilFlowersBundle:Product\Product')->findOneBy(['id' => $id]);

        if(!$product){
            throw $this->createNotFoundException('No product found!');
        }

        $recentNotes = $em->getRepository('FlorilFlowersBundle:Product\ProductReview')
            ->findAllRecentNotesForProduct($product);

        $funfact = "**CHECK** this! Change??? NEW";
        $transformer = $this->get('app.markdown_transformer');
        $funfact = $transformer->parse($funfact);

//        dump($product);die;

        return $this->render(':FlorilFlowers/Product:show.html.twig',
            [
                'product'=> $product,
                'reviews' => $product->getReviews(),
                'recentNotes' => $recentNotes,
                'funfact' => $funfact,
            ]);
    }

    /**
     * @Route("/products", name="products_list")
     * lists only published products, ordered descending by quantity
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('FlorilFlowersBundle:Product\Product')
        ->findAllPublishedOrderedByQuantity();

        return $this->render(':FlorilFlowers/Product:list.html.twig', ['products' => $products,]);

//        dump($products); die;
    }


}
