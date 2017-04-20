<?php

namespace FlorilFlowersBundle\Form\Product;

use FlorilFlowersBundle\Entity\Product\ProductImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, array(
                'label' => 'Upload image',
                'required' => false
            ))
            ->add('isFrontImage', CheckboxType::class, array(
                'label'    => 'Make this image main image for the offer.',
                'required' => false,
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Add description',
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => ProductImage::class]
        );
    }

    public function getBlockPrefix()
    {
        return 'floril_flowers_bundle_product_image_form_type';
    }
}
