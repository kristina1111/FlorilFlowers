<?php
//
//namespace FlorilFlowersBundle\Form\User;
//
//use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\PasswordType;
//use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolver;
//
//class RegisterFormType extends AbstractType
//{
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        switch ($options['flow_step']){
//            case 1:
//                $builder
//                    ->add('nickname', null, array('label' => 'Nickname:'))
//                    ->add('email', null, array('label' => 'Email:'))
//                    ->add('plainPassword', RepeatedType::class, array(
//                        'type' => PasswordType::class,
//                        'invalid_message' => 'The password fields must match.',
//                        'options' => array('attr' => array('class' => 'password-field')),
//                        'required' => true,
//                        'first_options'  => array('label' => 'Password:'),
//                        'second_options' => array('label' => 'Repeat Password:'),));
//                break;
//            case 2:
//                $builder
//                    ->add('type', TextType::class, array(
//                        'label' => 'Role:',));
//                break;
//
//        }
//    }
//
//    public function configureOptions(OptionsResolver $resolver)
//    {
//
//    }
//
//    public function getBlockPrefix()
//    {
//
//    }
//}
