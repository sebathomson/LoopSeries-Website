<?php

namespace LoopAnime\ShowsBundle\Controller;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoriesController extends Controller
{

    public function listCategoriesAction(Request $request)
    {
        /** @var AnimesRepository $animesRepo */
        $animesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');

        $genresResults = $animesRepo->getGenres(false);
        $genres = [];
        foreach($genresResults as $result) {
            $genres = array_unique(array_merge($genres, explode(",",$result["genres"])));
        }

        $notIn = "0";
        $categories = [];

        foreach($genres as $genre) {

            /** @var Animes $animesByGenres */
            $animesByGenres = $animesRepo->getAnimesByGenres($genre, "", false);
            $numAnimes = count($animesByGenres);

            $animesByGenres2 = $animesRepo->getAnimesByGenres($genre, $notIn, false);
            $numAnimes2 = count($animesByGenres2);

            if($numAnimes2 > 0) {
                $animes = $animesByGenres2[0];
            } else {
                $animes = $animesByGenres[0];
            }

            $notIn .= ", " . $animes->getId();

            $categories[] = array(
                "genre" 	=> $genre,
                "poster" 	=> $animes->getPoster(),
                "numAnimes" => $numAnimes
            );

        }

        if($request->getRequestFormat() === "json") {

            $data["payload"]["categories"][] = $categories;

            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeShowsBundle:categories:index.html.twig", array("categories" => $categories));
    }

    public function getCategoryAction($category, Request $request)
    {

        /** @var AnimesRepository $animesRepo */
        $animesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
        $query = $animesRepo->getAnimesByGenres($category, "");

        /** @var SlidingPagination $animes */
        $paginator  = $this->get('knp_paginator');
        $animes = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $request->query->get('maxr', 10)
        );

        if ($request->getRequestFormat() === "json") {
            $data = [];
            foreach ($animes as $anime) {
                $data["payload"]["categories"]["animes"][] = $anime->convert2Array();
            }
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeShowsBundle:categories:categoryInfo.html.twig", array("animes" => $animes, "category" => $category));
    }

}
