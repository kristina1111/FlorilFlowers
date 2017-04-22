<?php

namespace FlorilFlowersBundle\Form\Product;

use FlorilFlowersBundle\Entity\Product\Product;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', ProductFormType::class)
            ->add('description', TextareaType::class, array('label' => 'Offer description:'))
            ->add('quantityForSale', IntegerType::class, array('label' => 'Quantity available:'))
            ->add('retailPrice', MoneyType::class, array(
                'label' => 'Price Amount:',
                'currency'=>'',
                ))
            ->add('currency', EntityType::class, [
                'class' => 'FlorilFlowersBundle\Entity\Product\Currency',
                'label' => 'Currency:'
//                'placeholder' => 'Choose currency'
            ])
//            ->add('productPrices', CollectionType::class, array(
//                'label' => false,
//                'entry_type' => ProductPriceFormType::class,
//                'allow_add' => true,
//                'allow_delete' => true,
//                'by_reference' => false,
//            ))
            ->add('productImages', CollectionType::class,
                array(
                    'label' => false,
                    'entry_type' => ProductImageFormType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
            )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductOffer::class
        ]);
    }

//    public function getBlockPrefix()
//    {
//        return 'floril_flowers_bundle_product_offer_form_type';
//    }
}
