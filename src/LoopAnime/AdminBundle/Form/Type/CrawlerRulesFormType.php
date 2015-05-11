<?php
namespace LoopAnime\AdminBundle\Form\Type;


use LoopAnime\CrawlersBundle\Enum\HostersEnum;
use Symfony\Component\Form\FormBuilderInterface;

class CrawlerRulesFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('idAnime', null,
                array(
                    'label' => 'Id Anime:',
                    'attr' => ['class' => 'form-control input-small', 'placeholder' => 'Username']
                ))
            ->add('hoster', 'choice', array(
                'choices' => HostersEnum::getAsArray()
            ))
            ->add('newsletter', 'checkbox', array(
                'required' => false,
                'label' => 'Newsletter:',
                'attr' => [],
                'label_attr' => $commonLabelAttr,
            ))
            ->add('buttonSubmit','submit',array(
                'label' => 'Submit Changes',
                'attr' => ['class' => 'btn btn-small btn-success']
            ))
            ->add('avatarFile', 'file', array(
                'label' => 'Avatar File:',
                'label_attr' => $commonLabelAttr,
                'attr' => ['class' => 'form-control input-small', 'placeholder' => 'Avatar'],
                'mapped' => false,
                'required' => false
            ))
            ->add('lang', 'choice', array(
                'label' => 'Language:',
                'label_attr' => $commonLabelAttr,
                'attr' => ['class' => 'form-control input-small', 'placeholder' => 'Language'],
                "choices" => ['PT' => 'Portuguese-Brazilian', 'EN' => 'English', 'ES' => 'Spanish', 'FR' => 'French']
            ))
            ->add('country', 'choice', array(
                'label' => 'Country:',
                'label_attr' => $commonLabelAttr,
                'attr' => ['class' => 'form-control input-small', 'placeholder' => 'Country'],
                "choices" => $country_arr
            ))
            ->add('email', 'email', array(
                "label" => 'Email:',
                'attr' => ['class' => 'form-control input-small', 'placeholder' => 'E-Mail'],
                'label_attr' => $commonLabelAttr,
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LoopAnime\CrawlersBundle\Entity\Crawler',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'loopanime_user_usercp';
    }



}
