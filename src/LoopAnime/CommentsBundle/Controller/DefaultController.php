<?php

namespace LoopAnime\CommentsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoopAnimeCommentsBundle:Default:index.html.twig', array('name' => $name));
    }
}
