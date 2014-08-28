<?php

namespace LoopAnime\AdminBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\ShowsAPIBundle\Entity\APIS;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class AddNewAnimeType extends AbstractType
{
    private $em;

    /**
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $commonLabelAttr = ['class' => 'font-bold'];

        /** @var APIS[] $apis */
        $apisRepo = $this->em->getRepository('LoopAnime\ShowsAPIBundle\Entity\APIS');
        $apis = $apisRepo->findAll();

        foreach($apis as $key=>$api) {
            $apis[$key] = $api->getApi();
        }

        $builder
            ->add('tvdb_id', 'text', array(
                'label' => 'TVDB ID:',
                'label_attr' => $commonLabelAttr,
            ))
            ->add('buttonAvatar','submit',array(
                'label' => 'Go for it!',
                'attr' => ['class' => 'btn btn-small btn-success']
            ));
    }

    public function getName()
    {
        return 'AddNewAnime';
    }
}