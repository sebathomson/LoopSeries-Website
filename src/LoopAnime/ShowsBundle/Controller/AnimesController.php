<?php

namespace LoopAnime\ShowsBundle\Controller;

use LoopAnime\ShowsBundle\Entity\Animes;
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
        $animesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');

        /** @var Animes[] $animes */
        $animes = $animesRepo->findAll();

        if(empty($animes)) {
            throw $this->createNotFoundException("The anime does not exists or was removed.");
        }

        foreach($animes as $animeInfo) {
            $anime = [];
            $anime["id"] = $animeInfo->getId();
            $anime["poster"] = $animeInfo->getPoster();
            $anime["genres"] = $animeInfo->getGenres();
            $anime["startTime"] = $animeInfo->getStartTime();
            $anime["endTime"] = $animeInfo->getEndTime();
            $anime["title"] = $animeInfo->getTitle();
            $anime["plotSummary"] = $animeInfo->getPlotSummary();
            $anime["rating"] = $animeInfo->getRating();
            $anime["status"] = $animeInfo->getStatus();
            $anime["runningTime"] = $animeInfo->getRunningTime();

            if(($animeInfo->getRatingUp() + $animeInfo->getRatingDown()) > 0) {
                $anime["ratingPercent"] = round(($animeInfo->getRatingUp() * 100) / ($animeInfo->getRatingUp() + $animeInfo->getRatingDown()));
            } else {
                $anime["ratingPercent"] = 0;
            }

            $anime["ratingUp"] = $animeInfo->getRatingUp();
            $anime["ratingDown"] = $animeInfo->getRatingDown();

            $data["payload"]["animes"][] = $anime;
        }

        if($request->getRequestFormat() === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:listAnimes.html.twig", array("animes" => $data["payload"]["animes"]));
            return $render;
        } elseif($request->getRequestFormat() === "json") {
            return new JsonResponse($data);
        }

    }

    public function getAnimeAction($idAnime, Request $request)
    {

        $animesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');

        /** @var Animes $animes */
        $animes = $animesRepo->find($idAnime);

        if(empty($animes)) {
            throw $this->createNotFoundException("The anime does not exists or was removed.");
        }

        $anime = [];
        $anime["id"] = $animes->getId();
        $anime["poster"] = $animes->getPoster();
        $anime["genres"] = $animes->getGenres();
        $anime["startTime"] = $animes->getStartTime();
        $anime["endTime"] = $animes->getEndTime();
        $anime["title"] = $animes->getTitle();
        $anime["plotSummary"] = $animes->getPlotSummary();
        $anime["rating"] = $animes->getRating();
        $anime["status"] = $animes->getStatus();
        $anime["runningTime"] = $animes->getRunningTime();
        $anime["ratingUp"] = $animes->getRatingUp();
        $anime["ratingDown"] = $animes->getRatingDown();

        $data["payload"]["animes"][] = $anime;

        if($request->getRequestFormat() === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:animeInfo.html.twig", array("animes" => $data["payload"]["animes"]));
            return $render;
        } elseif($request->getRequestFormat() === "json") {
            return new JsonResponse($data);
        }

    }

}
