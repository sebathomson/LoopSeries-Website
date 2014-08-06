<?php

namespace LoopAnime\AdminBundle\Controller;


use LoopAnime\AdminBundle\Form\Type\AddNewAnimeType;
use LoopAnime\AdminBundle\Form\Type\CrawlEpisodesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{

    public function addAnimeAction()
    {
        $form = $this->createForm(new AddNewAnimeType($this->getDoctrine()->getManager()))->createView();
        return $this->render('LoopAnimeAdminBundle:admin:addAnime.html.twig',['form' => $form]);
    }

    public function populateLinksAction(Request $request)
    {
        $form = $this->createForm(new CrawlEpisodesType($this->getDoctrine()->getManager()));
        $form->bind($request);
        if($form->isValid()) {
            $hoster = $form->get("hoster");
            $anime = $form->get("anime");
            $all = $form->get("all");
            // TODO
        }
        $form = $form->createView();
        return $this->render('LoopAnimeAdminBundle:admin:crawl4Episodes.html.twig',['form' => $form]);
    }



}