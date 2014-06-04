<?php

namespace LoopAnime\ShowsBundle\Controller;

use Knp\Component\Pager\Paginator;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AnimesController extends Controller
{
    public function indexAction()
    {
        return $this->render('LoopAnimeShowsBundle:Default:index.html.twig');
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

        if($request->getRequestFormat() === "html") {

            /** @var Paginator $paginator */
            $paginator  = $this->get('knp_paginator');
            $animes = $paginator->paginate(
                $query,
                $request->query->get('page', 1),
                10
            );

            if(empty($animes)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            return $this->render("LoopAnimeShowsBundle:Animes:listAnimes.html.twig", array("pagination" => $animes, "type" => $type));
        } elseif($request->getRequestFormat() === "json") {

            /** @var Animes[] $animes */
            $animes = $query->getResult();

            if(empty($animes)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            foreach($animes as $animeInfo) {
                $data["payload"]["animes"][] = $this->convert2Array($animeInfo);
            }

            return new JsonResponse($data);
        }

    }

    public function getAnimeAction($idAnime, Request $request)
    {

        $animesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');

        /** @var Animes $anime */
        $anime = $animesRepo->find($idAnime);

        if(empty($anime)) {
            throw $this->createNotFoundException("The anime does not exists or was removed.");
        }

        if($request->getRequestFormat() === "html") {
            return $this->render("LoopAnimeShowsBundle:Animes:baseAnimes.html.twig", array("anime" => $anime));
        } elseif($request->getRequestFormat() === "json") {

            $data["payload"]["animes"][] = $this->convert2Array($anime);

            return new JsonResponse($data);
        }

    }

    /**
     *
     * Convert an Anime Doctrine object into an Array for Json
     *
     * @param Animes $anime
     * @return array
     */
    public function convert2Array(Animes $anime) {
        return array(
            "id"        => $anime->getId(),
            "poster"    =>  $anime->getPoster(),
            "genres"    =>  $anime->getGenres(),
            "startTime" =>  $anime->getStartTime(),
            "endTime"   =>  $anime->getEndTime(),
            "title"     =>  $anime->getTitle(),
            "plotSummary" =>  $anime->getPlotSummary(),
            "rating"    =>  $anime->getRating(),
            "status"    =>  $anime->getStatus(),
            "runningTime" =>  $anime->getRunningTime(),
            "ratingUp"  =>  $anime->getRatingUp(),
            "ratingDown" =>  $anime->getRatingDown()
        );
    }

}
