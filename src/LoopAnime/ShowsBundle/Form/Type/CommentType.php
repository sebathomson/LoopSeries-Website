<?php

namespace LoopAnime\ShowsBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $commonLabelAttr = ['class' => 'font-bold'];

        $builder
            ->add('comment', 'textarea', array(
                "label" => 'Comment:',
                'label_attr' => $commonLabelAttr,
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LoopAnime\CommentsBundle\Entity\Comments',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'loopanime_shows_comment';
    }

}
