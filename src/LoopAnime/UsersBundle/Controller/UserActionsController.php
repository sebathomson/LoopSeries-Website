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
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersFavorites;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;
use LoopAnime\UsersBundle\Entity\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserActionsController extends Controller {

    public function setPreferences(Users $user, Request $request)
    {
        // Togle Show Specials
        if($request->get("showSpecials")) {
        }
    }

    public function listUsersAction(Request $request)
    {
        /** @var UsersRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\Users');

        $query = $usersRepo->getAllUsers();

        if($request->getRequestFormat() === "html") {

            $paginator  = $this->get('knp_paginator');
            $users = $paginator->paginate(
                $query,
                $request->query->get('page', 1),
                10
            );

            if(empty($users)) {
                throw $this->createNotFoundException("There is no users");
            }

            return $this->render("LoopAnimeUsersBundle:users:listUsers.html.twig", array("users" => $users));
        } elseif($request->getRequestFormat() === "json") {

            /** @var Users[] $users */
            $users = $query->getResult();

            if(empty($users)) {
                throw $this->createNotFoundException("There is no users");
            }

            $data = [];
            foreach($users as $seasonInfo) {
                $data["payload"]["users"][] = $this->convert2Array($users);
            }

            return new JsonResponse($data);
        }
    }

    public function getUserAction($idUser, Request $request)
    {

        /** @var UsersRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\User');

        /** @var Users $user */
        $user = $usersRepo->getUser($idUser);

        if(empty($user)) {
            throw $this->createNotFoundException("User not found nor dont exist");
        }

        if($request->getRequestFormat() === "html") {

            if(empty($users)) {
                throw $this->createNotFoundException("There is no users");
            }

            return $this->render("LoopAnimeUsersBundle:users:userInfo.html.twig", array("user" => $user));
        } elseif($request->getRequestFormat() === "json") {

            $data["payload"]["users"][] = $this->convert2Array($user);

            return new JsonResponse($data);
        }

    }

    public function setAnimeAsFavorite($idAnime) {

        /** @var Users $user */
        $user = $this->getUser();

        if(!$user) {
            throw new \Exception("You need to be logged to perform this action.");
        }

        /** @var UsersFavoritesRepository $userFavoritesRepo */
        $userFavoritesRepo = $this->getDoctrine()->getManager("LoopAnime\UsersBundle\Entity\UsersFavorites");

        if(!empty($id_anime) and !empty($id_user)) {

            $favorite = $userFavoritesRepo->getAnimeFavorite($idAnime, $user->getId());

            // If is set remove -- else insert
            if($favorite) {
                $this->getDoctrine()->getManager()->remove($favorite);
                $this->getDoctrine()->getManager()->flush();
            } else {
                $userFavorite = new UsersFavorites();
                $userFavorite->setIdAnime($idAnime);
                $userFavorite->setIdUser($user->getId());
                $this->getDoctrine()->getManager()->persist($userFavorite);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return true;

    }

    public function setEpisodeAsSeen($idEpisode, $idLink)
    {

        /** @var Users $user */
        $user = $this->getUser();

        if(!$user) {
            throw new \Exception("You need to be logged to perform this action.");
        }

        /** @var UsersFavoritesRepository $userFavoritesRepo */
        $userFavoritesRepo = $this->getDoctrine()->getManager("LoopAnime\UsersBundle\Entity\UsersFavorites");

        if(!empty($id_anime) and !empty($id_user)) {

            $favorite = $userFavoritesRepo->getAnimeFavorite($idAnime, $user->getId());

            // If is set remove -- else insert
            if($favorite) {
                $this->getDoctrine()->getManager()->remove($favorite);
                $this->getDoctrine()->getManager()->flush();
            } else {
                $userFavorite = new UsersFavorites();
                $userFavorite->setIdAnime($idAnime);
                $userFavorite->setIdUser($user->getId());
                $this->getDoctrine()->getManager()->persist($userFavorite);
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

    private function convert2Array(Users $users)
    {

        return array(
            "id" => $users->getId(),
            "avatar" => $users->getAvatar(),
            "birthdate" => $users->getBirthdate(),
            "username" => $users->getUsername(),
            "country" => $users->getCountry(),
            "email" => $users->getEmail(),
            "lastLogin" => $users->getLastLogin(),
            "lang" => $users->getLang(),
            "newsletter" => $users->getNewsletter(),
            "createTime" => $users->getCreateTime(),
            "status" => $users->getStatus(),
            "confirmationToken" => $users->getConfirmationToken()
        );

    }

} 