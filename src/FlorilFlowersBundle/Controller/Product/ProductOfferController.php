<?php

namespace FlorilFlowersBundle\Controller\Product;

use Doctrine\Common\Collections\ArrayCollection;
use FlorilFlowersBundle\Entity\Product\Product;
use FlorilFlowersBundle\Entity\Product\ProductImage;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Form\Product\ProductFormType;
use FlorilFlowersBundle\Form\Product\ProductOfferFormType;
use FlorilFlowersBundle\Form\Product\ProductOfferReviewFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/products")
 */
class ProductOfferController extends Controller
{

    /**
     * @Route("/new", name="create_product")
     * @Security("is_granted('ROLE_EDITOR')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(ProductOfferFormType::class);

//        dump($form);exit;

        // only handles data in POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            dump($form->getData());exit;
            /**
             * @var $productOffer ProductOffer
             */
            $productOffer = $form->getData();
            $productOffer->setUser($this->getUser());
//            dump($productOffer);exit;
            $em = $this->getDoctrine()->getManager();
            $em->persist($productOffer->getProduct());
//            foreach ($productOffer->getProductPrices() as $price){
//                $price->setProductOffer($productOffer);
//                $em->persist($price);
//            }

//            -- image upload processing --
            foreach ($productOffer->getProductImages() as $image) {
                // $file stores the uploaded image file
                /** @var UploadedFile $file */
                $file = $image->getFile();
//                    dump($file);exit;
//                    && false === $originalImages->contains($image)
                if (!!$file) {
//                    dump($file);exit;
                    $fileName = $this->get('app.image_uploader')->upload($file);

                    // Update the 'path' property to store the image file name
                    // instead of its contents
                    $image->setPath($fileName);
                    $image->setProductOffer($productOffer);
                    $image->setFile(null);

//                    check if the checkbox for the image to be main image for the productOffer
//                        need to be here and in the else statement because user can
                    if (!!$image->getIsFrontImage()) {
                        $productOffer->setFrontProductImage($image);
                    }
                    $em->persist($image);

                } else {
                    $productOffer->getProductImages()->removeElement($image);
                    continue;
                }

            }

//                    check if the checkbox for the image to be main image for the productOffer
//                    if(!!$image->getIsFrontImage()){
//                        $productOffer->setFrontProductImage($image);
//                    }

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

        if (!$productOffer) {
            $this->addFlash('info', 'There is no such product!');
            return $this->redirectToRoute('products_list');
        }

        if (!$this->getUser()->isAuthor($productOffer) && $this->denyAccessUnlessGranted(new Expression(
                '"ROLE_EDITOR" in roles'
            ))
        ) {
            $this->addFlash('info', 'You cannot edit this product!');
            return $this->redirectToRoute('products_list');
        }

        // give the images to the file input in the edit form, otherwise errors
        foreach ($productOffer->getProductImages() as $image) {
            $image->setFile(
                new File($this->getParameter('image_directory') . '/' . $image->getPath())
            );
        }
        $form = $this->createForm(ProductOfferFormType::class, $productOffer);

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

