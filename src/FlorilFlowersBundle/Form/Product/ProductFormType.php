<?php

namespace FlorilFlowersBundle\Form\Product;

use FlorilFlowersBundle\Repository\Category\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('category', EntityType::class, [
                'class' => 'FlorilFlowersBundle\Entity\Category\Category',
                'placeholder' => 'Choose category',
//                'query_builder' => function(CategoryRepository $repository){
//                return $repository->createAlphabeticalQueryBuilder();
//                }
            ])
            ->add('subcategory', EntityType::class, [
                'class' => 'FlorilFlowersBundle\Entity\Category\Subcategory',
                'placeholder' => 'Choose subcategory',
            ])
            ->add('unitMeasure')
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'FlorilFlowersBundle\Entity\Product\Product'
        ]);
    }

//    public function getBlockPrefix()
//    {
////
//    }
}
