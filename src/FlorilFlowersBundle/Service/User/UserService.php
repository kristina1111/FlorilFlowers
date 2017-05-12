<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.5.2017 г.
 * Time: 11:47 ч.
 */

namespace FlorilFlowersBundle\Service\User;


use Doctrine\ORM\EntityManager;
use FlorilFlowersBundle\Entity\Cart\CartProduct;
use FlorilFlowersBundle\Entity\User\Role;
use FlorilFlowersBundle\Entity\User\User;
use Symfony\Component\Form\Form;

class UserService
{
    /** @var  EntityManager $em */
    private $em;

    /**
     * UserService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function registerUser(Form $form) : User
    {
        /** @var User $user */
        $user = $form->getData();

        $role = $this->em->getRepository(Role::class)->findOneBy(['type' => 'ROLE_USER']);

        $user->setRole($role);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function setSoldQuantitiesOfBoughtProductsByUser(User $user, array $boughtProducts)
    {
//add up-to-now sold quantities of each product that the user bought ( no matter they were announced for sale or not)
        for ($i = 0; $i < count($boughtProducts); $i++) {
//            set the sold quantities to 0 - here we accept that the user hasn't announced this product for sale yet.
            $boughtProducts[$i]['quantitySold'] = 0;
//     user can announce product for sale; for every bought product we need to check if the user has announced this product for sale
//     $boughtProducts[$i][0] - this is the cartProduct
            $product = $boughtProducts[$i][0]->getOffer()->getProduct();
//            we check if there are productOffer with that product, created by the user - this means that the user has already announced this product for sale
            $userOffer = $this->em->getRepository('FlorilFlowersBundle:Product\ProductOffer')->getProductOfferByCreatorAndProduct($user, $product );

            if ($userOffer) { // if such offer exists
//                    find the sold quantity for this productOffer of the user
                $queryQnSold = $this->em->getRepository('FlorilFlowersBundle:Product\ProductOffer')->findUserSoldProduct($userOffer[0]);
                if ($queryQnSold) {
//                            if there are sold quantities (otherwise the query returns [] ),
//                            we assign them to the result query of the bought products
//                            so that we can send them to the view
                    $boughtProducts[$i]['quantitySold'] = $queryQnSold[0]['quantitySold'];
                }
            }
        }

        return $boughtProducts;
    }

}