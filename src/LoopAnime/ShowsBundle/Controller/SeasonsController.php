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

        $paginator  = $this->get('knp_paginator');
        $seasons = $paginator->paginate(
            $seasons,
            $request->query->get('page', 1),
            $request->query->get('maxr', 10)
        );

        if($request->getRequestFormat() === "json") {
            $data = [];
            foreach($seasons as $seasonInfo) {
                $data["payload"]["seasons"][] = $seasonInfo->convert2Array();
            }
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeShowsBundle:Animes:seasonsList.html.twig", array("seasons" => $seasons));
    }

    public function getSeasonAction($idSeason, Request $request)
    {

        /** @var AnimesSeasonsRepository $seasonsRepo */
        $seasonsRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');
        /** @var Animes $animes */
        $seasons = $seasonsRepo->getSeasonById($idSeason, false);

        /** @var $paginator $seasons */
        $paginator  = $this->get('knp_paginator');
        /** @var AnimesSeasons[] $seasons */
        $seasons = $paginator->paginate(
            $seasons,
            $request->query->get('page', 1),
            $request->query->get('maxr', 10)
        );

        if($request->getRequestFormat() === "html") {
            /** @var AnimesSeasons $season */
            $season = $seasons->getItems()[0];
            /** @var Animes $anime */
            $anime = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes')->find($season->getIdAnime());
        } elseif($request->getRequestFormat() === "json") {
            foreach($seasons as $season) {
                $data["payload"]["seasons"][] = $season->convert2Array();
            }

            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeShowsBundle:Animes:baseAnimes.html.twig", array("seasons" => $season, "anime" => $anime, "seasonNumber" => $season->getSeason()));
    }

}