        if ($productOffer) {
//            // In order to be able to remove prices properly, first we need to save the prices that this product offer has
//            // in array. We will compare the values of this array to the newly submitted prices thus distinguishing the removed prices
//            $originalPrices = new ArrayCollection();
//            foreach ($productOffer->getProductPrices() as $originalPrice){
//                $originalPrices->add($originalPrice);
//            }

            $originalImages = new ArrayCollection();
            foreach ($productOffer->getProductImages() as $originalImage) {
                $originalImages->add($originalImage);
            }
//            dump($productOffer->getProductImages());
            $form = $this->createForm(ProductOfferFormType::class, $productOffer);
//            $form = $this->createForm(ProductOfferFormType::class);

            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()) {
//                dump($originalImages);
//                dump($productOffer->getProductImages());exit;
                $em = $this->getDoctrine()->getManager();
//                $em->persist($productOffer);


//                --- process prices ---

//                // Check if any of the prices that is in the original array before editing is not in the new array of prices
//                // after edition. If it is not, remove it from the db.
////                dump($originalPrices);
////                dump($productOffer->getProductPrices());exit;
//                foreach ($originalPrices as $originalPrice){
//                    if(false === $productOffer->getProductPrices()->contains($originalPrice)){
//                        /**
//                         * @var $originalPrice ProductPrice
//                         */
//                        $originalPrice->setProductOffer(null); // not necessary in this case!
//                        $em->persist($originalPrice);
//                        $em->remove($originalPrice);
//                    }
//                }
//
//                // Then check if any of the prices in the new array after the editing in not in the original
//                // array of prices before editing. If it is not, persist it in the db.
////            !!!!!!!!    It is not working if the user enters empty price object!!!!!!!!!
//                foreach ($productOffer->getProductPrices() as $price){
//                    if(false === $originalPrices->contains($price)){
//                        $price->setProductOffer($productOffer);
//                        $em->persist($price);
//                    }
//
//                }


//               --- process images ---

//                dump($originalImages);
//                dump($productOffer->getProductImages());exit;
                foreach ($originalImages as $originalImage) {

                    if (false === $productOffer->getProductImages()->contains($originalImage)) {
                        if ($productOffer->getFrontProductImage() === $originalImage) {
                            $productOffer->setFrontProductImage(null);
                        }
//                        dump($originalImage);exit;
                        /**
                         * @var $originalImage ProductImage
                         */
                        $originalImage->setProductOffer(null);
                        $em->persist($originalImage);
                        $em->remove($originalImage);
                    }
                }

                foreach ($productOffer->getProductImages() as $image) {
                    // $file stores the uploaded image file
                    /** @var UploadedFile $file */
                    $file = $image->getFile();
//                    dump($file);exit;
//                    && false === $originalImages->contains($image)
                    if (!!$file) {
//                    dump($file);exit;
                        $fileName = $this->get('app.image_uploader')->upload($file);

                        // Update the 'path' property to store the image file name
                        // instead of its contents
                        $image->setPath($fileName);
                        $image->setProductOffer($productOffer);
                        $image->setFile(null);

//                    check if the checkbox for the image to be main image for the productOffer
//                        need to be here and in the else statement because user can
                        if (!!$image->getIsFrontImage()) {
                            $productOffer->setFrontProductImage($image);
                        }

                    } else {
////                    check if the checkbox for the image to be main image for the productOffer
                        if (!!$image->getIsFrontImage() && true === $originalImages->contains($image)) {
                            $productOffer->setFrontProductImage($image);
//                            $em->persist($image);
                        } else {
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
                return $this->redirectToRoute('create_categories_list');
            }

            return $this->render(':FlorilFlowers/Product:edit.html.twig', ['productForm' => $form->createView()]);
        }

        $this->addFlash('info', 'There is no such product!');
        return $this->redirectToRoute('create_categories_list');
    }

    /**
     * @Route("/delete/{id}", name="delete_productOffer")
     * @Method("POST")
     * @Security("is_granted('ROLE_USER')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProductOfferAction($id)
    {
        $productOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($id);
        if (!!$productOffer && ($this->getUser()->isAuthor($productOffer) || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_EDITOR" in roles'))
            )
        ) {

            if (!$productOffer) {
                $this->addFlash('info', 'There are no such product');
                return $this->redirectToRoute('create_categories_list');
            }
            $em = $this->getDoctrine()->getManager();
            $productOffer->setDeletedOn(new \DateTime());
            $em->persist($productOffer);
            $em->flush();
            $this->addFlash('success', 'You successfully deleted product ' . $productOffer->getProduct()->getName() . ' from the database');
            return $this->redirectToRoute('create_categories_list');
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/product/{id}", name="product_show")
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
//        $product = $em->getRepository('FlorilFlowersBundle:Product\Product')->findOneBy(['id' => $id]);
        $productOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($id);

        if (!$productOffer) {
//            throw $this->createNotFoundException('No product found!');
            $this->addFlash('info', 'There is no such product!');
            return $this->redirectToRoute('products_list');
        }

        $reviewForm = $this->createForm(ProductOfferReviewFormType::class);

//        $recentNotes = $em->getRepository('FlorilFlowersBundle:Product\ProductReview')
//            ->findAllRecentNotesForProduct($product);

//        $funfact = "**CHECK** this! Change??? NEW";
//        $transformer = $this->get('app.markdown_transformer');
//        $funfact = $transformer->parse($funfact);

//        dump($product);die;

        return $this->render(':FlorilFlowers/Product:show.html.twig',
            [
                'productOffer' => $productOffer,
                'reviewForm' => $reviewForm->createView()
//                'reviews' => $product->getReviews(),
//                'recentNotes' => $recentNotes,
//                'funfact' => $funfact,
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
        $priceCalculator = $this->get('app.price_calculator');
//        dump($products);exit;
//
        return $this->render(':FlorilFlowers/Product:list.html.twig', [
            'productsOffers' => $products,
            'priceCalculator' => $priceCalculator
        ]);

//        dump($products); die;
    }

    /**
     * @Route("/user/{idUser}/products/sell/{idProduct}", name="announce_for_sale")
     * @Security("is_granted('ROLE_USER')")
     * @param $idUser
     * @param $idProduct
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function announceForSaleByBuyerAction($idUser, $idProduct, Request $request)
    {
        if ($this->getUser()->getId() != $idUser || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_EDITOR" in roles'))
        ) {
            $this->addFlash('error', 'You cannot sell this product');
        } else {
            $originalProductOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($idProduct);

            if (!!$originalProductOffer) {
                $alreadyOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->getProductOfferByCreatorAndProduct($this->getUser(), $originalProductOffer->getProduct());

                $quantityBoughtProduct = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')
                    ->findIfUserBoughtProduct($this->getUser(), $originalProductOffer);

//                find the quantity of the sold products - needed for the check if the user possesses enough quantity for sale
                $quantitySoldProduct = 0;
                if (count($quantityBoughtProduct) > 0) {
                    $quantityBoughtProduct = $quantityBoughtProduct[0]['quantityBought'];
                } else {
                    $quantityBoughtProduct = 0;
                }
                if (!!$alreadyOffer) {
                    $quantitySoldProduct = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')
                        ->findUserSoldProduct($alreadyOffer[0]);
                    if (count($quantitySoldProduct) > 0) {
                        $quantitySoldProduct = $quantitySoldProduct[0]['quantitySold'];
                    }else{
                        $quantitySoldProduct = 0;
                    }
                }
//            if the user announces the offer for the first time
                if (!$alreadyOffer) {
//                    first find if the user is the buyer ot that product
//                    $query = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->findIfUserBoughtProduct($this->getUser(), $originalProductOffer);

                    if (count($quantityBoughtProduct) > 0) { // if yes
//          this is needed because some fields in the form are not rendered and handlerequest is not parsing them to the productOffer object
//          method __clone is added to the ProductOffer entity
                        $productForSale = clone $originalProductOffer;

                        $productForSale->setQuantityForSale(1);

                        $form = $this->createForm(ProductOfferFormType::class, $productForSale);
                        $form->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {
// if the user try to announce more quantities for sale than he actually has (we calculate what they bought and what they sold)
                            if ($quantityBoughtProduct - $quantitySoldProduct >= $productForSale->getQuantityForSale()) {
                                $this->handleFormWhenFirstAnnounceToSell($form, $originalProductOffer);
                                return $this->redirectToRoute('products_list');
                            }else{
                                $this->addFlash('error', "You haven't bought enough of that product. You need to buy more in order to sell!");
                            }
                        }

                        return $this->render(':FlorilFlowers/User/Products:announce-for-sale.html.twig', array(
                            'productForm' => $form->createView()
                        ));
                    }
                    $this->addFlash('error', 'You cannot sell this product');
                } else { // if user wants to edit existing offer
                    $em = $this->getDoctrine()->getManager();
                    /** @var ProductOffer $productForSale */
                    $productForSale = $alreadyOffer[0];

                    $form = $this->createForm(ProductOfferFormType::class, $productForSale);

//                        Instead of handleRequest use submit, so that we tell it not to clear the fields that are not submitted (because not rendered!)
//http://stackoverflow.com/questions/25291607/symfony2-how-to-stop-form-handlerequest-from-nulling-fields-that-dont-exist
//                        $form->handleRequest($request);
                    $form->submit($request->get($form->getName()), false);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $qInNotFinalisedCarts = $em->getRepository('FlorilFlowersBundle:Product\ProductOffer')
                            ->getOfferQsInNotFinalisedCarts($productForSale);
                        $qInFinalisedCarts = $em->getRepository('FlorilFlowersBundle:Product\ProductOffer')
                            ->getOfferQsInFinalisedCarts($productForSale);
                        if(!$qInNotFinalisedCarts){
                            $qInNotFinalisedCarts = 0;
                        }else{
                            $qInNotFinalisedCarts = intval($qInNotFinalisedCarts[0]["quantity"]);
                        }
                        if(!$qInFinalisedCarts){
                            $qInFinalisedCarts = 0;
                        }else{
                            $qInFinalisedCarts = intval($qInNotFinalisedCarts[0]["quantity"]);
                        }

//dump(($quantityBoughtProduct));exit;
//                            if the user try to announce more quantities for sale than he actually has (we calculate what they bought and what they sold)
                        if (($quantityBoughtProduct - $quantitySoldProduct - $qInFinalisedCarts - $qInNotFinalisedCarts) > $productForSale->getQuantityForSale()) {
                            $this->handleFormWhenEditSellAnnouncement($form);
                            return $this->redirectToRoute('products_list');
                        } else {
                            $this->addFlash('error', "You haven't bought enough of that product. You need to buy more in order to sell!");
                            return $this->render(':FlorilFlowers/User/Products:announce-for-sale.html.twig', array(
                                'productForm' => $form->createView()
                            ));
                        }

                    }

