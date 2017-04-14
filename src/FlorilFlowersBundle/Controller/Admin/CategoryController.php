<?php

namespace FlorilFlowersBundle\Controller\Admin;

use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\Category\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("admin/categories")
 * @Security("is_granted('ROLE_EDITOR')")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="admin_categories_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoriesAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render(':FlorilFlowers/Admin/Category:list.html.twig',
            ['categories' => $categories]
            );
    }
}
