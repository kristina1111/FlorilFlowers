<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8.4.2017 г.
 * Time: 14:19 ч.
 */

namespace FlorilFlowersBundle\Security;

use FlorilFlowersBundle\Form\User\LoginFormType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    public function __construct(FormFactoryInterface $formFactory,
                                EntityManager $em,
                                RouterInterface $router,
                                UserPasswordEncoder $passwordEncoder)
    {

        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' &&
            $request->isMethod('POST');

        // if not login form is submitted, return null, then Symfony skips trying to
        // authenticate the user and the request continues
        if(!$isLoginSubmit) {
            return;
        }
        // if the user is trying to login, we need to fetch the username and password and return them

        $form = $this->formFactory->create(LoginFormType::class);
        $form->handleRequest($request);

        $data = $form->getData();

        $request->getSession()->set(Security::LAST_USERNAME, $data['_username']);

//        dump($request->getSession());exit;

        return $data;
        // the authentication continues with the next method below

    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];

        // if return null, authentication fails
        return $this->em->getRepository('FlorilFlowersBundle:User\User')
            ->findOneBy(['email' => $username]);
        // if returns an user object, the authentication continues with the method below

    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];

        if($this->passwordEncoder->isPasswordValid($user, $password))
        {
            //if true, continues with the next method below
            return true;
        }
        return false;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('security_login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('homepage');
    }


}