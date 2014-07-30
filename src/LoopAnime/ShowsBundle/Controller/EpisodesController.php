<?php

namespace LoopAnime\ShowsBundle\Controller;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use LoopAnime\CommentsBundle\Controller\CommentsController;
use LoopAnime\Helpers\Crawlers\Crawler;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use LoopAnime\ShowsBundle\Entity\AnimesLinksRepository;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesSeasonsRepository;
use LoopAnime\ShowsBundle\Entity\ViewsRepository;
use LoopAnime\UsersBundle\Controller\UserActionsController;
use LoopAnime\UsersBundle\Controller\UserController;
use LoopAnime\UsersBundle\Controller\UsersController;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;
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

        if (!$request->get("anime") && !$request->get("season")) {
            throw $this->createNotFoundException("Please look for an sepecific anime id or season id to retrieve episodes.");
        }

        /** @var AnimesEpisodes[] $episodes */
        $episodes = null;
        if ($request->get("anime")) {
            $episodes = $episodesRepo->getEpisodesByAnime($request->get("anime"), false);
        } elseif ($request->get("season")) {
            $episodes = $episodesRepo->getEpisodesBySeason($request->get("season"), false);
        }

        if ($request->getRequestFormat() === "html") {

            /** @var Paginator $paginator */
            $paginator = $this->get('knp_paginator');
            $episodes = $paginator->paginate(
                $episodes,
                $request->query->get('page', 1),
                10
            );

            if (empty($episodes)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            return $this->render("LoopAnimeShowsBundle:Animes:episodesList.html.twig", array("episodes" => $episodes));
        } elseif ($request->getRequestFormat() === "json") {

            $episodes = $episodes->getResult();

            if (empty($episodes)) {
                throw $this->createNotFoundException("There isnt any episode for the anime nor season selected");
            }

            foreach ($episodes as $episodeInfo) {
                $data["payload"]["episodes"][] = $episodeInfo;
            }

            return new JsonResponse($data);
        }

        return false;
    }

    public function getEpisodeAction($idEpisode, Request $request)
    {
        $selLink = 0;
        if ($request->get("selLink")) {
            $selLink = $request->get("selLink");
        }

        /** @var AnimesEpisodesRepository $episodesRepo */
        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');

        /** @var AnimesEpisodes $episode */
        $episode = $episodesRepo->find($idEpisode);

        if (empty($episode)) {
            throw $this->createNotFoundException("The episode does not exists or was removed.");
        }

        if ($request->getRequestFormat() === "html") {

            $renderData = [];
            $renderData['episode'] = $episode;
            $renderData['selLink'] = $selLink;

            /** @var AnimesRepository $animesRepo */
            $animesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:Animes');
            $renderData['anime'] = $animesRepo->getAnimeByEpisode($episode->getId(), false);

            /** @var AnimesSeasonsRepository $seasonsRepo */
            $seasonsRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesSeasons');
            $renderData['season'] = $seasonsRepo->getSeasonById($episode->getIdSeason(), true);

            /** @var AnimesLinksRepository $linksRepo */
            $linksRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesLinks');
            $renderData['links'] = $linksRepo->getLinksByEpisode($episode->getId());

            $renderData['isIframe'] = false;

            /** @var ViewsRepository $viewsRepo */
            $viewsRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:Views');
            $renderData['isSeen'] = $viewsRepo->isEpisodeSeen($this->getUser(),$idEpisode);

            /** @var UsersFavoritesRepository $usersFavoritesRepo */
            $usersFavoritesRepo = $this->getDoctrine()->getRepository('LoopAnimeUsersBundle:UsersFavorites');
            $renderData['isFavorite'] = $usersFavoritesRepo->isAnimeFavorite($this->getUser(),$renderData['anime']->getId());

            // Next Episode
            if ($nextEpisode = $episodesRepo->getNavigateEpisode($episode->getId())) {
                $renderData['nextEpisode'] = $nextEpisode;
            }
            // Prev Episode
            if ($prevEpisode = $episodesRepo->getNavigateEpisode($episode->getId(), false)) {
                $renderData['prevEpisode'] = $prevEpisode;
            }

            $render = $this->render(
                "LoopAnimeShowsBundle:Animes:episode.html.twig",
                $renderData);
            return $render;
        } elseif ($request->getRequestFormat() === "json") {

            $data["payload"]["episodes"][] = $episode->convert2Array();

            return new JsonResponse($data);
        }

    }

    public function getEpisodesAction(Request $request)
    {
        /** @var Users $user */
        $user = $this->getUser();
        if ($user) {
            $userPreferences = new UsersPreferences();
            $userPreferences->setIdUser($user);
        }

        $maxResults = 20;
        if ($request->get("maxr")) {
            $maxResults = $request->get("maxr");
        }

        $typeEpisode = "recent";
        if ($request->get("typeEpisode")) {
            $typeEpisode = $request->get("typeEpisode");
        }

        /** @var AnimesEpisodesRepository $animesEpisodes */
        $animesEpisodes = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');

        switch ($typeEpisode) {
            case "recent":
                $dql = $animesEpisodes->getRecentEpisodes(false);
                break;
            case "mostview":
                $dql = $animesEpisodes->getMostViewsEpisodes(false);
                break;
            case "mostrated":
                $dql = $animesEpisodes->getMostRatedEpisodes(false);
                break;
            case "userRecent":
                if (!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }
                /** @var AnimesEpisodesRepository $animesEpisodesRepo */
                $animesEpisodesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');
                $dql = $animesEpisodesRepo->getUserRecentsEpisodes($this->getUser(),false);
                break;
            case "userFuture":
                if (!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }
                /** @var AnimesEpisodesRepository $animesEpisodesRepo */
                $animesEpisodesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');
                $dql = $animesEpisodesRepo->getUserFutureEpisodes($this->getUser(),false);
                break;
            case "userHistory":
                if (!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }
                /** @var AnimesEpisodesRepository $animesEpisodesRepo */
                $animesEpisodesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');
                $dql = $animesEpisodesRepo->getUserHistoryEpisodes($this->getUser(),false);
                break;
        }

        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        /** @var SlidingPagination $episodes */
        $episodes = $paginator->paginate(
            $dql,
            $request->query->get('page', 1),
            $maxResults
        );

        if (!$episodes->valid()) {
            return new JsonResponse(['error' => true, 'error_msg' => "No episodes found!"]);
        }

        $data = [];

        if ($request->getRequestFormat() === "html") {
            $render = $this->render("LoopAnimeShowsBundle:extra:videoGallery.html.twig", array("episodes" => $episodes));
            return $render;
        } elseif ($request->getRequestFormat() === "json") {
            /** @var AnimesEpisodes[] $episodes */
            $episodes = $episodes->getItems();

            foreach ($episodes as $episode) {
                $data["payload"]["animes"]["episodes"][] = $episode->convert2Array();
            }

            return new JsonResponse($data);
        }
    }


    public function ajaxRequestAction(Request $request)
    {

        $url = $this->generateUrl('hwi_oauth_connect');

        /** @var Users $user */
        if(!$user = $this->getUser()) {
            $renderData["title"] = "Error - Login Required";
            $renderData["msg"] = "You need to login to use this feature.";
            $renderData['closeButton'] = false;
            $renderData["buttons"][] = array("text"=>"Close", "js"=>"onclick=".'"'."$('#myModal').remove();$('.modal-backdrop').remove()".'"', "class"=>"btn-primary");
            $renderData["buttons"][] = array("text"=>"Login", "js"=>"onclick=".'"'."window.location='".$url.'"', "class"=>"btn-primary");
        } else {
            $renderData = [];

            switch($request->get('op')) {
                case "mark_favorite":
                    /** @var UsersFavoritesRepository $usersRepo */
                    $usersRepo = $this->getDoctrine()->getRepository('LoopAnimeUsersBundle:UsersFavorites');
                    $renderData["title"] = "Operation - Mark as Favorite";
                    if($usersRepo->setAnimeAsFavorite($this->getUser(), $request->get("id_anime"))) {
                        $renderData["msg"] = "Anime was Marked/Dismarked as favorite.";
                    } else {
                        $renderData["msg"] = "Technical error - Please try again later.";
                    }
                    break;
                case "get_last_position":
                    //TODO
                    //echo $animes_obj->getLastPositionOnEpisode($id_episode, $id_user, $id_link);
                    exit;
                    break;
                case "mark_as_seen":
                    /** @var ViewsRepository $viewsRepo */
                    $viewsRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:Views');
                    $renderData["title"] = "Operation - Mark as (Un)Seen";
                    if($viewsRepo->setEpisodeAsSeen($user, $request->get("id_episode"), $request->get('id_link'))) {
                        $renderData["msg"] = "Episode marked as seen.";
                    } else {
                        $renderData["msg"] = "Technical error - Please try again later.";
                    }
                    break;
                case "comment_episode":
                    $renderData["title"] = "Operation - Mark as (Un)Seen";
                    $commentController = new CommentsController();
                    if($commentController->setCommentOnEpisode($request->get('id_episode'),$request->get('comment'))) {
                        $renderData["msg"] = "Comment has been created successfully!";
                    } else {
                        $renderData["msg"] = "Technical error - Please try again later.";
                    }

                    break;
                case "rating":
                    $renderData["title"] = "Operation - Rating";
                    $ratingUp = ($request->get('ratingUp') ? true : false);
                    if($userController->setRatingOnEpisode($request->get('ratingUp'), $request->get("id_episode"))) {
                        $renderData["msg"] = "Thank you for voting.";
                    } else {
                        $renderData["msg"] = "Technical error - Please try again later.";
                    }
                    break;
                default:
                    $renderData["title"] = "Operation Unknow";
                    $renderData["msg"] = "Technical error - Please try again later.";
                    break;
            }

        }
        $renderData['closeButton'] = false;
        $renderData['buttons'][] = array("text"=>"Close", "js"=>"onclick=".'"'."$('#myModal').remove();$('.modal-backdrop').remove()".'"', "class"=>"btn-primary");
        return $this->render("LoopAnimeShowsBundle:extra:modalWindow.html.twig", $renderData);
    }

    /**
     * Set an entire Animes or Seasons as seen
     * @param integer $id_anime
     * @param integer $id_season
     * @param integer $id_user
     * @return string MSG of error or Success. The message also includes a <status> tag with result "OK" or "KO"
     */
    public function setAllEpisodesAsSeen($id_anime = 0, $id_season = 0, $id_user) {

        $where_clause = " AND TRUE";

        if(!empty($id_anime))
            $where_clause .= " AND animes.id_anime = '$id_anime'";

        if(!empty($id_season))
            $where_clause .= " AND animes_seasons.id_season = '$id_season'";

        // Updates all animes "ON WATCH" to seen
        $query = "UPDATE views, animes, animes_episodes, animes_seasons SET views.completed = 1, view_time = NOW()
				  WHERE
					views.completed = 0
					AND animes.id_anime = animes_seasons.id_anime
					AND animes_episodes.id_season = animes_seasons.id_season
					AND views.id_episode = animes_episodes.id_episode
					AND views.id_user = '$id_user'
					$where_clause";

        $this->db->Query($query);

        // Inserts in views all episodes there as neever been watched (Even played)
        $query = "INSERT INTO views (id_episode, id_user, view_time, completed)
				  SELECT animes_episodes.id_episode, '$id_user', NOW(), '1'
				  FROM animes
				  	JOIN animes_seasons USING(id_anime)
				  	JOIN animes_episodes USING(id_season)
				  	LEFT JOIN views ON views.id_episode = animes_episodes.id_episode AND views.id_user = '$id_user'
				  WHERE
				    views.id_view IS NULL
				    AND animes_episodes.air_date <= NOW()
				    $where_clause
				  ";
        $this->db->Query($query);

        return true;


    }

}
