<?php
namespace LoopAnime\UsersBundle\Controller;


use Knp\Component\Pager\Paginator;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserCPController extends Controller {

    public function indexAction(Request $request) {

        return $this->listFavAnimesAction($request);

    }

    public function listFavAnimesAction(Request $request) {

        /** @var UsersFavoritesRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\UsersFavorites');

        $animesq = $usersRepo->getUsersFavoriteAnimes($this->getUser(),true);

        if($request->getRequestFormat() === "html") {

            /** @var Paginator $paginator */
            $paginator  = $this->get('knp_paginator');
            $animes = $paginator->paginate(
                $animesq,
                $request->query->get('page', 1),
                10
            );

            if(empty($animes)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            return $this->render("LoopAnimeUsersBundle:UsersCP:favoriteAnimesList.html.twig", array("animes" => $animes));
        } elseif($request->getRequestFormat() === "json") {

            /** @var Animes[] $animes */
            $animes = $animesq->getResult();

            if(empty($animes)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            foreach($animes as $animeInfo) {
                $data["payload"]["animes"][] = $animeInfo->convert2Array();
            }

            return new JsonResponse($data);
        }

    }

}