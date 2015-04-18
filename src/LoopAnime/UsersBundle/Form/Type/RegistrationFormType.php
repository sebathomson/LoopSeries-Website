<?php

namespace LoopAnime\UsersBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label' => 'form.username',
                'translation_domain' => 'FOSUserBundle',
                'label_attr' => ['class' => 'font-bold pull-left'],
                'attr' => ['class' => 'form-control input-small', 'placeholder' => 'Username']
            ])
            ->add('email', 'email', [
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'label_attr' => ['class' => 'font-bold pull-left'],
                'attr' => ['class' => 'form-control input-small', 'placeholder' => 'someone@email.com']
            ])
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => [
                    'label' => 'form.password',
                    'label_attr' => ['class' => 'font-bold pull-left'],
                    'attr' => ['class' => 'form-control input-small', 'placeholder' => 'Your Password']
                ],
                'second_options' => [
                    'label' => 'form.password_confirmation',
                    'label_attr' => ['class' => 'font-bold pull-left'],
                    'attr' => ['class' => 'form-control input-small', 'placeholder' => 'Re-Type your password']
                ],
                'invalid_message' => 'fos_user.password.mismatch',
            ]);
    }

    public function getName()
    {
        return 'loopanime_user_registration';
    }
}
