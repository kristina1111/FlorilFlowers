<?php

namespace FlorilFlowersBundle\Controller\User;

use FlorilFlowersBundle\Form\User\LoginFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
//        check if the user is already logged in, if yes - redirect
        if($this->getUser()){
            $this->addFlash('info', 'You are already logged in!');
            return $this->redirectToRoute('homepage');
        }
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(
            LoginFormType::class,
            [
                '_username' => $lastUsername,
            ]);

        return $this->render('FlorilFlowers/Security/login.html.twig', array(
            'loginForm' => $form->createView(),
            'error' => $error,
        ));
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
//        doesn't get in here at all!
//        dump("YESS");exit;
//        $this->addFlash('success', 'You successfully logged out! Come again!');
    }
}
