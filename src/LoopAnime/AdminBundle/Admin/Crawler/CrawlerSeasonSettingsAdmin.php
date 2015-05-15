<?php

namespace LoopAnime\AdminBundle\Admin\Crawler;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints as Assert;

class CrawlerSeasonSettingsAdmin extends Admin
{

    protected $baseRouteName = 'crawler_season_settings_admin';
    protected $baseRoutePattern = 'crawler_season_settings_admin';

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('season')
            ->add('episodeTitle')
            ->add('animeTitle')
            ->add('reset')
            ->add('handicap')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('season')
            ->add('episodeTitle')
            ->add('animeTitle')
            ->add('reset')
            ->add('handicap')
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        if (!$this->hasParentFieldDescription()) {
            $form->add('crawler', null, array('constraints' => new Assert\NotNull()));
        }

        $form
            ->add('season', null, ['required' => false])
            ->add('episodeTitle', null, ['required' => false])
            ->add('animeTitle', null, ['required' => false])
            ->add('reset', null, ['required' => false])
            ->add('handicap', null, ['required' => false])
        ;
    }



}
