<?php

namespace LoopAnime\ShowsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AnimeController extends Controller
{
    public function indexAction()
    {
        return $this->render('LoopAnimeShowsBundle:Default:index.html.twig');
    }

    public function ListAnimeAction()
    {

    }
}
