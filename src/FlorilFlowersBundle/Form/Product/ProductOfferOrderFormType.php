<?php

namespace FlorilFlowersBundle\Form\Product;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\Product\ProductOfferOrder;
use FlorilFlowersBundle\Repository\Product\ProductOfferOrderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOfferOrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('name', ChoiceType::class, array(
//                'choices' => $this->buildProductOrderChoices(),
//                'label' => 'Choose order of the products'
//            ))
//                ->add('choice', EntityType::class, array(
//                'class' => 'FlorilFlowersBundle\Entity\Product\ProductOfferOrder',
//                'query_builder' => function(ProductOfferOrderRepository $er){
//                    return $er->findAllOrders();
//                },
////                'data' => $this->em->getReference('FlorilFlowersBundle:Product\ProductOfferOrder', 3),
//                'choice_label' => 'name',
////                'label' => 'Choose how to order the products:',
//                'placeholder' => 'Choose how to order the products',
//
//                'required' => false,
//            ))
            ->add('descOrAsc', CheckboxType::class, array(
                'required' => false,
                'label' => 'If checked - Descending',
                'mapped' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
//            'data_class'=> ProductOfferOrder::class,
//            'orderNames' => false
        ]);
    }

//    public function getBlockPrefix()
//    {
//        return 'floril_flowers_bundle_product_offer_order_form_type';
//    }

    protected function buildProductOrderChoices()
    {
//        $choices = [];
//        $productOrderObjects = function (ProductOfferOrderRepository $er){
//            return $er->findAll();
//        };
//        foreach ($productOrderObjects as $object){
//            dump($object);exit;
//        }
//        dump($productOrderObjects);exit;
    }
}
