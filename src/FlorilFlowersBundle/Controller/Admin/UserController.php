<?php

namespace FlorilFlowersBundle\Controller\Admin;

use FlorilFlowersBundle\Entity\User\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @Route("/admin/users")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends Controller
{
    /**
     * @Route("/all", name="show_all_users")
     */
    public function showAllUsers()
    {
        if ($this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {
            /** @var User[] $allUsers */
            $allUsers = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->findAll();
            return $this->render(':FlorilFlowers/Admin/User:all.html.twig', array(
                'allUsers' =>$allUsers
            ));

        }else{
            $this->addFlash('error', "You don't have access to this page!");
            return $this->redirectToRoute('homepage');
        }
    }
}
