<?php

namespace LoopAnime\ShowsBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersPreferences;
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
     * @Route("/animes/get-episodes.{_format}", requirements={"_format" = "html|json"}, defaults={"_format" = "html"})
     */
    public function getEpisodesAction($_format, Request $request)
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

        if($_format === "html") {
            $render = $this->render("LoopAnimeShowsBundle:Default:videoGallery.html.twig", array("recentsEpisodes" => $data["payload"]["animes"]["episodes"]));
            return $render;
        } elseif($_format === "json") {
            return new JsonResponse($data);
        }
    }

}
