<?php

namespace LoopAnime\ShowsAPIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoopAnimeShowsAPIBundle:Default:index.html.twig', array('name' => $name));
    }
}
