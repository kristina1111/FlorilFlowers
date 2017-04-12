<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3.4.2017 г.
 * Time: 14:43 ч.
 */

namespace FlorilFlowersBundle\DataFixtures\ORM;


use FlorilFlowersBundle\Entity\Product;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        Fixtures::load(__DIR__ .
            '/fixtures.yaml',
            $manager,
            ['providers' => [$this]]);
    }


    //Function for creating fake data for product names
    public function productName()
    {
        $productName = [
            'Tulip',
            'Rose',
            'Lily',
            'Dahlia',
            'Daffodil',
        ];

        $key = array_rand($productName);

        return $productName[$key];
    }

    //Function for creation fake data for product categories
    public function productCategory()
    {
        $productCategory = [
            'Aquebono',
            'Angelique',
            'Apricot Impression',
            'Black parrot',
            'Blushing Lady',
            'Boston',
            'Caroussel',
        ];

        $key = array_rand($productCategory);

        return $productCategory[$key];
    }

    public function userRoles()
    {
        $userRoles = [
            'ROLE_USER',
            'ROLE_ADMIN',
            'ROLE_EDITOR',
        ];

        $key = array_rand($userRoles);

        return $userRoles[$key];
    }
}