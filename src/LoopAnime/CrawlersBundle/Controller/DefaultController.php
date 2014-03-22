<?php

namespace LoopAnime\CrawlersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoopAnimeCrawlersBundle:Default:index.html.twig', array('name' => $name));
    }
}
