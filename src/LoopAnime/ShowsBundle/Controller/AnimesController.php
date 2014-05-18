<?php

namespace LoopAnime\ShowsBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AnimesController extends Controller
{
    public function indexAction()
    {
        return $this->render('LoopAnimeShowsBundle:Default:index.html.twig');
    }

    /**
     * @Route("/animes/get-recent-episodes.{_format}", requirements={"_format" = "html|json"}, defaults={"_format" = "html"})
     */
    public function getRecentEpisodesAction($_format, Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $maxResults = 20;
        if($request->get("maxr")) {
            $maxResults = $request->get("maxr");
        }

        $skip = 0;
        if($request->get("skip")) {
            $skip = $request->get("skip");
        }

        $dql = 'SELECT ae FROM LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae WHERE ae.airDate <= CURRENT_TIMESTAMP() ORDER BY ae.airDate DESC';
        $query = $entityManager->createQuery($dql)
            ->setFirstResult($skip)
            ->setMaxResults($maxResults);

        /** @var AnimesEpisodes[] $recentEpisodes */
        $recentEpisodes = new Paginator($query, $fetchJoinCollection = true);

        if(!$recentEpisodes) {
            return new JsonResponse(array("failure"=>true,"msg"=>"There isn't any recent episodes today!"));
        }

        $data =[];

        foreach ($recentEpisodes as $episode) {
            $data["payload"]["animes"]["episodes"][] = array(
                "id" => $episode->getId(),
                "url" => $episode->getEpisode(),
                "poster" => $episode->getPoster(),
                "title" => $episode->getEpisodeTitle(),
                "views" => $episode->getViews(),
                "rating" => $episode->getRating(),
            );

        }

        if($_format === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:videoGallery.html.twig", array("recentsEpisodes" => $data["payload"]["animes"]["episodes"]));
            return $render;
        } elseif($_format === "json") {
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/animes/get-most-view-episodes.{_format}", requirements={"_format" = "html|json"}, defaults={"_format" = "html"})
     */
    public function getMostViewEpisodesAction($_format, Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $maxResults = 20;
        if($request->get("maxr")) {
            $maxResults = $request->get("maxr");
        }

        $skip = 0;
        if($request->get("skip")) {
            $skip = $request->get("skip");
        }

        $dql = 'SELECT ae FROM LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae WHERE ae.airDate <= CURRENT_TIMESTAMP() ORDER BY ae.views DESC';
        $query = $entityManager->createQuery($dql)
            ->setFirstResult($skip)
            ->setMaxResults($maxResults);

        /** @var AnimesEpisodes[] $recentEpisodes */
        $recentEpisodes = new Paginator($query, $fetchJoinCollection = true);

        if(!$recentEpisodes) {
            return new JsonResponse(array("failure"=>true,"msg"=>"There isn't any recent episodes today!"));
        }

        $data =[];

        foreach ($recentEpisodes as $episode) {
            $data["payload"]["animes"]["episodes"][] = array(
                "id" => $episode->getId(),
                "url" => $episode->getEpisode(),
                "poster" => $episode->getPoster(),
                "title" => $episode->getEpisodeTitle(),
                "views" => $episode->getViews(),
                "rating" => $episode->getRating(),
            );

        }

        if($_format === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:videoGallery.html.twig", array("recentsEpisodes" => $data["payload"]["animes"]["episodes"]));
            return $render;
        } elseif($_format === "json") {
            return new JsonResponse($data);
        }
    }
}
