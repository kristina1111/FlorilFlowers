<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.5.2017 г.
 * Time: 22:58 ч.
 */

namespace FlorilFlowersBundle\Service\ProductOffer;


use Doctrine\ORM\EntityManager;
use FlorilFlowersBundle\Entity\Product\Product;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\User\User;
use Symfony\Component\Form\Form;

class ProductOfferService
{
    /** @var  EntityManager $em */
    private $em;

    /**
     * ProductOfferService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getQtProductOffersInNotFinalisedCarts(ProductOffer $productOffer)
    {
        $qInNotFinalisedCarts = $this->em->getRepository('FlorilFlowersBundle:Product\ProductOffer')
            ->getOfferQsInNotFinalisedCarts($productOffer);

        if (!$qInNotFinalisedCarts) {
            $qInNotFinalisedCarts = 0;
        } else {
            $qInNotFinalisedCarts = intval($qInNotFinalisedCarts[0]["quantity"]);
        }

        return $qInNotFinalisedCarts;
    }

    public function getQtProductOffersInFinalisedCarts(ProductOffer $productOffer)
    {
        $qInFinalisedCarts = $this->em->getRepository('FlorilFlowersBundle:Product\ProductOffer')
            ->getOfferQsInFinalisedCarts($productOffer);

        if (!$qInFinalisedCarts) {
            $qInFinalisedCarts = 0;
        } else {
            $qInFinalisedCarts = intval($qInFinalisedCarts[0]["quantity"]);
        }

        return $qInFinalisedCarts;
    }

    public function getSoldQtProductOffers(ProductOffer $productOffer)
    {
        $soldQtProduct = $this->em->getRepository('FlorilFlowersBundle:Product\ProductOffer')
            ->findUserSoldProduct($productOffer);
        if (count($soldQtProduct) > 0) {
            $soldQtProduct = $soldQtProduct[0]['quantitySold'];
        } else {
            $soldQtProduct = 0;
        }

        return $soldQtProduct;
    }

    public function getBoughtQtProductOffers(User $user, ProductOffer $productOffer)
    {
        $boughtQtProduct = $this->em->getRepository('FlorilFlowersBundle:Product\ProductOffer')
            ->findIfUserBoughtProduct($user, $productOffer);

        if (count($boughtQtProduct) > 0) {
            $boughtQtProduct = $boughtQtProduct[0]['quantityBought'];
        } else {
            $boughtQtProduct = 0;
        }

        return $boughtQtProduct;
    }

    public function handleFormWhenFirstAnnounceToSell(Form $form, ProductOffer $originalProductOffer, User $user)
    {
//            dump($form->getData());exit;
        /** @var ProductOffer $productForSale */
        $productForSale = $form->getData();
        $productForSale->setUser($user);
        $productForSale->setProduct($originalProductOffer->getProduct());

//        because handlerequest is setting to null the data that is not in the post, we manually add it to the new productOffer Object
//        Instead of handleRequest we could use submit, so that we tell it not to clear the fields that are not submitted (because not rendered!)
//http://stackoverflow.com/questions/25291607/symfony2-how-to-stop-form-handlerequest-from-nulling-fields-that-dont-exist
        $productForSale->setFrontProductImage($originalProductOffer->getFrontProductImage());

        foreach ($originalProductOffer->getProductImages() as $originalImage) {
            $productForSale->getProductImages()->add(clone $originalImage);
        }

        $this->em->persist($productForSale);
        foreach ($productForSale->getProductImages() as $newImage) {
            $newImage->setProductOffer($productForSale);
            $this->em->persist($newImage);
        }
        $this->em->flush();
//        $this->addFlash('success', 'You successfully announced your product for sale!');
    }

    public function handleFormWhenEditSellAnnouncement(Form $form)
    {
//            dump($form->getData());exit;
        /** @var ProductOffer $productForSale */
        $productForSale = $form->getData();
        $this->em->persist($productForSale);
        $this->em->flush();
//        $this->addFlash('success', 'You successfully announced your product for sale!');
    }
}