<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6.4.2017 г.
 * Time: 20:52 ч.
 */

namespace FlorilFlowersBundle\Entity\User;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\User\RoleRepository")
 * @ORM\Table(name="roles")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

//    /**
//     * @ORM\ManyToMany(targetEntity="FlorilFlowersBundle\Entity\User\User", mappedBy="roles")
//     * @var User[]|ArrayCollection
//     */
//    private $users;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\User\User", mappedBy="role")
     * @var User[]|ArrayCollection
     */
    private $users;

//    /**
//     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\User\UserRoleAssociation", mappedBy="role")
//     * @var UserRoleAssociation[]|ArrayCollection
//     */
//    private $user_role_associations;

    public function __construct()
    {
//        $this->user_role_associations = new ArrayCollection();
        $this->users = new ArrayCollection();
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }



//    /**
//     * @return UserRoleAssociation[]|ArrayCollection
//     */
//    public function getUserRoleAssociations()
//    {
//        return $this->user_role_associations;
//    }

//    /**
//     * @param UserRoleAssociation[]|ArrayCollection $user_role_associations
//     */
//    public function setUserRoleAssociations($user_role_associations)
//    {
//        $this->user_role_associations = $user_role_associations;
//    }


}