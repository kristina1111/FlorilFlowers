<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.4.2017 г.
 * Time: 10:55 ч.
 */

namespace FlorilFlowersBundle\Service\ProductOffer;


use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\Product\ProductOfferOrder;
use FlorilFlowersBundle\Repository\Product\ProductOfferOrderRepository;
use FlorilFlowersBundle\Repository\Product\ProductOfferRepository;
use FlorilFlowersBundle\Service\Promotion\PriceCalculator;
use FlorilFlowersBundle\Service\Promotion\PromotionManager;

class ProductOfferOrderManager
{
    private $productOfferRepository;

    private $productOfferOrderRepository;

    /**
     * @var PriceCalculator $priceCalculator
     */
    private $priceCalculator;

    public function __construct(
        ProductOfferRepository $productOfferRepository,
        ProductOfferOrderRepository $productOfferOrderRepository,
        PriceCalculator $priceCalculator
    )
    {
        $this->productOfferRepository = $productOfferRepository;
        $this->productOfferOrderRepository = $productOfferOrderRepository;
        $this->priceCalculator = $priceCalculator;
    }

    public function getOrderedProductOffers()
    {
        /** @var ProductOfferOrder $activeOrder */
        $activeOrder = $this->productOfferOrderRepository->findActiveOrder()[0];
//        dump($activeOrder);exit;
        $orderByWhat = $activeOrder->getName();
        $descOrAsc = $activeOrder->getDescOrAsc() == 0 ? "ASC" : "DESC";
//        dump($descOrAsc);exit;
        $orderByWhat = implode('', array_map(function ($a) {
            return ucfirst($a);
        }, explode(' ', $orderByWhat)));

//        dump($orderByWhat);exit;
        $command = 'getAll' . $orderByWhat;

        $productOffers = $this->productOfferRepository->$command($descOrAsc);
//      if we want to order by price, we need to take into consideration the promotions
//        some custom sorting logic is needed
        if(strpos($command, 'Price')!== false){
            $productOffers = $this->orderByRetailPriceConsideringPromotions($productOffers, $descOrAsc);
        }
        return $productOffers;
//        dump($productOffers);exit;
    }

    private function orderByRetailPriceConsideringPromotions(array $productOffers, $descOrAsc)
    {
        $productOffers = array_map(function ($a){
            $a['retailPrice'] = $this->priceCalculator->calculate($a['offer']);
            return $a;
        }, $productOffers);

        usort($productOffers, function ($a, $b) use ($descOrAsc){
            if($descOrAsc == "DESC"){
                return $a['retailPrice']<$b['retailPrice'];
            }
            return $a['retailPrice']>$b['retailPrice'];
        });

        $resultArray = [];
        foreach ($productOffers as $offer){
            $resultArray[] = $offer['offer'];
        }

//        dump($resultArray);exit;
        return $resultArray;
    }
}