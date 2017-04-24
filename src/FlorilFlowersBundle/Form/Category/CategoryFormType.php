<?php

namespace FlorilFlowersBundle\Form\Category;

use FlorilFlowersBundle\Entity\Category\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Category name:'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class
            ]);
    }

    public function getBlockPrefix()
    {
        return 'floril_flowers_bundle_category_form_type';
    }
}
