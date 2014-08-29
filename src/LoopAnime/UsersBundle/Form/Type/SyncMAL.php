<?php

namespace LoopAnime\UsersBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SyncMAL extends AbstractType
{

    public function __construct(ObjectManager $entityManager, Users $user)
    {
        $this->em = $entityManager;
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $commonLabelAttr = ['class' => 'font-bold'];

        $builder
            ->add('username', "text",
                array(
                    'label' => 'Username:',
                    'label_attr' => $commonLabelAttr,
                ))
            ->add('password', "password",
                array(
                    'label' => 'Password:',
                    'label_attr' => $commonLabelAttr,
                ))
            ->add('buttonSync','submit',array(
                'label' => 'Sync MAL',
                'attr' => ['class' => 'btn btn-small btn-success']
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }

    public function getName()
    {
        return 'SyncMAL';
    }

}