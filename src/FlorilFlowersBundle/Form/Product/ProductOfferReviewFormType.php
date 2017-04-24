<?php

namespace FlorilFlowersBundle\Form\Product;

use FlorilFlowersBundle\Entity\Product\ProductOfferReview;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOfferReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reviewText', TextareaType::class, array(
                'label' => 'Write your review here:',
                'attr' => array(
                    'class' => 'review-area'
                )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductOfferReview::class
        ]);
    }

//    public function getBlockPrefix()
//    {
////        return 'floril_flowers_bundle_product_review_form_type';
//    }
}
