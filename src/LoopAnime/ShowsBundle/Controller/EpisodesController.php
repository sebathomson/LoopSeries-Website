<?php

namespace LoopAnime\ShowsBundle\Controller;

use Knp\Component\Pager\Paginator;
use LoopAnime\Helpers\Crawlers\Crawler;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersPreferences;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EpisodesController extends Controller
{

    public function listEpisodesAction(Request $request)
    {
        /** @var AnimesEpisodesRepository $episodesRepo */
        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');

        if(!$request->get("anime") && !$request->get("season")) {
            throw $this->createNotFoundException("Please look for an sepecific anime id or season id to retrieve episodes.");
        }

        /** @var AnimesEpisodes[] $episodes */
        $episodes = null;
        if($request->get("anime")) {
            $episodes = $episodesRepo->getEpisodesByAnime($request->get("anime"), false);
        } elseif($request->get("season")) {
            $episodes = $episodesRepo->getEpisodesBySeason($request->get("season"), false);
        }

        if($request->getRequestFormat() === "html") {

            /** @var Paginator $paginator */
            $paginator  = $this->get('knp_paginator');
            $episodes = $paginator->paginate(
                $episodes,
                $request->query->get('page', 1),
                10
            );

            if(empty($episodes)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            return $this->render("LoopAnimeShowsBundle:Animes:episodesList.html.twig", array("episodes" => $episodes));
        } elseif($request->getRequestFormat() === "json") {

            $episodes = $episodes->getResult();

            if(empty($episodes)) {
                throw $this->createNotFoundException("There isnt any episode for the anime nor season selected");
            }

            foreach($episodes as $episodeInfo) {
                $data["payload"]["episodes"][] = $episodeInfo;
            }

            return new JsonResponse($data);
        }

        return false;
    }


    public function getEpisodeAction($idEpisode, Request $request)
    {

        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');

        /** @var AnimesEpisodes $episodes */
        $episodes = $episodesRepo->find($idEpisode);

        if(empty($episodes)) {
            throw $this->createNotFoundException("The episode does not exists or was removed.");
        }

        $episodeInfo = &$episodes;
        $episode = [];
        $episode["id"] = $episodeInfo->getId();
        $episode["poster"] = $episodeInfo->getPoster();
        $episode["airDate"] = $episodeInfo->getAirDate();
        $episode["absoluteNumber"] = $episodeInfo->getAbsoluteNumber();
        $episode["views"] = $episodeInfo->getViews();
        $episode["title"] = $episodeInfo->getEpisodeTitle();
        $episode["episodeNumber"] = $episodeInfo->getEpisode();
        $episode["rating"] = $episodeInfo->getRating();
        $episode["summary"] = $episodeInfo->getSummary();
        $episode["ratingUp"] = $episodeInfo->getRatingUp();
        $episode["ratingDown"] = $episodeInfo->getRatingDown();

        $data["payload"]["episodes"][] = $episode;

        if($request->getRequestFormat() === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:animeInfo.html.twig", array("animes" => $data["payload"]["animes"]));
            return $render;
        } elseif($request->getRequestFormat() === "json") {
            return new JsonResponse($data);
        }

    }


    public function getEpisodesAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        /** @var Users $user */
        $user = $this->getUser();
        if($user) {
            $userPreferences = new UsersPreferences();
            $userPreferences->setIdUser($user);
        }

        $maxResults = 20;
        if($request->get("maxr")) {
            $maxResults = $request->get("maxr");
        }

        $skip = 0;
        if($request->get("skip")) {
            $skip = $request->get("skip");
        }

        $typeEpisode = "recent";
        if($request->get("typeEpisode")) {
            $typeEpisode = $request->get("typeEpisode");
        }

        $where = "ae.airDate <= CURRENT_TIMESTAMP()";

        switch($typeEpisode) {
            case "recent":
                $orderBy = "ae.airDate DESC";
                $dql = 'SELECT ae FROM LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae WHERE '.$where.' ORDER BY ' . $orderBy;
                break;
            case "mostview":
                $orderBy = "ae.views DESC";
                $dql = 'SELECT ae FROM LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae WHERE '.$where.' ORDER BY ' . $orderBy;
                break;
            case "mostrated":
                $orderBy = "ae.rating DESC, ae.ratingCount DESC, ae.ratingUp DESC";
                $dql = 'SELECT ae FROM LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae WHERE '.$where.' ORDER BY ' . $orderBy;
                break;
            case "userRecent":
                if(!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }

                $order = "DESC";
                if($userPreferences->getTrackEpisodesSort())
                    $order = $userPreferences->getTrackEpisodesSort();

                $orderBy = "animes_seasons.season $order, animes_episodes.episode $order";
                $dql = '
                SELECT animesEpisodes FROM
                    LoopAnime\UsersBundle\Entity\UsersFavorites uf
						JOIN uf.anime animes
						JOIN animes.animesSeasons animesSeasons
                        JOIN animesSeasons.AnimesEpisodes animesEpisodes
                    WHERE '.$where.' ORDER BY ' . $orderBy;
                break;
            case "userFuture":
                if(!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }

                // User Preferences
                if($userPreferences->getFutureListSpecials())
                    $where .= " AND animesSeasons.season > 0";

                $orderBy = "ae.airDate ASC";
                $dql = '
                SELECT animesEpisodes FROM
                    LoopAnime\UsersBundle\Entity\UsersFavorites uf
						JOIN uf.idAnime animes
						JOIN animes.AnimesSeasons animesSeasons
                        JOIN animesSeasons.AnimesEpisodes animesEpisodes
                    WHERE '.$where.' ORDER BY ' . $orderBy;
                break;
                break;
            case "userHistory":
                if(!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }
                break;
        }


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

        if($request->getRequestFormat() === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:videoGallery.html.twig", array("recentsEpisodes" => $data["payload"]["animes"]["episodes"]));
            return $render;
        } elseif($request->getRequestFormat() === "json") {
            return new JsonResponse($data);
        }
    }

    public function getLinksAction(Request $request)
    {
        $linksRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesLinks');

        /** @var AnimesLinks[] $links */
        $links = null;
        if($request->get("episode")) {
            $links = $linksRepo->findBy(array("idEpisode" => $request->get("episode")));
        } else {
            throw new \Exception("Please provide a get value with the id of the Episode");
        }

        if(empty($links)) {
            throw $this->createNotFoundException("There arent any link for that episode.");
        }

        foreach($links as $linkInfo) {
            $link = [];
            $link["id"] = $linkInfo->getId();
            $link["lang"] = $linkInfo->getLang();
            $link["createTime"] = $linkInfo->getCreateTime();
            $link["fileServer"] = $linkInfo->getFileServer();
            $link["fileSize"] = $linkInfo->getFileSize();
            $link["hoster"] = $linkInfo->getHoster();
            $link["subtitles"] = $linkInfo->getSubtitles();
            $link["subtitlesLang"] = $linkInfo->getSubLang();
            $link["qualityType"] = $linkInfo->getQualityType();
            $link["fileType"] = $linkInfo->getFileType();
            $link["link"] = $linkInfo->getLink();
            $link["used"] = $linkInfo->getUsed();
            $link["usedTimes"] = $linkInfo->getUsedTimes();
            $link["status"] = $linkInfo->getStatus();

            $data["payload"]["episodes"][] = $link;
        }

        if($request->getRequestFormat() === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:animeInfo.html.twig", array("animes" => $data["payload"]["animes"]));
            return $render;
        } elseif($request->getRequestFormat() === "json") {
            return new JsonResponse($data);
        }

    }

    public function getDirectLinkAction($idLink, Request $request)
    {

        $linksRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesLinks');

        /** @var AnimesLinks $links */
        $links = $linksRepo->find($idLink);

        if(empty($links)) {
            throw $this->createNotFoundException("The link id does not exists");
        }

        $linkInfo = &$links;
        $link = [];
        $link["id"] = $linkInfo->getId();
        $link["lang"] = $linkInfo->getLang();
        $link["createTime"] = $linkInfo->getCreateTime();
        $link["fileServer"] = $linkInfo->getFileServer();
        $link["fileSize"] = $linkInfo->getFileSize();
        $link["hoster"] = $linkInfo->getHoster();
        $link["subtitles"] = $linkInfo->getSubtitles();
        $link["subtitlesLang"] = $linkInfo->getSubLang();
        $link["qualityType"] = $linkInfo->getQualityType();
        $link["fileType"] = $linkInfo->getFileType();
        $link["link"] = $linkInfo->getLink();
        $link["videoOptions"] = Crawler::crawlVideoLink(explode("-",$linkInfo->getHoster())[0],$linkInfo->getLink());
        $link["used"] = $linkInfo->getUsed();
        $link["usedTimes"] = $linkInfo->getUsedTimes();
        $link["status"] = $linkInfo->getStatus();

        $data["payload"]["episodes"][] = $link;

        if($request->getRequestFormat() === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:animeInfo.html.twig", array("animes" => $data["payload"]["animes"]));
            return $render;
        } elseif($request->getRequestFormat() === "json") {
            return new JsonResponse($data);
        }

    }

    public function convert2Array(AnimesEpisodes $episodeInfo) {
        return array(
            "id"        => $episodeInfo->getId(),
            "poster"    => $episodeInfo->getPoster(),
            "idSeason"  => $episodeInfo->getIdSeason(),
            "airDate"   => $episodeInfo->getAirDate(),
            "absoluteNumber" => $episodeInfo->getAbsoluteNumber(),
            "views"     => $episodeInfo->getViews(),
            "title"     => $episodeInfo->getEpisodeTitle(),
            "episodeNumber" => $episodeInfo->getEpisode(),
            "rating"    => $episodeInfo->getRating(),
            "summary"   => $episodeInfo->getSummary(),
            "ratingUp"  => $episodeInfo->getRatingUp(),
            "ratingDown" => $episodeInfo->getRatingDown()
        );
    }

}
