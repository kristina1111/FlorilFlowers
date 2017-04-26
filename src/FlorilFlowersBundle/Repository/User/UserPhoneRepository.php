<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.4.2017 г.
 * Time: 14:55 ч.
 */

namespace FlorilFlowersBundle\Repository\User;


use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\User\User;

class UserPhoneRepository extends EntityRepository
{
    public function getUserPhones(User $user)
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameter('user', $user);
    }
}