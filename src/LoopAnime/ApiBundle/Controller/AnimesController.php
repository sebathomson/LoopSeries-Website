<?php

namespace LoopAnime\ApiBundle\Controller;

use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnimesController extends BaseController {

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getAnimesAction(Request $request)
    {
        /** @var AnimesRepository $animesRepo */
        $animesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:Animes');
        $payload = $this->paginateObject($request, $animesRepo, []);

        $view = $this->view($payload, 200);
        return $this->handleView($view);
    }

    public function getAnimeAction(Animes $anime)
    {
        if($anime === null) {
            throw new NotFoundHttpException("Anime not found.");
        }

        $view = $this->view($anime, 200);
        return $this->handleView($view);
    }

} 