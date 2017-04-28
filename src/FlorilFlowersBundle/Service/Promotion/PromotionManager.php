<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.4.2017 г.
 * Time: 15:05 ч.
 */

namespace FlorilFlowersBundle\Service\Promotion;


use FlorilFlowersBundle\Entity\Category\Category;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\Promotion\Promotion;
use FlorilFlowersBundle\Entity\User\Role;
use FlorilFlowersBundle\Entity\User\User;
use FlorilFlowersBundle\Repository\Promotion\PromotionRepository;

class PromotionManager
{
    /**
     * @var int $generalPromotionPercent
     */
    private $generalPromotionPercent;

    /**
     * @var int $rolePromotions
     */
    private $rolePromotions;

    /**
     * @var array $categoryPromotions
     */
    private $categoryPromotions;

    /**
     * @var array $productPromotions
     */
    private $productPromotions;

    public function __construct(PromotionRepository $repository)
    {
        $this->generalPromotionPercent = $repository->getGeneralPromotionAllProductsAllUsers();
        $this->categoryPromotions = $repository->getPromotionForCategories();
        $this->rolePromotions = $repository->getPromotionForAllProductsByRoleUser();
        $this->productPromotions = $repository->getPromotionsByProduct();
    }

    /**
     * @param ProductOffer $productOffer
     * @return int
     * @internal param User $productOfferCreator
     */
    public function getGeneralPromotionPercent(ProductOffer $productOffer): int
    {
        /** @var Promotion $promotion */
        $promotion = $this->generalPromotionPercent;
        if($promotion!=null && $this->isSameAuthorPromotionAndProductOffer($productOffer, $promotion->getUser())){
            return $promotion->getPercent();
        }
        return 0;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function hasCategoryPromotion(Category $category) : bool
    {
//        dump($this->categoryPromotions);exit;
        return array_key_exists($category->getId(), $this->categoryPromotions);
    }


    /**
     * @param Category $category
     * @param ProductOffer $productOffer
     * @return int
     */
    public function getCategoryPromotion(Category $category, ProductOffer $productOffer): int
    {
        if($this->isSameAuthorPromotionAndProductOffer(
            $productOffer, $this->categoryPromotions['creator']
        )){
            return $this->categoryPromotions[$category->getId()];
        }
        return 0;

    }

    /**
     * @param Role $role
     * @return bool
     */
    public function hasRolePromotion(Role $role) : bool
    {
        return array_key_exists($role->getId(), $this->rolePromotions);
    }

    /**
     * @param ProductOffer $productOffer
     * @param Role $role
     * @return int
     */
    public function getRolePromotion(Role $role, ProductOffer $productOffer): int
    {
        if($this->isSameAuthorPromotionAndProductOffer(
            $productOffer, $this->rolePromotions['creator']
        )) {
            return $this->rolePromotions[$role->getId()];
        }
        return 0;
    }

    /**
     * @param ProductOffer $productOffer
     * @return bool
     */
    public function hasProductPromotion(ProductOffer $productOffer) : bool
    {
        return array_key_exists($productOffer->getId(), $this->productPromotions);
    }

    /**
     * @param ProductOffer $productOffer
     * @return int
     */
    public function getProductPromotion(ProductOffer $productOffer): int
    {
        if($this->isSameAuthorPromotionAndProductOffer(
            $productOffer, $this->rolePromotions['creator']
        )) {
            return $this->productPromotions[$productOffer->getId()];
        }
        return 0;
    }

    public function isSameAuthorPromotionAndProductOffer(ProductOffer $productOffer, User $promotionCreator)
    {
        if($productOffer->getUser() == $promotionCreator || $productOffer->getUser()->getRole() == "ROLE_EDITOR"){
            return true;
        }
        return false;
    }


}