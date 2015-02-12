<?php

namespace LoopAnime\ShowsBundle\Controller;

use Knp\Component\Pager\Paginator;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use LoopAnime\ShowsBundle\Entity\ViewsRepository;
use LoopAnime\ShowsBundle\Services\VideoService;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EpisodesController extends Controller
{

    public function listEpisodesAction(Request $request)
    {
        /** @var AnimesEpisodesRepository $episodesRepo */
        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');

        if (!$request->get("anime") && !$request->get("season")) {
            return new JsonResponse(['isError' => true, 'errorMsg' => 'Controller needs to have a valid anime and season']);
        }

        /** @var AnimesEpisodes[] $episodes */
        $episodes = null;
        if ($request->get("anime")) {
            $episodes = $episodesRepo->getEpisodesByAnime($request->get("anime"), false);
        } elseif ($request->get("season")) {
            $episodes = $episodesRepo->getEpisodesBySeason($request->get("season"), false);
        }

        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $episodes = $paginator->paginate(
            $episodes,
            $request->query->get('page', 1),
            $request->query->get('maxr', 10)
        );

        if ($request->getRequestFormat() === "json") {
            $data = [];
            foreach ($episodes as $episodeInfo) {
                $extraMerge = ['anime' => ['id' => $episodeInfo['id'], 'title' => $episodeInfo['title']],
                    'season' => ['id' => $episodeInfo[0]->getIdSeason(), 'season' => $episodeInfo['season']]];
                $data["payload"]["episodes"][] = array_merge($extraMerge,$episodeInfo[0]->convert2Array());
            }
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeShowsBundle:Animes:episodesList.html.twig", array("episodes" => $episodes));
    }

    public function getEpisodeAction(AnimesEpisodes $episode, Request $request)
    {
        $selLink = !empty($request->get("selLink")) ? $request->get("selLink") : 0;
        $videoService = new VideoService();
        $isIframe = false;
        $link = '';

        if ($episode === null) {
            return new JsonResponse(['isError' => true, 'errorMsg' => "Get parameter episode needs to be set and not empty."]);
        }

        $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes')->incrementView($episode);
        $anime = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:Animes')->getAnimeByEpisode($episode->getId(), false);
        $season = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesSeasons')->getSeasonById($episode->getSeason(), true);
        $links = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesLinks')->getLinksByEpisode($episode->getId());
        if(isset($links[$selLink])) {
            $link = $videoService->getDirectVideoLink($links[$selLink]);
            if(!$link) { $isIframe = true; $link = $videoService->getIframeLink($links[$selLink]); }
        }
        $renderData = [
            'episode' => $episode,
            'selLink' => $selLink,
            'season' => $season,
            'anime' => $anime,
            'links' => $links,
            'initialLink' => $link,
            'isIframe' => $isIframe,
            'isSeen' => $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:Views')->isEpisodeSeen($this->getUser(),$episode->getId()),
            'isFavorite' => $this->getDoctrine()->getRepository('LoopAnimeUsersBundle:UsersFavorites')->isAnimeFavorite($this->getUser(),$anime->getId()),
            'comments' => $this->getDoctrine()->getRepository('LoopAnimeCommentsBundle:Comments')->getCommentsByEpisode($episode, true),
            'totalFavorites' => $episode->getRatingUp()
        ];

        return $this->render("LoopAnimeShowsBundle:Animes:episode.html.twig", $renderData);
    }

    public function releaseDateAction(Request $request)
    {
        $date = new \DateTime($request->get('rd'));
        $prevDate = clone $date; $prevDate->modify('-1 day');
        $nextDate = clone $date; $nextDate->modify('+1 day');
        /** @var AnimesEpisodesRepository $animesEpisodes */
        $animesEpisodes = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');
        $episodes = $animesEpisodes->getEpisodesByDate($date);

        return $this->render('LoopAnimeShowsBundle:index:releaseSchedule.html.twig', [
            'prevDate' => $prevDate,
            'currDate' => $date,
            'nextDate' => $nextDate,
            'episodes' => $episodes
        ]);
    }

    public function navigateSeasonAction(Request $request)
    {
        $season = $request->get('season');
        $season = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesSeasons')->find($season);
        if(!$season) {
            throw new NotFoundHttpException();
        }
        $prevSeason = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesSeasons')->getPrevSeason($season);
        $nextSeason = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesSeasons')->getNextSeason($season);
        $episodes = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes')->getEpisodesBySeason($season,true);

        return $this->render('LoopAnimeShowsBundle:Animes:episodeSeasonsContainer.html.twig',['prevSeason' => $prevSeason, 'nextSeason' => $nextSeason, 'episodes' => $episodes, 'season' => $season]);
    }

    public function ajaxRequestAction(Request $request)
    {
        /** @var ViewsRepository $viewsRepo */
        $viewsRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:Views');
        /** @var UsersFavoritesRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnimeUsersBundle:UsersFavorites');
        $episodeService = $this->get('loopanime.episode.service');

        /** @var Users $user */
        if(!$user = $this->getUser()) {
            return new JsonResponse(['isError' => true, 'error' => 'You need to be logged in to perform this actions']);
        }

        $data = [];
        $msg = "";
        switch($request->get('op')) {
            case "mark_favorite":
                if($usersRepo->setAnimeAsFavorite($this->getUser(), $request->get("id_anime")))
                    $msg = "Anime was updated successfully";
                break;
            case "set_progress":
                if($viewsRepo->setViewProgress($user, $request->get("id_episode"), $request->get('id_link'), $request->get('watched_time')))
                    $msg = "Progress has been set";
                break;
            case "get_last_progress":
                if($data = $viewsRepo->getViewProgress($user, $request->get("id_episode")))
                    $msg = "Last Progress retrieved";
                else
                    $msg = 'There is no record of you seing this episode';
                break;
            case "mark_as_seen":
                if($episodeService->markEpisodeAsSeen($request->get("id_episode"), $request->get('id_link')))
                    $msg = "Episode marked as seen.";
                break;
            case "rating":
                if($episodeService->rateEpisode($request->get("id_episode"), $request->get('ratingUp')))
                    $msg = "Thank you for voting.";
                break;
        }

        return new JsonResponse(['isError' => empty($msg) ? true : false, 'msg' => empty($msg) ? 'Technical Error with your request - try again latter.' : $msg, 'data' => $data]);
    }

}
