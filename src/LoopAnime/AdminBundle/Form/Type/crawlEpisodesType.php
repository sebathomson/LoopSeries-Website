<?php

namespace LoopAnime\AdminBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\ShowsAPIBundle\Entity\APIS;
use LoopAnime\ShowsBundle\Entity\Animes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class CrawlEpisodesType extends AbstractType
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

        /** @var Animes[] $animesObj */
        $animesRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
        $animesObj = $animesRepo->findAll();

        $animes = [];
        foreach($animesObj as $key=>$anime) {
            $animes[$anime->getId()] = $anime->getTitle();
        }

        $builder
            ->add('hoster', 'choice', array(
                'label' => 'Hoster:',
                'label_attr' => $commonLabelAttr,
                'required' => true,
                "choices" => ["anime44" => "anime44","anitube" => "anitube"]
            ))
            ->add('anime', 'choice', array(
                'label' => 'Anime:',
                'label_attr' => $commonLabelAttr,
                'required' => true,
                "choices" => $animes
            ))
            ->add('all', 'checkbox', array(
                'label' => 'All Episodes:',
                'label_attr' => $commonLabelAttr,
                'required' => false
            ))
            ->add('button','submit',array(
                'label' => 'Go for it!',
                'attr' => ['class' => 'btn btn-small btn-success']
            ));
    }

    public function getName()
    {
        return 'PopulateLinks';
    }
}