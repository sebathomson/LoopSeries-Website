<?php

namespace LoopAnime\AdminBundle\Admin\Crawler;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

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
        $form
            ->add('season')
            ->add('episodeTitle')
            ->add('animeTitle')
            ->add('reset', 'checkbox')
            ->add('handicap')
        ;
    }



}
