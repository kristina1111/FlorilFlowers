<?php

namespace FlorilFlowersBundle\Form\Product;

use FlorilFlowersBundle\Repository\Product\CategoryRepository;
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
                'class' => 'FlorilFlowersBundle\Entity\Product\Category',
                'placeholder' => 'Choose a category',
                'query_builder' => function(CategoryRepository $repository){
                return $repository->createAlphabeticalQueryBuilder();
                }
            ])
            ->add('quantity')
            ->add('unitMeasure')
            ->add('description')
            ->add('isPublished');
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
