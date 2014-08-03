<?php
/**
 * Created by PhpStorm.
 * User: joshlopes
 * Date: 28/05/2014
 * Time: 19:30
 */

namespace LoopAnime\UsersBundle\Controller;


use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\Views;
use LoopAnime\ShowsBundle\Entity\ViewsRepository;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserActionsController extends Controller {

    public function setPreferencesAction(Request $request)
    {
        /** @var Users $user */
        $user = $this->getUser();
        /** @var UsersRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\Users');
        $user = $usersRepo->find($user->getId());
        $userPreferences = $user->getPreferences();

        // Togle Show Specials
        if($request->get("showSpecials")) {
            $userPreferences->togglePreference("ShowSpecials");
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($userPreferences);
        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }

    public function listUsersAction(Request $request)
    {
        /** @var UsersRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\Users');

        $query = $usersRepo->getAllUsers();

        $paginator  = $this->get('knp_paginator');
        /** @var Users $users */
        $users = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $request->query->get('maxr', 10)
        );

        if($request->getRequestFormat() === "json") {
            $data = [];
            foreach($users as $userInfo) {
                $data["payload"]["users"][] = $userInfo->convert2Array();
            }
            return new JsonResponse($data);
        }
        $stats = [];
        foreach($users as $user) {
            $stats[$user->getId()] = $usersRepo->getStats($user);
        }
        return $this->render("LoopAnimeUsersBundle:users:listUsers.html.twig", array("users" => $users, "stats" => $stats));
    }

    public function getUserAction($idUser, Request $request)
    {
        /** @var UsersRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\Users');
        /** @var Users $user */
        $user = $usersRepo->getUser($idUser);

        if($request->getRequestFormat() === "json") {
            $data["payload"]["users"][] = $user->convert2Array();
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeUsersBundle:users:userInfo.html.twig", array("user" => $user));
    }

    public function setEpisodeAsSeen($idEpisode, $idLink)
    {
        /** @var Users $user */
        $user = $this->getUser();
        /** @var ViewsRepository $viewsRepo */
        $viewsRepo = $this->getDoctrine()->getManager('LoopAnimeShowsBundle:Views');

        if(!empty($idEpisode) and !empty($id_user)) {

            $favorite = $viewsRepo->isEpisodeSeen($user, $idEpisode);

            // If is set remove -- else insert
            if($favorite) {
                /** @var Views $favorite */
                $favorite = $viewsRepo->findBy(['idEpisode' => $idEpisode, 'idUser' => $user->getId()]);
                $favorite->setCompleted(0);
                $this->getDoctrine()->getManager()->persist($favorite);
                $this->getDoctrine()->getManager()->flush();
            } else {
                $view = new Views();
                $view->setCompleted(1);
                $view->setIdEpisode($idEpisode);
                $view->setIdLink($idLink);
                $view->setIdUser($user->getId());
                $this->getDoctrine()->getManager()->persist($view);
                $this->getDoctrine()->getManager()->flush();
            }
        }
        return true;
    }

    public function setRatingOnEpisode($ratingUp, $idEpisode)
    {

        /** @var AnimesEpisodesRepository $episodesRepo */
        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
        /** @var AnimesEpisodes $episode */
        $episode = $episodesRepo->find($idEpisode);

        if(isset($_SESSION['checks']['rating']))
            $check_ratings = $_SESSION['checks']['rating'];
        else
            $check_ratings = array();

        // Check if there is a rate already
        if(isset($check_ratings[$idEpisode])) {
            // Change of hear - Up to Down
            if($check_ratings[$idEpisode] == "up" and !$ratingUp) {
                $episode->setRatingUp($episode->getRatingUp() - 1);
                $episode->setRatingDown($episode->getRatingDown() + 1);
            }
            elseif($check_ratings[$idEpisode] == "down" and $ratingUp) {
                $episode->setRatingUp($episode->getRatingUp() + 1);
                $episode->setRatingDown($episode->getRatingDown() - 1);
            }
        } else {
            $episode->setRatingCount($episode->getRatingCount() + 1);
            if($ratingUp)
                $episode->setRatingUp($episode->getRatingUp() + 1);
            else
                $episode->setRatingDown($episode->getRatingDown() + 1);
        }

        $this->getDoctrine()->getManager()->persist($episode);
        $this->getDoctrine()->getManager()->flush($episode);

        // Sets on Session what pick he choose
        $_SESSION['checks']['rating'][$idEpisode] = ($ratingUp ? "up" : "down");

        return true;

    }

} 