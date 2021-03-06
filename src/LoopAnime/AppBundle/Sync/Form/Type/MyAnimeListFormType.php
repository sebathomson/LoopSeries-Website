<?php

namespace LoopAnime\AppBundle\Sync\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MyAnimeListFormType extends AbstractType
{

    public function __construct(ObjectManager $entityManager, TokenStorageInterface $user)
    {
        $this->em = $entityManager;
        $this->user = $user->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $commonLabelAttr = ['class' => 'font-bold'];

        $builder
            ->add('username', "text",
                array(
                    'label' => 'Username:',
                    'label_attr' => $commonLabelAttr,
                    'attr' => ['class' => 'form-control input-small']
                ))
            ->add('password', "password",
                array(
                    'label' => 'Password:',
                    'label_attr' => $commonLabelAttr,
                    'attr' => ['class' => 'form-control input-small']
                ))
            ->add('buttonSync', 'submit', array(
                'label' => 'Sync MAL',
                'attr' => ['class' => 'btn btn-small btn-success']
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }

    public function getName()
    {
        return 'loopanime_sync_form_myanimelist';
    }

}
