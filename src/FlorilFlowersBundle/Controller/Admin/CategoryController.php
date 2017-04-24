<?php

namespace FlorilFlowersBundle\Controller\Admin;

use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\Category\Category;
use FlorilFlowersBundle\Form\Category\CategoryFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("admin/categories")
 * @Security("is_granted('ROLE_EDITOR')")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="create_categories_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoriesAction(Request $request)
    {
        /**
         * @var $categories Category
         */
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $form = $this->createForm(CategoryFormType::class);
        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()){

            /**
             * @var Category $category
             */
            $category = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'You added new category "' . $category->getName() . '"');
            return $this->redirectToRoute('create_categories_list');
        }

        return $this->render('FlorilFlowers/Admin/Category/list.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="categories_edit")
     * @Security("is_granted('ROLE_EDITOR')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editCategoryAction($id, Request $request)
    {
        /**
         * @var $categories Category
         */
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if(!$category){
            $this->addFlash('info', 'No such category exists');
            return $this->redirectToRoute('create_categories_list');
        }

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'You edited category "' . $category->getName() . '"');
            return $this->redirectToRoute('create_categories_list');
        }

        return $this->render('FlorilFlowers/Admin/Category/list.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_category")
     * @Security("is_granted('ROLE_EDITOR')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCategoryAction($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
//        dump($category);exit;
        if($category->getProducts()->count()>0){
            $this->addFlash('info', 'There are products under this category. You should change those products category before deleting the category!');
            return $this->redirectToRoute('products_list');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'You successfully deleted category ' . $category->getName() . '!');
        return $this->redirectToRoute('create_categories_list');
    }
}
