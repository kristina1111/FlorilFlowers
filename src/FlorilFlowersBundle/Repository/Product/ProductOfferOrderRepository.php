<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.4.2017 г.
 * Time: 12:31 ч.
 */

namespace FlorilFlowersBundle\Repository\Product;


use Doctrine\ORM\EntityRepository;

class ProductOfferOrderRepository extends EntityRepository
{
    public function findActiveOrder()
    {
        return $this->createQueryBuilder('poo')
//            ->select('poo.name, poo.descOrAsc')
            ->where('poo.activatedOn is not NULL')
            ->setMaxResults(1)
            ->getQuery()
            ->execute();
    }

    public function findAllOrders()
    {
//        dump($this->createQueryBuilder('poo')
//            ->select('poo'));exit;
        return $this->createQueryBuilder('poo')
            ->select('poo');
    }

    public function setActivatedOnToNull()
    {
        $this->createQueryBuilder('poo')
            ->update('FlorilFlowersBundle:Product\ProductOfferOrder', 'poo')
            ->set('poo.activatedOn', ':null')
            ->setParameter('null', null)
            ->getQuery()->execute();
    }

//    public function findAllOrdersNames()
//    {
//        $query = $this->createQueryBuilder('poo')
//            ->select('poo.name, poo.id')
//            ->getQuery()
//        ->getResult();
//
////        dump($query);exit;
////
////        $result = [];
////        foreach ($query as $order){
////            $result[$order['name']] = $order['id'];
////        }
////        dump($result);exit;
////            array_map(function ($a){
//////            dump($a['name']);exit;
////            $var = $a['name'];
////            return [$a[$var] => $a['id']];
////        }, $query);
////                dump($result);exit;
////        return $query;
//    }
}