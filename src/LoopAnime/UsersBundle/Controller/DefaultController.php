<?php

namespace LoopAnime\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoopAnimeUsersBundle:Default:index.html.twig', array('name' => $name));
    }
}
