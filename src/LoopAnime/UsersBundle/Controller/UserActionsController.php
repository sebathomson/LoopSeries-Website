<?php
/**
 * Created by PhpStorm.
 * User: joshlopes
 * Date: 28/05/2014
 * Time: 19:30
 */

namespace LoopAnime\UsersBundle\Controller;


use LoopAnime\CommentsBundle\Entity\CommentsRepository;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\Views;
use LoopAnime\ShowsBundle\Entity\ViewsRepository;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersFavorites;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;
use LoopAnime\UsersBundle\Entity\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserActionsController extends Controller
{

    public function setPreferencesAction(Request $request)
    {
        /** @var Users $user */
        $user = $this->getUser();
        /** @var UsersRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\Users');
        $user = $usersRepo->find($user->getId());
        $userPreferences = $user->getPreferences();

        // Togle Show Specials
        if ($request->get("showSpecials")) {
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

        $paginator = $this->get('knp_paginator');
        /** @var Users $users */
        $users = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $request->query->get('maxr', 10)
        );

        if ($request->getRequestFormat() === "json") {
            $data = [];
            foreach ($users as $userInfo) {
                $data["payload"]["users"][] = $userInfo->convert2Array();
            }
            return new JsonResponse($data);
        }
        $stats = [];
        foreach ($users as $user) {
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
        $stats = $usersRepo->getStats($user);
        /** @var UsersFavoritesRepository $usersFavRepo */
        $usersFavRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\UsersFavorites');
        $favorites = $usersFavRepo->getUsersFavoriteAnimes($this->getUser(), false);
        /** @var CommentsRepository $commentsRepo */
        $commentsRepo = $this->getDoctrine()->getRepository('LoopAnime\CommentsBundle\Entity\Comments');
        $comments = $commentsRepo->getCommentsByUser($user);

        $animeStats = [];
        foreach ($favorites as $favorite) {
            $animeStats[$favorite->getId()] = $favorite->getStats($user);
        }

        if ($request->getRequestFormat() === "json") {
            $data["payload"]["users"][] = $user->convert2Array();
            $data['payload']['favorites'][] = $favorites->convert2Array();
            $data['payload']['comments'][] = $comments->convert2Array();
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeUsersBundle:users:userProfile.html.twig", [
            "user" => $user,
            "favorites" => $favorites,
            "comments" => $comments,
            "stats" => $stats,
            "animeStats" => $animeStats
        ]);
    }

} 