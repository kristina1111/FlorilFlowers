<?php

namespace FlorilFlowersBundle\Form\Cart;

use FlorilFlowersBundle\Entity\Cart\Order;
use FlorilFlowersBundle\Form\User\AddressFormType;
use FlorilFlowersBundle\Form\User\PhoneFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', AddressFormType::class)
            ->add('phone', PhoneFormType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> Order::class
        ]);
    }

//    public function getBlockPrefix()
//    {
//        return 'floril_flowers_bundle_order_form_type';
//    }
}
