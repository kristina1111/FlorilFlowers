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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
//        check if the user is already logged in, if yes - redirect
        if ($this->getUser()) {
            $this->addFlash('info', 'You are already logged in!');
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(UserRegisterFormType::class);

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
        $form = $this->createForm(UserRegisterFormType::class);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();

            /** @var User $user */
            $user = $form->getData();

            $role = $em->getRepository(Role::class)->findOneBy(['type' => 'ROLE_USER']);

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
     * @Security("is_granted('ROLE_USER')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserProfileAction($id)
    {
//        users can see only their profiles
//        admins can access any user's profile
        if ($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))
        ) {
            $em = $this->getDoctrine()->getManager();

//      search the user by id in the repository; we cannot user $this->getUser because if admin access another user profile
//            we need to access this user profile
            /** @var User $user */
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
        return $this->redirectToRoute('user_profile_show', array(
            'id' => $this->getUser()->getId()
        ));

    }

    /**
     * @Route("/users/profile/edit/{id}", name="user_edit_profile")
     * @Security("is_granted('ROLE_USER')")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editProfileAction($id)
    {
//        users can edit only their profiles
//        admins can edit any user's profile
        if ($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))
        ) {
//      search the user by id in the repository; we cannot user $this->getUser because if admin access another user profile
//            we need to access this user profile
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);

            $form = $this->createForm(UserProfileFormType::class, $user);

            return $this->render(':FlorilFlowers/User/Profile:edit.html.twig', array(
                'form' => $form->createView(),
                'user' => $user
            ));
        }
        $this->addFlash('info', 'You can only edit your profile!');
        return $this->redirectToRoute('user_edit_profile', array(
            'id' => $this->getUser()->getId()
        ));
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
//        users can edit only their profiles
//        admins can edit any user's profile
        if ($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))
        ) {
            $em = $this->getDoctrine()->getManager();

//      search the user by id in the repository; we cannot user $this->getUser because if admin access another user profile
//            we need to access this user profile
            /** @var User $user */
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);

            $form = $this->createForm(UserProfileFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()) {
//                parse the information in the form with the user object
                $user = $form->getData();

//              check if the this->getUser is Admin
                if ($user != $this->getUser() && $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))) {
//                if admin is editing another user information, they won't enter password
                    $this->addFlash('error', "You successfully changed " . $user->getNickname() . " profile information!");
                } else if ($user == $this->getUser() && $this->get('security.password_encoder')->isPasswordValid($user, $form->get('checkPass')->getData())) {
//                  if the user is changing their own information and the current password they entered matches the pass in the db
                    if ($form->get('plainPassword')->getData() != null) {
//                  if the user wanted to change their password,
//                  then we change the plainPassword property that activates the hashPasswordListener
                        $user->setPlainPassword($form->get('plainPassword')->getData());
                    }
                    $this->addFlash('error', "You successfully changed your profile information!");
                } else {
//                    if none of the above was executed then the user must have entered wrong password
//                    and is immediately logged out
                    $this->addFlash('error', 'You entered invalid password!');
                    return $this->redirectToRoute('security_logout');
                }

//                the changing of the information in the db is here
                $em->persist($user);
                $em->flush();
                return $this->render('FlorilFlowers/User/Profile/show.html.twig',
                    [
                        'user' => $user
                    ]);
            }
//          if the form has errors
            $this->addFlash('error', "You didn't entered valid data!");
            return $this->render(':FlorilFlowers/User/Profile:edit.html.twig', array(
                'form' => $form->createView(),
                'user' => $user
            ));
        }
//        if user is not properly authenticated
        $this->addFlash('info', 'You can only edit your profile!');
        return $this->redirectToRoute('products_list');
    }

    /**
     * @Route("/user/{id}/products/all", name="user_bought_products_all")
     * @Security("is_granted('ROLE_USER')")
     */
    public function showBoughtProductsByUser($id)
    {
//        only user and admin have access to this information
        if ($this->getUser()->getId() == $id
            || $this->get('security.authorization_checker')->isGranted(new Expression('"ROLE_ADMIN" in roles'))
        ) {
//      search the user by id in the repository; we cannot user $this->getUser because if admin access another user profile
//            we need to access this user profile
            $user = $this->getDoctrine()->getRepository('FlorilFlowersBundle:User\User')->find($id);
//            /** @var Order[] $completedOrders */
//            $completedOrders = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\Order')->findByUserAndCompleted($user);
//           finds all the products that the user ever bought with their quantities
//          returns array with arrays - innerArray[0] is the cartProduct object, innerArray["quantityBought"] is the quantity
            $query = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Cart\CartProduct')->selectAllBoughtProductsWithQuantity($user);

            if (count($query) > 0) {
//add up-to-now sold quantities of each product that the user bought ( no matter they were announced for sale or not
                for ($i = 0; $i < count($query); $i++) {
//                   user can announce product for sale
//                  for every bought product check we need to check if the user has announced this product for sale
                    $product = $query[$i][0]->getOffer()->getProduct();
                    $userOffer = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->getProductOfferByCreatorAndProduct($user, $product );

                    $query[$i]['quantitySold'] = 0;
                    if ($userOffer) {
//                    find the sold quantity for every productOffer of the user
                        $queryQnSold = $this->getDoctrine()->getRepository('FlorilFlowersBundle:Product\ProductOffer')->findUserSoldProduct($userOffer[0]);
//                        dump($queryQnSold);exit;
                        if ($queryQnSold) {
//                            if there are sold quantities (otherwise the query returns [] ),
//                            we assign them to the result query of the bought products
//                            so that we can send them to the view
                            $query[$i]['quantitySold'] = $queryQnSold[0]['quantitySold'];
                        }
                    }
                }
//                assign the price calculator to a variable so that we can send it to the view
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
