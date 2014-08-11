<?php

namespace LoopAnime\ShowsBundle\Controller;

use Knp\Component\Pager\Paginator;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesSeasonsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AnimesController extends Controller
{
    public function indexAction()
    {
        return $this->render('LoopAnimeShowsBundle:index:index.html.twig');
    }

    public function listAnimesAction(Request $request)
    {
        /** @var AnimesRepository $animesRepo */
        $animesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');

        $query = "";

        $type = "ordered";
        if($request->get("type")) {
            switch($request->get("type")) {
                case "mostrated":
                    $type = "mostrated";
                    $query = $animesRepo->getAnimesMostRated();
                    break;
                case "recents":
                    $type = "recents";
                    $query = $animesRepo->getAnimesRecent();
                    break;
            }
        }

        if($type === "ordered" && $request->get("title")) {
            $query = $animesRepo->getAnimesByTitle($request->get("title"));
        } elseif($type === "ordered") {
            $query = $animesRepo->getAnimesByTitle("");
        }

        /** @var Animes[] $animes */
        $paginator  = $this->get('knp_paginator');
        $animes = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $request->query->get('maxr', 10)
        );

        if($request->getRequestFormat() === "json") {
            $data = [];
            foreach($animes as $animeInfo) {
                /** @var AnimesSeasonsRepository $seasonRepo */
                $seasonRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
                $extra = ['total_seasons' => $seasonRepo->getTotSeasons($animeInfo)];
                $data["payload"]["animes"][] = array_merge($extra,$animeInfo->convert2Array());
            }
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeShowsBundle:Animes:listAnimes.html.twig", array("pagination" => $animes, "type" => $type));

    }

    public function getAnimeAction($idAnime, Request $request)
    {

        $animesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');

        /** @var Animes $anime */
        $anime = $animesRepo->find($idAnime);

        if($request->getRequestFormat() === "json") {
            /** @var AnimesSeasonsRepository $seasonRepo */
            $seasonRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');
            $extra = ['total_seasons' => $seasonRepo->getTotSeasons($anime)];
            $data["payload"]["animes"][] = array_merge($extra,$anime->convert2Array());
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeShowsBundle:Animes:baseAnimes.html.twig", array("anime" => $anime));

    }



}
