<?php

namespace FlorilFlowersBundle\Twig\Extension;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FlorilFlowersBundle\Entity\Category\Category;


class DatabaseGlobalsExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
//http://stackoverflow.com/questions/27965739/how-to-have-a-global-variable-coming-from-db-in-symfony-template
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getGlobals()
    {
        /**
         * @var Category[]|ArrayCollection
         */
        $categories = $this->em->getRepository('FlorilFlowersBundle:Category\Category')->findAll();
        return array(
            'categories' => $categories
        );
    }

    public function getName()
    {
        return "FlorilFlowersBundle:DatabaseGlobalsExtension";
    }
}
