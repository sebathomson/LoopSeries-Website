<?php

namespace LoopAnime\ApiBundle\Controller;

use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EpisodesController extends BaseController
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getEpisodesAction(Request $request)
    {
        /** @var AnimesEpisodesRepository $episodesRepo */
        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');
        if ($request->get('season') != "") {
            $episodes = $episodesRepo->getEpisodesBySeason($request->get('season'));
        } elseif ($request->get('anime') != "") {
            $episodes = $episodesRepo->getEpisodesByAnime($request->get('anime'));
        } else {
            throw new \Exception("Season or Anime parameter is required to get episodes");
        }

        $view = $this->view($episodes, 200);
        return $this->handleView($view);
    }

    public function getEpisodeAction($episode)
    {
        /** @var AnimesEpisodesRepository $episodesRepo */
        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');
        $episode = $episodesRepo->find($episode);
        if ($episode === null) {
            throw new NotFoundHttpException("Episode does not exists.");
        }
        $view = $this->view($episode, 200);
        return $this->handleView($view);
    }

}
