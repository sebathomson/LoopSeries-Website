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
use LoopAnime\UsersBundle\Controller\UserActionsController;
use LoopAnime\UsersBundle\Controller\UserController;
use LoopAnime\UsersBundle\Controller\UsersController;
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
            $animesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
            $renderData['anime'] = $animesRepo->getAnimeByEpisode($episode->getId(), true)[0];

            /** @var AnimesSeasonsRepository $seasonsRepo */
            $seasonsRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');
            $renderData['season'] = $seasonsRepo->getSeasonById($episode->getIdSeason(), true)[0];

            /** @var AnimesLinksRepository $linksRepo */
            $linksRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesLinks');
            $renderData['links'] = $linksRepo->getLinksByEpisode($episode->getId());

            $renderData['isIframe'] = false;

            if ($nextEpisode = $episodesRepo->getNavigateEpisode($episode->getId())) {
                $renderData['nextEpisode'] = $nextEpisode;
            }

            if ($prevEpisode = $episodesRepo->getNavigateEpisode($episode->getId(), false)) {
                $renderData['prevEpisode'] = $prevEpisode;
            }

            $render = $this->render(
                "LoopAnimeShowsBundle:Animes:episode.html.twig",
                $renderData);
            return $render;
        } elseif ($request->getRequestFormat() === "json") {

            $data["payload"]["episodes"][] = $this->convert2Array($episode);

            return new JsonResponse($data);
        }

    }

    public function getEpisodesAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

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

        $where = "ae.airDate <= CURRENT_TIMESTAMP()";

        switch ($typeEpisode) {
            case "recent":
                $orderBy = "ae.airDate DESC";
                $dql = 'SELECT ae FROM LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae WHERE ' . $where . ' ORDER BY ' . $orderBy;
                break;
            case "mostview":
                $orderBy = "ae.views DESC";
                $dql = 'SELECT ae FROM LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae WHERE ' . $where . ' ORDER BY ' . $orderBy;
                break;
            case "mostrated":
                $orderBy = "ae.rating DESC, ae.ratingCount DESC, ae.ratingUp DESC";
                $dql = 'SELECT ae FROM LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae WHERE ' . $where . ' ORDER BY ' . $orderBy;
                break;
            case "userRecent":
                if (!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }

                $order = "DESC";
                if ($userPreferences->getTrackEpisodesSort())
                    $order = $userPreferences->getTrackEpisodesSort();

                $orderBy = "animes_seasons.season $order, animes_episodes.episode $order";
                $dql = '
                SELECT animesEpisodes FROM
                    LoopAnime\UsersBundle\Entity\UsersFavorites uf
						JOIN uf.anime animes
						JOIN animes.animesSeasons animesSeasons
                        JOIN animesSeasons.AnimesEpisodes animesEpisodes
                    WHERE ' . $where . ' ORDER BY ' . $orderBy;
                break;
            case "userFuture":
                if (!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }

                // User Preferences
                if ($userPreferences->getFutureListSpecials())
                    $where .= " AND animesSeasons.season > 0";

                $orderBy = "ae.airDate ASC";
                $dql = '
                SELECT animesEpisodes FROM
                    LoopAnime\UsersBundle\Entity\UsersFavorites uf
						JOIN uf.idAnime animes
						JOIN animes.AnimesSeasons animesSeasons
                        JOIN animesSeasons.AnimesEpisodes animesEpisodes
                    WHERE ' . $where . ' ORDER BY ' . $orderBy;
                break;
                break;
            case "userHistory":
                if (!$user) {
                    throw new \Exception("You need to be logged to see this content");
                }
                break;
        }


        $query = $entityManager->createQuery($dql);

        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        /** @var SlidingPagination $episodes */
        $episodes = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $maxResults
        );

        if (!$episodes->valid()) {
            throw $this->createNotFoundException("There isn't any recent episodes today!");
        }

        $data = [];

        if ($request->getRequestFormat() === "html") {
            $render = $this->render("LoopAnimeShowsBundle:extra:videoGallery.html.twig", array("episodes" => $episodes));
            return $render;
        } elseif ($request->getRequestFormat() === "json") {

            $episodes = $episodes->getItems();

            foreach ($episodes as $episode) {
                $data["payload"]["animes"]["episodes"][] = $this->convert2Array($episode);
            }

            return new JsonResponse($data);
        }
    }


    public function ajaxRequestAction(Request $request)
    {

        $url = $this->generateUrl(
            'blog_show',
            array('slug' => 'my-blog-post')
        );

        /** @var Users $user */
        if(!$user = $this->getUser()) {
            $renderData["title"] = "Error - Login Required";
            $renderData["msg"] = "You need to login to use this feature.";
            $renderData['closeButton'] = false;
            $renderData["buttons"][] = array("text"=>"Close", "js"=>"onclick=".'"'."$('#myModal').remove();$('.modal-backdrop').remove()".'"', "class"=>"btn-primary");
            $renderData["buttons"][] = array("text"=>"Login", "js"=>"onclick=".'"'."window.location='".$url.'"', "class"=>"btn-primary");
        } else {

            $id_user = $user->getId();
            $userController = new UserActionsController();
            $renderData = [];

            switch($request->get('op')) {
                case "mark_favorite":
                    $renderData["title"] = "Operation - Mark as Favorite";
                    if($userController->setAnimeAsFavorite($request->get("id_anime"))) {
                        $renderData["msg"] = "Anime was marked as favorite.";
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
                    $renderData["title"] = "Operation - Mark as (Un)Seen";
                    if($userController->setEpisodeAsSeen($request->get("id_episode"), $request->get('id_link'))) {
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
        $this->render("LoopAnimeShowsBundle:extra:modalWindow.html.twig", $renderData);

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

    public function convert2Array(AnimesEpisodes $episodeInfo)
    {
        return array(
            "id" => $episodeInfo->getId(),
            "poster" => $episodeInfo->getPoster(),
            "idSeason" => $episodeInfo->getIdSeason(),
            "airDate" => $episodeInfo->getAirDate(),
            "absoluteNumber" => $episodeInfo->getAbsoluteNumber(),
            "views" => $episodeInfo->getViews(),
            "title" => $episodeInfo->getEpisodeTitle(),
            "episodeNumber" => $episodeInfo->getEpisode(),
            "rating" => $episodeInfo->getRating(),
            "summary" => $episodeInfo->getSummary(),
            "ratingUp" => $episodeInfo->getRatingUp(),
            "ratingDown" => $episodeInfo->getRatingDown()
        );
    }

}