                    return $this->render(':FlorilFlowers/User/Products:announce-for-sale.html.twig', array(
                        'productForm' => $form->createView()
                    ));
                }


            } else {
                $this->addFlash('error', 'You cannot sell this product');
            }
        }

        return $this->redirectToRoute('homepage');
    }

//    /**
//     * @Route("/user/{idUser}/products/sell/{idProduct}", name="announce_for_sale_process")
//     * @Method("POST")
//     * @Security("is_granted('ROLE_USER')")
//     * @param $idUser
//     * @param $idProduct
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
//     */
//    public function announceForSaleByBuyerActionProcess($idUser, $idProduct, Request $request)
//    {
//        if($this->getUser()->getId() != $idUser){
//            $this->addFlash('error', 'You cannot sell this product');
//        }else{
//            $em = $this->getDoctrine()->getManager();
//
//            $originalProductOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($idProduct);
//            $productForSale = clone $originalProductOffer;
//
//            $productForSale->setQuantityForSale(1);
//
//            $form = $this->createForm(ProductOfferFormType::class, $productForSale);
//            // only handles data in POST
//            $form->handleRequest($request);
////        $em->detach($productForSale);
////        $productForSale = clone $productForSale;
//            if($form->isSubmitted() && $form->isValid()) {
//
////            dump($form->getData());exit;
//                /** @var ProductOffer $productForSale */
//                $productForSale = $form->getData();
//                $productForSale->setUser($this->getUser());
//                $productForSale->setProduct($originalProductOffer->getProduct());
//
//                $productForSale->setFrontProductImage($originalProductOffer->getFrontProductImage());
//
//                foreach ($originalProductOffer->getProductImages() as $originalImage){
//                    $productForSale->getProductImages()->add(clone $originalImage);
//                }
//
//
////            dump($productForSale->getProduct()->getName());exit;
////            dump($productForSale);exit;
////            dump($productOffer);exit;
//
//                $em->persist($productForSale);
//                foreach ($productForSale->getProductImages() as $newImage){
//                    $newImage->setProductOffer($productForSale);
//                    $em->persist($newImage);
//                }
//                $em->flush();
//                $this->addFlash('success', 'You successfully announced your product for sale!');
//                return $this->redirectToRoute('products_list');
//            }
//            return $this->render(':FlorilFlowers/User/Products:announce-for-sale.html.twig', array(
//                'productForm' => $form->createView()
//            ));
//        }
//        return $this->redirectToRoute('homepage');
//    }

    private function handleFormWhenFirstAnnounceToSell($form, ProductOffer $originalProductOffer)
    {
        $em = $this->getDoctrine()->getManager();
//            dump($form->getData());exit;
        /** @var ProductOffer $productForSale */
        $productForSale = $form->getData();
        $productForSale->setUser($this->getUser());
        $productForSale->setProduct($originalProductOffer->getProduct());

//        because handlerequest is setting to null the data that is not in the post, we manually add it to the new productOffer Object
//        Instead of handleRequest we could use submit, so that we tell it not to clear the fields that are not submitted (because not rendered!)
//http://stackoverflow.com/questions/25291607/symfony2-how-to-stop-form-handlerequest-from-nulling-fields-that-dont-exist
        $productForSale->setFrontProductImage($originalProductOffer->getFrontProductImage());

        foreach ($originalProductOffer->getProductImages() as $originalImage) {
            $productForSale->getProductImages()->add(clone $originalImage);
        }

        $em->persist($productForSale);
        foreach ($productForSale->getProductImages() as $newImage) {
            $newImage->setProductOffer($productForSale);
            $em->persist($newImage);
        }
        $em->flush();
        $this->addFlash('success', 'You successfully announced your product for sale!');
    }

    private function handleFormWhenEditSellAnnouncement($form)
    {
        $em = $this->getDoctrine()->getManager();
//            dump($form->getData());exit;
        /** @var ProductOffer $productForSale */
        $productForSale = $form->getData();
        $em->persist($productForSale);
        $em->flush();
        $this->addFlash('success', 'You successfully announced your product for sale!');
    }

}
