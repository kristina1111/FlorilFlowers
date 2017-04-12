<?php

namespace FlorilFlowersBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', null, array('label' => 'Email:'))
            ->add('_password', PasswordType::class, array('label' => 'Password:'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

}
