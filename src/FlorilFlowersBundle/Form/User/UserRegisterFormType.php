<?php

namespace FlorilFlowersBundle\Form\User;

use FlorilFlowersBundle\Entity\User\Role;
use FlorilFlowersBundle\Entity\User\User;
use FlorilFlowersBundle\Form\Product\TagFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nickname', null, array('label' => 'Nickname:'))
            ->add('email', null, array('label' => 'Email:'))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Password:'),
                'second_options' => array('label' => 'Repeat Password:'),))
            ->add('tags', CollectionType::class, array(
                'entry_type' => TagFormType::class,

                'allow_add' => true
            ))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);

    }

    public function getBlockPrefix()
    {

    }
}
