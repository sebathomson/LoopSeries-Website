<?php

namespace LoopAnime\UsersBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use LoopAnime\GeneralBundle\Entity\Countries;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function __construct($class, EntityManager $entityManager)
    {
        parent::__construct($class);
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $em = $this->em->getRepository('LoopAnimeGeneralBundle:Countries');
        /** @var Countries[] $countries */
        $countries = $em->findAll();

        $country_arr = [];
        foreach($countries as $country) {
            $country_arr[$country->getIso2()] = $country->getDescription();
        }

        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('birthdate','date',array(
                'widget' => 'single_text',
                'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day')
            ))
            ->add('country','choice', array(
                'choices' => $country_arr,
                'empty_value' => 'Choose an option',
            ))
            ->add('newsletter','checkbox', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'loopanime_user_registration';
    }

}