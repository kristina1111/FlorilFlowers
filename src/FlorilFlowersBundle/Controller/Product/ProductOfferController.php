<?php

namespace FlorilFlowersBundle\Controller\Product;

use Doctrine\Common\Collections\ArrayCollection;
use FlorilFlowersBundle\Entity\Product\Product;
use FlorilFlowersBundle\Entity\Product\ProductImage;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\Product\ProductPrice;
use FlorilFlowersBundle\Form\Product\ProductFormType;
use FlorilFlowersBundle\Form\Product\ProductOfferFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/products")
 */
class ProductOfferController extends Controller
{

    /**
     * @Route("/new", name="create_product")
     * @Security("is_granted('ROLE_USER')")
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
     * @Route("/edit/{id}", name="edit_product")
     * @Security("is_granted('ROLE_USER')")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($id)
    {
        $productOffer = $this->getDoctrine()->getRepository(ProductOffer::class)->find($id);

        if(!$productOffer){
            $this->addFlash('info', 'There is no such product!');
            return $this->redirectToRoute('products_list');
        }

        if(!$this->getUser()->isAuthor($productOffer) && !$this->getUser()->isEditor()){
            $this->addFlash('info', 'You cannot edit this product!');
            return $this->redirectToRoute('products_list');
        }

        foreach ($productOffer->getProductImages() as $image){
            $image->setFile(
                new File($this->getParameter('image_directory') . '/' . $image->getPath())
            );
        }
        $form = $this->createForm(ProductOfferFormType::class, $productOffer);

//        dump($productOffer->getProductImages());exit;

        return $this->render(':FlorilFlowers/Product:edit.html.twig', ['productForm' => $form->createView()]);


    }

    /**
     * @Route("/edit/{id}", name="edit_product_process")
     * @Security("is_granted('ROLE_USER')")
     * @Method("POST")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editActionProcess($id, Request $request)
    {
        $productOffer = $this->getDoctrine()->getRepository(ProductOffer::class)->find($id);

        if($productOffer){
            // In order to be able to remove prices properly, first we need to save the prices that this product offer has
            // in array. We will compare the values of this array to the newly submitted prices thus distinguishing the removed prices
            $originalPrices = new ArrayCollection();
            foreach ($productOffer->getProductPrices() as $originalPrice){
                $originalPrices->add($originalPrice);
            }


            $originalImages = new ArrayCollection();
            foreach ($productOffer->getProductImages() as $originalImage){
                $originalImages->add($originalImage);
            }
//            dump($productOffer->getProductImages());
            $form = $this->createForm(ProductOfferFormType::class, $productOffer);
//            $form = $this->createForm(ProductOfferFormType::class);

            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){
//                dump($originalImages);
//                dump($productOffer->getProductImages());exit;
                $em = $this->getDoctrine()->getManager();
//                $em->persist($productOffer);


//                --- process prices ---

                // Check if any of the prices that is in the original array before editing is not in the new array of prices
                // after edition. If it is not, remove it from the db.
//                dump($originalPrices);
//                dump($productOffer->getProductPrices());exit;
                foreach ($originalPrices as $originalPrice){
                    if(false === $productOffer->getProductPrices()->contains($originalPrice)){
                        /**
                         * @var $originalPrice ProductPrice
                         */
                        $originalPrice->setProductOffer(null); // not necessary in this case!
                        $em->persist($originalPrice);
                        $em->remove($originalPrice);
                    }
                }

                // Then check if any of the prices in the new array after the editing in not in the original
                // array of prices before editing. If it is not, persist it in the db.
//            !!!!!!!!    It is not working if the user enters empty price object!!!!!!!!!
                foreach ($productOffer->getProductPrices() as $price){
                    if(false === $originalPrices->contains($price)){
                        $price->setProductOffer($productOffer);
                        $em->persist($price);
                    }

                }


//               --- process images ---

//                dump($originalImages);
//                dump($productOffer->getProductImages());exit;
                foreach ($originalImages as $originalImage){

                    if(false === $productOffer->getProductImages()->contains($originalImage)){
//                        dump($originalImage);exit;
                        /**
                         * @var $originalImage ProductImage
                         */
                        $originalImage->setProductOffer(null);
//                        $em->persist($originalImage);
                        $em->remove($originalImage);
                    }
                }

                foreach ($productOffer->getProductImages() as $image){
                    // $file stores the uploaded image file
                    /** @var UploadedFile $file */
                    $file = $image->getFile();
//                    dump($file);exit;
//                    && false === $originalImages->contains($image)
                    if(!!$file){
//                    dump($file);exit;
                        $fileName = $this->get('app.image_uploader')->upload($file);

                        // Update the 'path' property to store the image file name
                        // instead of its contents
                        $image->setPath($fileName);
                        $image->setProductOffer($productOffer);
                        $image->setFile(null);

//                    check if the checkbox for the image to be main image for the productOffer
//                        need to be here and in the else statement because user can
                        if(!!$image->getIsFrontImage()){
                            $productOffer->setFrontProductImage($image);
                        }

                    }
                    else{
////                    check if the checkbox for the image to be main image for the productOffer
                        if(!!$image->getIsFrontImage() && true === $originalImages->contains($image)){
                            $productOffer->setFrontProductImage($image);
//                            $em->persist($image);
                        }else{
                            $productOffer->getProductImages()->removeElement($image);
                            continue;
                        }

                    }

//                    check if the checkbox for the image to be main image for the productOffer
//                    if(!!$image->getIsFrontImage()){
//                        $productOffer->setFrontProductImage($image);
//                    }

                    $em->persist($image);

                }

                $em->persist($productOffer);

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
     * @Route("/all", name="products_list")
     * lists only published products, ordered descending by quantity
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('FlorilFlowersBundle:Product\ProductOffer')->findAll();
//        ->findOneBy(['id' => 2]);

//        dump($products->getProduct()->getName());exit;

        return $this->render(':FlorilFlowers/Product:list.html.twig', [
            'productsOffers' => $products,
        ]);

//        dump($products); die;
    }


}
