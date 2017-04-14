<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6.4.2017 г.
 * Time: 14:45 ч.
 */

namespace FlorilFlowersBundle\Repository\Category;


use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('category')
            ->orderBy('category.name', 'ASC');
    }
}