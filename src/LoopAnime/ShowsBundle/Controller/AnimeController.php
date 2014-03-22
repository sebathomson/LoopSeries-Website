<?php

namespace LoopAnime\ShowsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoopAnimeShowsBundle:Default:index.html.twig', array('name' => $name));
    }

    public function ListAnimeAction()
    {

    }
}
