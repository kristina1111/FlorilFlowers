<?php

namespace FlorilFlowersBundle\Form\Cart;

use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\Cart\Order;
use FlorilFlowersBundle\Entity\User\UserPhone;
use FlorilFlowersBundle\Form\User\AddressFormType;
use FlorilFlowersBundle\Form\User\PhoneFormType;
use FlorilFlowersBundle\Repository\User\UserAddressRepository;
use FlorilFlowersBundle\Repository\User\UserPhoneRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', AddressFormType::class, array(
                'required' => false,
//                'mapped' => false
            ))
            ->add('addresses', EntityType::class, array(
                'class' => 'FlorilFlowersBundle\Entity\User\UserAddress',
                'query_builder' => function(UserAddressRepository $er)use ($options){
                    return $er->getUserAddresses($options['user']);
                },
                'label' => 'Choose address from your previous orders:',
                'placeholder' => 'Choose address',
                'choice_label' => 'address',
                'required' => false,
//                'mapped' => false
            ))
            ->add('phone', PhoneFormType::class, array(
                'required' => false,
//                'mapped' => false
            ))
        ->add('phones', EntityType::class, array(
            'class' => 'FlorilFlowersBundle\Entity\User\UserPhone',
            'query_builder' => function(UserPhoneRepository $er) use ($options){
                return $er->getUserPhones($options['user']);
            },
            'label' => 'Choose phone from your previous orders:',
            'placeholder' => 'Choose phone',
            'choice_label' => 'phoneNumber',
            'required' => false,
//            'mapped' => false
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
//            'data_class'=> Order::class,
            'user' => false
        ]);
    }

//    public function getBlockPrefix()
//    {
//        return 'floril_flowers_bundle_order_form_type';
//    }
}
