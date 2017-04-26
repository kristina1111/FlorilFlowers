<?php

namespace FlorilFlowersBundle\Controller\User;

use FlorilFlowersBundle\Entity\Cart\Order;
use FlorilFlowersBundle\Entity\User\Role;
//use FlorilFlowersBundle\Entity\Product\Tag;
use FlorilFlowersBundle\Entity\User\User;
use FlorilFlowersBundle\Form\User\UserProfileFormType;
use FlorilFlowersBundle\Form\User\UserRegisterFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Expression;
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
//        $tag1 = new Tag();
//        $tag1->setName('tag1');
//        $user->getTags()->add($tag1);
//        $tag2 = new Tag();
//        $tag2->setName('tag2');
//        $user->getTags()->add($tag2);

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

        if($form->isValid() && $form->isSubmitted()){

//            dump($form);exit;
            $em = $this->getDoctrine()->getManager();

            /**
             * @var User $user
             */
            $user = $form->getData();

//            foreach ($user->getTags() as $tag)
//            {
//                $em->persist($tag);
//            }

            $role = $em->getRepository(Role::class)->findOneBy(['type' => 'ROLE_USER']);

//            $user->getRolesEntities()->add($role);

            $user->setRole($role);

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
        if($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){
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

        $this->addFlash('info', 'You can only access your profile!');
        return $this->redirectToRoute('products_list');

    }

    /**
     * @Route("/users/profile/edit/{id}", name="user_edit_profile")
     * @Method("GET")
     */
    public function editProfileAction($id)
    {
//        dump($this->getUser()->getId() == $id);exit;
        if($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){

            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);

            $form = $this->createForm(UserProfileFormType::class, $user);

            return $this->render(':FlorilFlowers/User/Profile:edit.html.twig', array(
                'form' => $form->createView(),
                'user' => $user
            ));
        }
        $this->addFlash('info', 'You can only edit your profile!');
        return $this->redirectToRoute('products_list');
    }

    /**
     * @Route("/users/profile/edit/{id}", name="user_edit_profile_process")
     * @Method("POST")
     */
    public function editProfileActionProcess($id, Request $request)
    {
//        dump($this->getUser()->getId() == $id);exit;
        if($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){
            $em = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);

            $form = $this->createForm(UserProfileFormType::class, $user);
            $form->handleRequest($request);
//            dump($this->get('security.password_encoder')->isPasswordValid($user, $form->get('checkPass')->getData()));exit;
            if($form->isValid() && $form->isSubmitted()){
                if($this->get('security.password_encoder')->isPasswordValid($user, $form->get('checkPass')->getData())){
                    $user = $form->getData();
                    if($form->get('plainPassword')->getData()!=null){
                        $user->setPlainPassword($form->get('plainPassword')->getData());
                    }
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('error', "You successfully changed your profile information!");
                    return $this->render('FlorilFlowers/User/Profile/show.html.twig',
                        [
                            'user' => $user
                        ]);
//                    dump($user);exit;
                }else{
                    $this->addFlash('error', 'You entered invalid password!');
                    return $this->redirectToRoute('security_logout');
                }
            }

            $this->addFlash('error', "You didn't entered valid data!");
            return $this->render(':FlorilFlowers/User/Profile:edit.html.twig', array(
                'form' => $form->createView(),
                'user' => $user
            ));
        }
        $this->addFlash('info', 'You can only edit your profile!');
        return $this->redirectToRoute('products_list');
    }

    /**
     * @Route("/user/{id}/products/all", name="user_bought_products_all")
     */
    public function showBoughtProductsByUser($id)
    {
        if($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))){

            /** @var Order[] $completedOrders */
            $completedOrders = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Order')->findByUserAndCompleted($this->getUser());
            $query = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\CartProduct')->selectAllBoughtProductsWithQuantity($this->getUser());

            return $this->render(':FlorilFlowers/User/Products:list-bought-products.html.twig', array(
                'boughtProducts' => $query
            ));

            //            dump($query[0]);exit;
//            $query[0][0]->getOffer()->getProduct()->getName()
        }
        $this->addFlash('info', 'You cannot see other users bought products!');
        return $this->redirectToRoute('products_list');
    }
}
