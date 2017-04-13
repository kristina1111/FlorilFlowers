<?php

namespace FlorilFlowersBundle\Controller\User;

use FlorilFlowersBundle\Entity\User\Role;
use FlorilFlowersBundle\Entity\Product\Tag;
use FlorilFlowersBundle\Entity\User\User;
use FlorilFlowersBundle\Form\User\UserRegisterFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction()
    {
        $user = new User();
        $tag1 = new Tag();
        $tag1->setName('tag1');
        $user->getTags()->add($tag1);
        $tag2 = new Tag();
        $tag2->setName('tag2');
        $user->getTags()->add($tag2);

//        dump($user);exit;
        $form = $this->createForm(UserRegisterFormType::class, $user);

        return $this->render('FlorilFlowers/User/register.html.twig', [
            'formRegister' => $form->createView()
        ]);
    }

    /**
     * @Route("/register", name="user_register_process")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerActionProcess(Request $request)
    {
//        $user = new User();

        $form = $this->createForm(UserRegisterFormType::class);
//        dump($form);exit;
        $form->handleRequest($request);

        if($form->isValid()){

//            dump($form);exit;
            $em = $this->getDoctrine()->getManager();

            /**
             * @var User $user
             */
            $user = $form->getData();

            foreach ($user->getTags() as $tag)
            {
                $em->persist($tag);
            }

            $role = $em->getRepository(Role::class)->findOneBy(['type' => 'ROLE_USER']);

            $user->getRolesEntities()->add($role);


            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome ' . $user->getNickname());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );

        }

        return $this->render('FlorilFlowers/User/register.html.twig', [
            'formRegister' => $form->createView()
        ]);
    }

    /**
     * @Route("users/profile/{id}", name="user_profile_show")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserProfileAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('FlorilFlowersBundle:User\User')->findOneBy(['id' => $id]);

        if(!$user){
            throw $this->createNotFoundException('No user found!');
        }

        return $this->render('FlorilFlowers/User/Profile/show.html.twig',
            [
                'user' => $user
            ]);
    }

    /**
     * @Route("/users/edit/{id}", name="user_edit")
     * @Method("GET")
     */
    public function editAction()
    {

    }
}
