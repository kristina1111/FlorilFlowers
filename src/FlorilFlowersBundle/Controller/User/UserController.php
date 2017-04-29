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

        if ($form->isValid() && $form->isSubmitted()) {

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
        if ($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))
        ) {
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('FlorilFlowersBundle:User\User')->findOneBy(['id' => $id]);

            if (!$user) {
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
        if ($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))
        ) {

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
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editProfileActionProcess($id, Request $request)
    {
//        dump($this->getUser()->getId() == $id);exit;
        if ($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))
        ) {
            $em = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);

            $form = $this->createForm(UserProfileFormType::class, $user);
            $form->handleRequest($request);
//            dump($this->get('security.password_encoder')->isPasswordValid($user, $form->get('checkPass')->getData()));exit;
            if ($form->isValid() && $form->isSubmitted()) {
                if ($user != $this->getUser() && $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {
                    $user = $form->getData();
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('error', "You successfully changed ". $user->getNickname() ." profile information!");
                    return $this->render('FlorilFlowers/User/Profile/show.html.twig',
                        [
                            'user' => $user
                        ]);
                }
                if ($this->get('security.password_encoder')->isPasswordValid($user, $form->get('checkPass')->getData())) {
                    $user = $form->getData();
                    if ($form->get('plainPassword')->getData() != null) {
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
                } else {
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
        if ($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))
        ) {

            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);
            /** @var Order[] $completedOrders */
            $completedOrders = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Order')->findByUserAndCompleted($user);
            // finds all the products that the user ever bought with their quantities
            //returns array with arrays - innerArray[0] is the object, innerArray["quantityBought"] is the quantity
            $query = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\CartProduct')->selectAllBoughtProductsWithQuantity($user);
//            dump($query);exit;
//            add up to now sold quantities of each product that the user bought ( no matter they were announced for sale or not)
            if (count($query) > 0) {
//
                for ($i = 0; $i < count($query); $i++) {
//                    dump($query[0][0]->getOffer()->getProduct());exit;
//                    check if user has announced this product for sale
                    $hasUserOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->getProductOfferByCreatorAndProduct($user, $query[$i][0]->getOffer()->getProduct());
                    $queryQnSold = 0;
                    if ($hasUserOffer) {
                        $queryQnSold = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->findUserSoldProduct($hasUserOffer[0]);
                    }
//                    find the sold quantity for every productOffer of the user

                    if ($queryQnSold) {
                        $queryQnSold = $queryQnSold[0]['quantitySold'];
//                        dump($queryQnSold);exit;
                    } else {
                        $queryQnSold = 0;
                    }
                    $query[$i]['quantitySold'] = $queryQnSold;
//                    dump($product);exit;
                }
                $priceCalculator = $this->get('app.price_calculator');
//                dump($query);exit;
                return $this->render(':FlorilFlowers/User/Products:list-bought-products.html.twig', array(
                    'boughtProducts' => $query,
                    'priceCalculator' => $priceCalculator,
                    'user' => $user
                ));
            } else {
                if ($this->getUser()->getId() != $id && $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {
                    $this->addFlash('info', "This user hasn't bought any products!");
                    return $this->redirectToRoute('show_all_users');
                }
            }
            $this->addFlash('info', "You haven't bought any products! It's time to shop!");
            return $this->redirectToRoute('products_list');

            //            dump($query[0]);exit;
//            $query[0][0]->getOffer()->getProduct()->getName()
        }
        $this->addFlash('info', 'You cannot see other users bought products!');
        return $this->redirectToRoute('products_list');
    }
}
