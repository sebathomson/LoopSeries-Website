<?php

namespace LoopAnime\UsersBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('invitation', 'loopanime_invitation_type');
    }

    public function getName()
    {
        return 'loopanime_user_registration';
    }
}
