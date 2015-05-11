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
            ->add('id')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id')
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('id')
        ;
    }



}
