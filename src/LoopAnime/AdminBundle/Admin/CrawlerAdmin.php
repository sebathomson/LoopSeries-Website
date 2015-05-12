<?php

namespace LoopAnime\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CrawlerAdmin extends Admin
{

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('idAnime')
            ->add('hoster')
            ->add('seasonsSettings')
            ->add('episodeClean')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id')
            ->add('idAnime')
            ->add('hoster')
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('id')
            ->add('idAnime')
            ->add('hoster')
            ->add('seasonsSettings', 'sonata_type_immutable_array', [
                'keys' => [
                    ['season', 'text', []],
                    ['title', 'text', []],
                    ['episode', 'text', []],
                    ['reset','checkbox',[]],
                    ['handicap', 'integer', []]
                ]
            ])
            ->add('episodeClean')
        ;
    }



}
