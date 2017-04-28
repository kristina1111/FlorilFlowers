<?php

namespace FlorilFlowersBundle\Form\Promotion;

use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\Promotion\Promotion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Name:'
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Description:'
            ))
            ->add('percent', IntegerType::class, array(
                'label' => 'Percent:'
            ))
            ->add('startDate', DateTimeType::class, array(
                'label' => 'Start date:',
//                'widget' => 'single_text',
                'attr' => [
//                    'class' => 'js-datepicker1',
//                    'data_format' => 'dd/MM/yyyy hh:mm:ss'
                ],
//                'html5' => false
            ))
            ->add('endDate', DateTimeType::class, array(
                'label' => 'End date:',
//                'widget' => 'single_text',
                'attr' => [
//                    'class' => 'js-datepicker1'
                ],
//                'html5' => false
            ))
            ->add('category', EntityType::class, array(
                'label' => 'For category:',
                'class' => 'FlorilFlowersBundle\Entity\Category\Category',
                'placeholder' => 'Choose category',
                'choice_label' => 'name',
                'required' => false
            ))
            ->add('productOffer', EntityType::class, array(
                'label' => 'For product offer:',
                'class' => 'FlorilFlowersBundle\Entity\Product\ProductOffer',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('po')
                        ->join('po.user', 'u')
                        ->join('u.role', 'r')
                        ->where('r.id = 3 or r.id = 2')
                        ->orderBy('po.id', 'ASC');
                },
                'placeholder' => 'Choose product offer',
                'required' => false
            ))
            ->add('role', EntityType::class, array(
                'label' => 'For role:',
                'class' => 'FlorilFlowersBundle\Entity\User\Role',
                'placeholder' => 'Choose role',
                'choice_label' => 'type',
                'required' => false
            ));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class
        ]);
    }

//    public function getBlockPrefix()
//    {
//        return 'floril_flowers_bundle_promotion_form_type';
//    }
}
