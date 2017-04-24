<?php

namespace FlorilFlowersBundle\Form\Product;

use FlorilFlowersBundle\Repository\Category\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Product name:'
            ))
            ->add('category', EntityType::class, [
                'label' => 'Category:',
                'class' => 'FlorilFlowersBundle\Entity\Category\Category',
                'placeholder' => 'Choose category',
//                'query_builder' => function(CategoryRepository $repository){
//                return $repository->createAlphabeticalQueryBuilder();
//                }
            ])
//            ->add('subcategory', EntityType::class, [
//                'label' => 'Subcategory:',
//                'class' => 'FlorilFlowersBundle\Entity\Category\Subcategory',
//                'placeholder' => 'Choose subcategory',
//            ])
//            ->add('unitMeasure')
            ->add('description', TextareaType::class, [
                'label' => 'Product description:'
            ]);
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
