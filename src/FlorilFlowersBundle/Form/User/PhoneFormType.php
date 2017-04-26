<?php

namespace FlorilFlowersBundle\Form\User;

use FlorilFlowersBundle\Entity\User\UserPhone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', TextType::class, array(
                'label' => 'Enter new phone number',
                'attr' => array(
                    'placeholder' => 'e.g. 0888888888'
                )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPhone::class
        ]);
    }

//    public function getBlockPrefix()
//    {
//        return 'floril_flowers_bundle_phone_form_type';
//    }
}
