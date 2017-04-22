<?php

namespace FlorilFlowersBundle\Form\Cart;

use FlorilFlowersBundle\Entity\Cart\CartProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartProductTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CartProduct::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'floril_flowers_bundle_cart_product_type_form';
    }
}
