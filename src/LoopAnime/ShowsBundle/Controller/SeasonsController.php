<?php

namespace LoopAnime\ShowsBundle\Controller;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use LoopAnime\ShowsBundle\Entity\AnimesSeasonsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SeasonsController extends Controller
{

    public function listSeasonsAction(Request $request)
    {
        /** @var AnimesSeasonsRepository $seasonsRepo */
        $seasonsRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');

        $seasons = $seasonsRepo->getSeasonsByAnime($request->get("anime"), false);

        if($request->getRequestFormat() === "html") {

            $paginator  = $this->get('knp_paginator');
            $seasons = $paginator->paginate(
                $seasons,
                $request->query->get('page', 1),
                10
            );

            if(empty($seasons)) {
                throw $this->createNotFoundException("The Season does not exists or was removed.");
            }

            return $this->render("LoopAnimeShowsBundle:Animes:seasonsList.html.twig", array("seasons" => $seasons));
        } elseif($request->getRequestFormat() === "json") {

            /** @var AnimesSeasons[] $seasons */
            $seasons = $seasons->getResult();

            if(empty($seasons)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            $data = [];
            foreach($seasons as $seasonInfo) {
                $data["payload"]["animes"][] = $this->convert2Array($seasonInfo);
            }

            return new JsonResponse($data);
        }

    }

    public function getSeasonAction($idSeason, Request $request)
    {

        /** @var AnimesSeasonsRepository $seasonsRepo */
        $seasonsRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');

        /** @var Animes $animes */
        $seasons = $seasonsRepo->getSeasonById($idSeason, false);

        if($request->getRequestFormat() === "html") {

            /** @var $paginator $seasons */
            $paginator  = $this->get('knp_paginator');
            /** @var SlidingPagination $seasons */
            $seasons = $paginator->paginate(
                $seasons,
                $request->query->get('page', 1),
                10
            );

            if(!$seasons->valid()) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            /** @var AnimesSeasons $season */
            $season = $seasons->getItems()[0];

            /** @var Animes $anime */
            $anime = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes')->find($season->getIdAnime());

            $render = $this->render("LoopAnimeShowsBundle:Animes:baseAnimes.html.twig", array("seasons" => $season, "anime" => $anime, "seasonNumber" => $season->getSeason()));
            return $render;
        } elseif($request->getRequestFormat() === "json") {
            $season = $seasons->getResult();

            if(empty($seasons)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            $data["payload"]["animes"][] = $this->convert2Array($season);
            return new JsonResponse($data);
        }

    }

    public function convert2Array(AnimesSeasons $seasonInfo) {
        return array(
            "id" => $seasonInfo->getId(),
            "createTime" => $seasonInfo->getCreateTime(),
            "numberEpisodes" => $seasonInfo->getNumberEpisodes(),
            "lastUpdate" => $seasonInfo->getLastUpdate(),
            "season" => $seasonInfo->getSeason(),
            "poster" => $seasonInfo->getPoster(),
            "seasonTitle" => $seasonInfo->getSeasonTitle()
        );
    }

}
