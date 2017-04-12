<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.4.2017 г.
 * Time: 16:19 ч.
 */

namespace FlorilFlowersBundle\Service\FormFlow;


use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class RegisterUserFlow extends FormFlow
{
    public function loadRegisterForm()
    {
        return array(
            array(
                'label' => 'userInfo',
                'form_type' => 'FlorilFlowers\Form\UserRegisterFormType',
            ),
            array(
                'label' => 'role',
                'form_type' => 'FlorilFlowers\Form\RoleFormType',
                'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
                }
            ),
            array(
                'label' => 'confirmation',
            ),
        );
    }
}