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
//        dump($this->isRegularUserCreatorPromotion($productOffer));exit;
        if($this->isRegularUserCreatorPromotion($productOffer)){
            return 0;
        }
//        dump($this->categoryPromotions['creator']);exit;
        /** @var Promotion $promotion */
        $promotion = $this->generalPromotionPercent;
//        dump($this->isSameAuthorPromotionAndProductOffer($productOffer, $promotion->getUser()));exit;
//        if this is general promotion there is no need to check the author
        if($promotion!=null){
//        if($promotion!=null && $this->isSameAuthorPromotionAndProductOffer($productOffer, $promotion->getUser())){
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
        if($this->isRegularUserCreatorPromotion($productOffer)){
            return 0;
        }

        if(!$this->isSameAuthorPromotionAndProductOffer(
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
//        dump($role->getId() ==$this->rolePromotions["creator"]->getRole()->getId());exit;
        if($this->rolePromotions){
            return $role->getId() == $this->rolePromotions["creator"]->getRole()->getId();
        }
        return false;
//        dump($this->rolePromotions);exit;
//        return array_key_exists($role->getId(), $this->rolePromotions);
    }

    /**
     * @param ProductOffer $productOffer
     * @param Role $role
     * @return int
     */
    public function getRolePromotion(Role $role, ProductOffer $productOffer): int
    {
//        dump($this->isSameAuthorPromotionAndProductOffer(
//            $productOffer, $this->rolePromotions['creator']
//        ));exit;
//        if($this->isSameAuthorPromotionAndProductOffer(
//            $productOffer, $this->rolePromotions['creator']
//        )) {
            return $this->rolePromotions[$role->getId()];
//        }
//        return 0;
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

    // if the product offer creator is editor or admin - they cannot have this promotion
    public function isSameAuthorPromotionAndProductOffer(ProductOffer $productOffer, User $promotionCreator)
    {
//        dump($productOffer->getUser()->getRole() == "ROLE_USER" );exit;
        if($productOffer->getUser()->getRole() == "ROLE_USER" ){
            return true;
        }
//        dump($productOffer->getUser() == $promotionCreator);exit;
//        if($productOffer->getUser() == $promotionCreator || $productOffer->getUser()->isGranted('ROLE_EDITOR')){
        if(($productOffer->getUser() == $promotionCreator || $productOffer->getUser()->getRole() == "ROLE_EDITOR")){
//            return false;
            return true;
        }
//        return true;
        return false;
    }

    public function isRegularUserCreatorPromotion(ProductOffer $productOffer){
//        dump($productOffer->getUser()->getRole() == "ROLE_USER" );exit;
        if($productOffer->getUser()->getRole()->getType() == "ROLE_USER" ){
            return true;
        }
        return false;
    }

}