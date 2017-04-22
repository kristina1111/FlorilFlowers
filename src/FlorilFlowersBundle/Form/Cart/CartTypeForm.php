<?php

namespace FlorilFlowersBundle\Form\Cart;

use FlorilFlowersBundle\Entity\Cart\Cart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cartProducts', CollectionType::class, array(
                'label' => false,
                'entry_type' => CartProductTypeForm::class,
                'allow_delete' => true,
                'by_reference' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cart::class
            ]);
    }

    public function getBlockPrefix()
    {
        return 'floril_flowers_bundle_cart_type_form';
    }
}
