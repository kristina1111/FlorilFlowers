<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6.4.2017 г.
 * Time: 20:49 ч.
 */

namespace FlorilFlowersBundle\Entity\User;


use FlorilFlowersBundle\Entity\Cart\Cart;
use FlorilFlowersBundle\Entity\Cart\Order;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\Product\ProductOfferReview;
use FlorilFlowersBundle\Entity\Product\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @UniqueEntity(fields={"nickname"}, message="This nickname is taken!")
 * @UniqueEntity(fields={"email"}, message="Looks like you already have an account!")
 */
class User implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_EDITOR = 'ROLE_EDITOR';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", unique=true)
     */
    private $nickname;

    /**
     *
     * @ORM\Column(type="string", name="first_name", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", name="last_name", nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetimeRegistered;

    /**
     * @ORM\Column(type="string")
     */
    private $password;


    // only for encryption purposes, a temporary storage place during single request
    /**
     * @Assert\NotBlank(groups={"Registration"})
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOfferReview", mappedBy="user")
     * @var ProductOfferReview[]|ArrayCollection
     */
    private $productOfferReviews;

    /**
     * @ORM\ManyToMany(targetEntity="FlorilFlowersBundle\Entity\Product\Tag", inversedBy="users")
     * @ORM\JoinTable(name="users_tags")
     * @var Tag[]|ArrayCollection
     */
    private $tags;

//    /**
//     * @ORM\ManyToMany(targetEntity="FlorilFlowersBundle\Entity\User\Role", inversedBy="users")
//     * @ORM\JoinTable(name="users_roles")
//     * @var Role[]|ArrayCollection
//     */
//    private $roles;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\Role", inversedBy="users")
     * @var Role
     */
    private $role;

//    /**
//     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\User\UserAddress", mappedBy="user")
//     * @var UserAddress[]|ArrayCollection
//     */
//    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\User\UserPhone", mappedBy="user")
     * @var UserPhone[]|ArrayCollection
     */
    private $phones;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer", mappedBy="user")
     * @var ProductOffer[]|ArrayCollection
     */
    private $productOffers;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Cart\Order", mappedBy="user")
     * @var Order[]|ArrayCollection
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Cart\Cart", mappedBy="user")
     * @var Cart[]|ArrayCollection
     */
    private $carts;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $cash;

    /**
     *
     * @var ProductOffer[]|ArrayCollection
     */
    private $favouriteOffers;

    public function __construct()
    {
        $this->datetimeRegistered = new \DateTime('now');
        $this->productOfferReviews = new ArrayCollection();
        $this->tags = new ArrayCollection();
//        $this->roles = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->productOffers = new ArrayCollection();
        $this->favouriteOffers = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->carts = new ArrayCollection();
        $this->cash = 100;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }



    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return ProductOfferReview[]|ArrayCollection
     */
    public function getProductOfferReviews()
    {
        return $this->productOfferReviews;
    }

    /**
     * @param ProductOfferReview[]|ArrayCollection $productOfferReviews
     */
    public function setProductOfferReviews($productOfferReviews)
    {
        $this->productOfferReviews = $productOfferReviews;
    }

    /**
     * @return Tag[]|ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag[]|ArrayCollection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return ProductOffer[]|ArrayCollection
     */
    public function getProductOffers()
    {
        return $this->productOffers;
    }

    /**
     * @param ProductOffer[]|ArrayCollection $productOffers
     */
    public function setProductOffers($productOffers)
    {
        $this->productOffers = $productOffers;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return UserPhone[]|ArrayCollection
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param UserPhone[]|ArrayCollection $phones
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
    }

    /**
     * @return ArrayCollection|ProductOffer[]
     */
    public function getFavouriteOffers()
    {
        return $this->favouriteOffers;
    }

    /**
     * @param ArrayCollection|ProductOffer[] $favouriteOffers
     */
    public function setFavouriteOffers($favouriteOffers)
    {
        $this->favouriteOffers = $favouriteOffers;
    }




    /*
     * used mainly for debugging purposes
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return array($this->role->getType());
    }

    /**
     * @param Role $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

//    /**
//     * @return array
//     */
//    public function getRoles()
//    {
//        // need to return array
//        $roles = array_map(function ($r){
//            /** @var Role $r  */
//            return $r->getType();
//        }, $this->roles->toArray());
////        dump($roles);die;
//        return $roles;
//    }

//    /**
//     * @return Role[]|ArrayCollection
//     */
//    public function getRolesEntities()
//    {
//        return $this->roles;
//    }

//    /**
//     * @param Role[]|ArrayCollection $roles
//     */
//    public function setRoles($roles)
//    {
//        $this->roles = $roles;
//    }






    /**
     * @return UserAddress[]|ArrayCollection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param UserAddress[]|ArrayCollection $addresses
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * @return ArrayCollection|Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection|Order[] $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return ArrayCollection|Cart[]
     */
    public function getCarts()
    {
        return $this->carts;
    }

    public function getActiveCart()
    {

    }

    /**
     * @param ArrayCollection|Cart[] $carts
     */
    public function setCarts($carts)
    {
        $this->carts = $carts;
    }

    /**
     * @return mixed
     */
    public function getCash()
    {
        return $this->cash;
    }

    /**
     * @param mixed $cash
     */
    public function setCash($cash)
    {
        $this->cash = $cash;
    }




    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;
        // the Doctrine listener needs this because Doctrine listeners are not called if
        // Doctrine thinks that an object has not been updated.
        // If you eventually create a "change password" form, then the only property that will be updated is
        // plainPassword. Since this is not persisted, Doctrine will think the object is "un-changed",
        // or "clean". In that case, the listeners will not be called, and the password will not be changed.
        // But by adding this line, the object will always look like it has been changed.

    }

//    for user access to their products, reviews... whatever they are authors to.

    /**
     * @param $entity ProductOffer | ProductReview // or any entity that has user author
     * @return bool
     */
    public function isAuthor($entity)
    {
        if($entity->getUser()->getId() == $this->getId()){
            return true;
        }
        return false;
    }
//
//    public function isAdmin()
//    {
//        return in_array(self::ROLE_ADMIN, $this->getRoles());
//    }
//
//    public function isEditor()
//    {
//        return in_array(self::ROLE_EDITOR, $this->getRoles());
//    }

}