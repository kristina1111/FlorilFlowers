<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.4.2017 г.
 * Time: 15:44 ч.
 */

namespace FlorilFlowersBundle\Service\Promotion;


use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class PriceCalculator
{
    /**
     * @var PromotionManager $prManager
     */
    protected $prManager;

    /**
     * @var TokenStorage $tokenStorage
     */
    protected $tokenStorage;

    public function __construct(PromotionManager $prManager, TokenStorage $tokenStorage)
    {
        $this->prManager = $prManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param ProductOffer $productOffer
     * @return float
     */
    public function calculate(ProductOffer $productOffer): float
    {
        /** @var User $user */ //returns 'anon.' if user is not logged in
        $user = $this->tokenStorage->getToken()->getUser();
        $highestPromotion = 0;
// Only users that created an offer can announce promotions for them so we check
//        if($this->prManager->isSameAuthorPromotionAndProductOffer($productOffer, $user)){
//        walk through all promotions that we fetched
        $category = $productOffer->getProduct()->getCategory();

        $generalPromotion = $this->prManager->getGeneralPromotionPercent($productOffer);
//        dump($generalPromotion);exit;
        $highestPromotion = $generalPromotion > $highestPromotion ? $generalPromotion : $highestPromotion;

        $categoryPromotion = 0;
        if ($this->prManager->hasCategoryPromotion($category)) {
            $categoryPromotion = $this->prManager->getCategoryPromotion($category, $productOffer);
//            dump($categoryPromotion);exit;
        }
        $highestPromotion = $categoryPromotion > $highestPromotion ? $categoryPromotion : $highestPromotion;

        $productPromotion = 0;
        if ($this->prManager->hasProductPromotion($productOffer)) {
            $productPromotion = $this->prManager->getProductPromotion($productOffer);
//            dump($categoryPromotion);exit;
        }
        $highestPromotion = $productPromotion > $highestPromotion ? $productPromotion : $highestPromotion;

        $rolePromotion = 0;

//        dump($user !== 'anon.');exit;
        if ($user !== 'anon.' && $this->prManager->hasRolePromotion($user->getRole())) {
            $rolePromotion = $this->prManager->getRolePromotion($user->getRole(), $productOffer);
//            dump($rolePromotion);exit;
        }
        $highestPromotion = $rolePromotion > $highestPromotion ? $rolePromotion : $highestPromotion;
//        }


        return $productOffer->getRetailPrice() - ($productOffer->getRetailPrice() * $highestPromotion / 100);


    }
}