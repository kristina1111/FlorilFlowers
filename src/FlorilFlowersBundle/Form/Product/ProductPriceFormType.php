<?php

namespace FlorilFlowersBundle\Form\Product;

use FlorilFlowersBundle\Entity\Product\ProductPrice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPriceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('retailPrice', MoneyType::class, array('label' => 'Price: '))
            ->add('currency', EntityType::class, [
                'class' => 'FlorilFlowersBundle\Entity\Product\Currency',
                'label' => 'Currency: '
//                'placeholder' => 'Choose currency'
            ])
            ->add('startDate', DateType::class, array(
                'label' => 'Start date: ',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ],
                'html5' => false
            ))
            ->add('endDate', DateType::class, array(
                'label' => 'End date: ',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ],
                'html5' => false
            ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPrice::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'floril_flowers_bundle_product_price_form_type';
    }
}
