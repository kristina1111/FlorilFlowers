<?php

namespace FlorilFlowersBundle\Controller\Product;

use Doctrine\Common\Collections\ArrayCollection;
use FlorilFlowersBundle\Entity\Product\Product;
use FlorilFlowersBundle\Entity\Product\ProductImage;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\Product\ProductOfferOrder;
use FlorilFlowersBundle\Entity\User\User;
use FlorilFlowersBundle\Form\Product\ProductFormType;
use FlorilFlowersBundle\Form\Product\ProductOfferFormType;
use FlorilFlowersBundle\Form\Product\ProductOfferReviewFormType;
use FlorilFlowersBundle\Form\Product\ProductOfferOrderFormType;
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
//            for($i = 0; $i<count($productOffer->getProductImages()); $i++){
//                $image = $productOffer->getProductImages()[$i];
            foreach ($productOffer->getProductImages() as $image) {
                // $file stores the uploaded image file
                /** @var UploadedFile $file */
                $file = $image->getFile();
//                $file = $image->getFile();
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
                    $form = $this->createForm(ProductOfferFormType::class, $productOffer);
//                    dump($productOffer->getProductImages());exit;
                    return $this->render(':FlorilFlowers/Product:create.html.twig', ['productForm' => $form->createView()]);
//                    array_splice($productOffer->getProductImages(), $i ,1);

//                    this is not working
//                    $productOffer->getProductImages()->removeElement($image);
//                    $em->detach($image);
//                    continue;
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
        $priceCalculator = $this->get('app.price_calculator');

        return $this->render(':FlorilFlowers/Product:show.html.twig',
            [
                'productOffer' => $productOffer,
                'reviewForm' => $reviewForm->createView(),
                'priceCalculator' => $priceCalculator
            ]);
    }

    /**
     * @Route("/all", name="products_list")
     * lists only published products, ordered descending by quantity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $this->get('app.product_offer_order_manager')->getOrderedProductOffers();

//        Handling how the products are ordered in the view

//        get all possible orderTypes (so that we can send them to the view)
        $allOrders = $em->getRepository('FlorilFlowersBundle:Product\ProductOfferOrder')->findAll();
//        create the orderForm
        $formProductOrder = $this->createForm(ProductOfferOrderFormType::class);
        $formProductOrder->handleRequest($request);

        if ($formProductOrder->isValid() && $formProductOrder->isSubmitted()) {
            //        find the current active order
            /** @var ProductOfferOrder $productsActiveOrder */
            $productsActiveOrder = $em->getRepository('FlorilFlowersBundle:Product\ProductOfferOrder')->findActiveOrder()[0];
//            because we don't use the formType to render all the possible orderTypes
//            we need to get the newly selected order from the request
            $orderTypeId = $request->request->get('orderType');
//            but we use the formType to render the desOrAsc checkbox, so we get it from the handled formType
            $desOrAsc = boolval($formProductOrder['descOrAsc']->getViewData());
//            we check if the newly selected order is different from the currently active order
            if ($productsActiveOrder->getId()==$orderTypeId) { // if not
//                we check if the newly selected desOrAsc order is different from the currently active order desOrAsc
                if($productsActiveOrder->getDescOrAsc()!=$desOrAsc){ // if yes
//                    set the new desOrAsc order to the currently active order
                    $productsActiveOrder->setDescOrAsc($desOrAsc);
                    $this->addFlash('info', "You just reordered the products!");
                }else{ // if not, the nothing is changed
                    $this->addFlash('info', 'Products are already ordered like this!');
                    return $this->redirectToRoute('products_list');
                }
            }else{ // if yes
//                find the newly selected order in the db by id
                $newOrder = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOfferOrder')->find($orderTypeId);
                if($newOrder){ // if it exists
//                    set the previous active order property ActivatedOn to null
//                   (this is the property by which we distinguish the currently active order)
                    $productsActiveOrder->setActivatedOn(null);
//                    set the newly selected order property ActivatedOn to the present \Datetime
                    $newOrder->setActivatedOn(new \DateTime());
//                    set the newly selected order property to the selected one
                    $newOrder->setDescOrAsc($desOrAsc);

                    $em->persist($newOrder);
                    $this->addFlash('info', "You just reordered the products!");
                }else{
                    $this->addFlash('error', "You cannot order the products like that!");
                    return $this->redirectToRoute('products_list');
                }
            }

            $em->persist($productsActiveOrder);
            $em->flush();
            return $this->redirectToRoute('products_list');

        }
        $priceCalculator = $this->get('app.price_calculator');
        return $this->render(':FlorilFlowers/Product:list.html.twig', [
            'productsOffers' => $products,
            'priceCalculator' => $priceCalculator,
            'formProductsOrder' => $formProductOrder->createView(),
            'allOrderTypes' => $allOrders
        ]);
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
//        dump($this->getUser()->getRoles());exit;
//        if the user that is announcing the product for sale is not the owner of the product
        if ($this->getUser()->getId() != $idUser || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_EDITOR" in roles'))
        ) {
            $this->addFlash('error', 'You cannot sell this product');
        } else {
            /** @var User $user */
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($idUser);
//            find the original productOffer - it is the base for the new offer by the user
            $originalProductOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->find($idProduct);

            if (!!$originalProductOffer) { // if there are original product offer
                $alreadyOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->getProductOfferByCreatorAndProduct($user, $originalProductOffer->getProduct());

//                here we define the bought and sold quantities of that product by that user
                $boughtQtProduct = $this->get('app.product_offer_service')->getBoughtQtProductOffers($user, $originalProductOffer);
//            if the user announces the offer for the first time
                if (!$alreadyOffer) {
                    if (count($boughtQtProduct) > 0) { // if yes
//          this is needed because some fields in the form are not rendered and handlerequest is not parsing them to the productOffer object
//          method __clone is added to the ProductOffer entity
                        $productForSale = clone $originalProductOffer;

                        $productForSale->setQuantityForSale(1);

                        $form = $this->createForm(ProductOfferFormType::class, $productForSale);
                        $form->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {
// if the user try to announce more quantities for sale than he actually has (we calculate what they bought and what they sold)
                            if ($boughtQtProduct >= $productForSale->getQuantityForSale()) {
                                $this->get('app.product_offer_service')->handleFormWhenFirstAnnounceToSell($form, $originalProductOffer, $user);
                                return $this->redirectToRoute('products_list');
                            } else {
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

//  Instead of handleRequest use submit, so that we tell it not to clear the fields that are not submitted (because not rendered!)
//http://stackoverflow.com/questions/25291607/symfony2-how-to-stop-form-handlerequest-from-nulling-fields-that-dont-exist
                    $form->submit($request->get($form->getName()), false);
                    if ($form->isSubmitted() && $form->isValid()) {

                        $qInNotFinalisedCarts = $this->get('app.product_offer_service')->getQtProductOffersInNotFinalisedCarts($productForSale);
                        $qInFinalisedCarts = $this->get('app.product_offer_service')->getQtProductOffersInFinalisedCarts($productForSale);
                        $soldQtProduct = $this->get('app.product_offer_service')->getSoldQtProductOffers($productForSale);

//dump(($quantityBoughtProduct));exit;
//                            if the user try to announce more quantities for sale than he actually has (we calculate what they bought and what they sold)
                        if (($boughtQtProduct - $soldQtProduct - $qInFinalisedCarts - $qInNotFinalisedCarts) >= $productForSale->getQuantityForSale()) {
                            $this->get('app.product_offer_service')->handleFormWhenEditSellAnnouncement($form);
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

}
