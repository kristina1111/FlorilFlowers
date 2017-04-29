<?php

namespace FlorilFlowersBundle\Form\User;

use FlorilFlowersBundle\Entity\User\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, array(
                'label' => 'First name:',
                'required' => false
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'Last name:',
                'required' => false
            ))
            ->add('email', TextType::class, array(
                'label' => 'Email:*'
            ))
            ->add('nickname', TextType::class, array(
                'label' => 'Nickname:*'
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => false,
                'first_options'  => array('label' => 'New Password:'),
                'second_options' => array('label' => 'Repeat New Password:'),
                'mapped' => false
                ))
            ->add('checkPass', PasswordType::class, array(
                'label' => 'Enter your password:*',
                'required' => false,
                'mapped' => false
            ))->add('role', EntityType::class, array(
                    'label' => 'Role:',
                    'class' => 'FlorilFlowersBundle\Entity\User\Role',
                'choice_label'=>'type'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'floril_flowers_bundle_user_profile_form_type';
    }
}
