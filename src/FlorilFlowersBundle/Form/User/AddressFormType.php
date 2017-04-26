<?php

namespace FlorilFlowersBundle\Form\User;

use FlorilFlowersBundle\Entity\User\UserAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class, array(
                'label' => 'Enter shipment address',
                'attr' => array(
                    'placeholder' => 'City, full address'
                )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAddress::class
        ]);
    }

//    public function getBlockPrefix()
//    {
//        return 'floril_flowers_bundle_address_form_type';
//    }
}
