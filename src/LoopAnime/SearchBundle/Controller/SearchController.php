<?php

namespace LoopAnime\SearchBundle\Controller;

use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{

    public function searchAction($term, Request $request)
    {
        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');

        /** @var AnimesRepository $animeRepo */
        $animeRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:Animes');
        $animesq = $animeRepo->getAnimesByTitle($term);
        /** @var SlidingPagination $animes */
        $animes = $paginator->paginate(
            $animesq,
            $request->query->get('page', 1),
            $request->query->get('maxr', 20)
        );

        /** @var AnimesEpisodesRepository $episodesRepo */
        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');
        $episodesq = $episodesRepo->getEpisodesByTitle($term);
        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        /** @var SlidingPagination $episodes */
        $episodes = $paginator->paginate(
            $episodesq,
            $request->query->get('page2', 1),
            $request->query->get('maxr2', 20)
        );

        return $this->render('LoopAnimeSearchBundle:Search:index.html.twig', ['animes' => $animes, 'episodes' => $episodes]);
    }

}