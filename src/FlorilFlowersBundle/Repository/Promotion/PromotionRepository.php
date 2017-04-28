<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.4.2017 г.
 * Time: 12:35 ч.
 */

namespace FlorilFlowersBundle\Repository\Promotion;


use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\Category\Category;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\Promotion\Promotion;
use FlorilFlowersBundle\Entity\User\Role;
use FlorilFlowersBundle\Entity\User\User;

class PromotionRepository extends EntityRepository
{
    public function getGeneralPromotionAllProductsAllUsers()
    {
        $qb = $this->createQueryBuilder('p');
        $today = new \DateTime();
//        dump($today->format('Y-m-d H:i:s'));exit;
        $query = $qb->select('p')
            ->where($qb->expr()->lte('p.startDate', ':today'))
            ->andWhere($qb->expr()->gte('p.endDate', ':today'))
            ->andWhere('p.category is NULL')
            ->andWhere('p.productOffer is NULL')
            ->andWhere('p.role is NULL')
            ->setParameter('today', $today->format('Y-m-d H:i:s'))
            ->orderBy('p.percent', 'DESC')
            ->setMaxResults(1)
            ->getQuery();
//        dump($query->getOneOrNullResult());exit;

//        if($query->getOneOrNullResult() !== null){
////            dump($query->getOneOrNullResult());exit;
//            return $query->getSingleScalarResult();
//        }
        return $query->getOneOrNullResult();
    }

    public function getPromotionForCategories()
    {
        $qb = $this->createQueryBuilder('p');
        $today = new \DateTime();
//        dump($today->format('Y-m-d H:i:s'));exit;
        $query = $qb->select(['p, MAX(p.percent) as percent', 'c.id'])
            ->join('p.category', 'c')
            ->where($qb->expr()->lte('p.startDate', ':today'))
            ->andWhere($qb->expr()->gte('p.endDate', ':today'))
            ->andWhere($qb->expr()->isNotNull('p.category'))
            ->andWhere($qb->expr()->isNull('p.productOffer'))
            ->andWhere($qb->expr()->isNull('p.role'))
            ->setParameters([
                'today' => $today->format('Y-m-d H:i:s')
            ])
            ->groupBy('c')
            ->orderBy('p.percent', 'DESC')->getQuery();

        $result = $query->getResult();
//        dump($result);exit;

        $promotions = [];

        foreach ($result as $promotion){
            $promotions[$promotion['id']] = $promotion['percent'];
            $promotions['creator'] = $promotion[0]->getUser();
        }
//        dump($promotions);exit;
        return $promotions;

    }

    public function getPromotionForAllProductsByRoleUser()
    {
        $qb = $this->createQueryBuilder('p');
        $today = new \DateTime();
        $query = $qb->select(['p, MAX(p.percent) as percent', 'r.id'])
            ->join('p.role', 'r')
            ->where($qb->expr()->lte('p.startDate', ':today'))
            ->andWhere($qb->expr()->gte('p.endDate', ':today'))
            ->andWhere('p.category is NULL')
            ->andWhere('p.productOffer is NULL')
            ->andWhere('p.role is not NULL')
            ->setParameters([
                'today' => $today->format('Y-m-d H:i:s')
            ])
            ->groupBy('r')
            ->orderBy('p.percent', 'DESC')
            ->getQuery();

        $result = $query->getResult();
//        dump($result);exit;

        $promotions = [];
        foreach ($result as $promotion){
            $promotions[$promotion['id']] = $promotion['percent'];
            $promotions['creator'] = $promotion[0]->getUser();
        }
        return $promotions;
    }

    public function getPromotionsByProduct()
    {
        $qb = $this->createQueryBuilder('p');
        $today = new \DateTime();
        $query = $qb->select(['p, MAX(p.percent) as percent', 'pr.id'])
            ->join('p.productOffer', 'pr')
            ->where($qb->expr()->lte('p.startDate', ':today'))
            ->andWhere($qb->expr()->gte('p.endDate', ':today'))
            ->andWhere('p.category is NULL')
            ->andWhere('p.productOffer is not NULL')
            ->andWhere('p.role is NULL')
            ->setParameters([
                'today' => $today->format('Y-m-d H:i:s')
            ])
            ->groupBy('pr')
            ->orderBy('p.percent', 'DESC')
            ->getQuery();

        $result = $query->getResult();
//        dump($result);exit;

        $promotions = [];
        foreach ($result as $promotion){
            $promotions[$promotion['id']] = $promotion['percent'];
            $promotions['creator'] = $promotion[0]->getUser();
        }
        return $promotions;
    }

    public function getAllOrderedByDate()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.endDate', 'DESC')
            ->getQuery()
            ->execute();
    }
}