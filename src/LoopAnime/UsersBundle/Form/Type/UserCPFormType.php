<?php

namespace LoopAnime\UsersBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use LoopAnime\GeneralBundle\Entity\Countries;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserCPFormType extends AbstractType
{

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $em = $this->em->getRepository('LoopAnimeGeneralBundle:Countries');
        /** @var Countries[] $countries */
        $countries = $em->findAll();

        $country_arr = [];
        foreach ($countries as $country) {
            $country_arr[$country->getIso2()] = $country->getDescription();
        }

        $commonLabelAttr = ['class' => 'font-bold'];

        $builder
            ->add('username', null,
                array(
                    'label' => 'Username:',
                    'label_attr' => $commonLabelAttr,
                ))
            ->add('birthdate', 'date', array(
                'widget' => 'single_text',
                'label' => 'Birthday:',
                'label_attr' => $commonLabelAttr,
                'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day')
            ))
            ->add('newsletter', 'checkbox', array(
                'required' => false,
                'label' => 'Newsletter:',
                'label_attr' => $commonLabelAttr,
            ))
            ->add('buttonSubmit','submit',array(
                'label' => 'Submit Changes',
                'attr' => ['class' => 'btn btn-small btn-success']
            ))
            ->add('oldpassword', 'password', array(
                'label' => 'Old Password:',
                'label_attr' => $commonLabelAttr,
                'mapped' => false
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_options' => array('label' => 'Password:', 'label_attr' => $commonLabelAttr,),
                'second_options' => array('label' => 'Re-type Password:', 'label_attr' => $commonLabelAttr,),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('buttonPassword','submit',array(
                'label' => 'Change Password',
                'attr' => ['class' => 'btn btn-small btn-success']
            ))
            ->add('avatar', 'text', array(
                'label' => 'Avatar:',
                'label_attr' => $commonLabelAttr,
                'required' => false
            ))
            ->add('avatarFile', 'file', array(
                'label' => 'Avatar File:',
                'label_attr' => $commonLabelAttr,
                'mapped' => false,
                'required' => false
            ))
            ->add('buttonAvatar','submit',array(
                'label' => 'Change Avatar',
                'attr' => ['class' => 'btn btn-small btn-success']
            ))
            ->add('lang', 'choice', array(
                'label' => 'Language:',
                'label_attr' => $commonLabelAttr,
                "choices" => ['PT' => 'Portuguese-Brazilian', 'EN' => 'English']
            ))
            ->add('country', 'choice', array(
                'label' => 'Country:',
                'label_attr' => $commonLabelAttr,
                "choices" => $country_arr
            ))
            ->add('buttonRegion','submit',array(
                'label' => 'Change Regional Preferences',
                'attr' => ['class' => 'btn btn-small btn-success']
            ))
            ->add('email', 'email', array(
                "label" => 'Email:',
                'label_attr' => $commonLabelAttr,
            ))
            ->add('buttonChangeEmail','submit',array(
                'label' => 'Change Email',
                'attr' => ['class' => 'btn btn-small btn-success']
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LoopAnime\UsersBundle\Entity\Users',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'loopanime_user_usercpForm';
    }

}