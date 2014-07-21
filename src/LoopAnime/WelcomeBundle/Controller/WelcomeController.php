<?php
namespace LoopAnime\WelcomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller {

    public function indexAction() {
        return $this->render("LoopAnimeWelcomeBundle:welcome:index.html.twig");
    }

}