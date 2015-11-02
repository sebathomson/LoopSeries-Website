<?php

namespace LoopAnime\ApiBundle\Controller;

use LoopAnime\ApiBundle\Exceptions\ParameterRequiredException;
use LoopAnime\ApiBundle\Exceptions\ResourceNotFoundException;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EpisodeController extends BaseController
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @ApiDoc(
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when requirements are not valid.",
     *  },
     *  resource=true,
     *  description="Return Episodes List",
     *  filters={
     *      {"name"="season", "dataType"="integer", "requirement"="\d+", "description"="Id of the season resource", "required"="false"},
     *      {"name"="anime", "dataType"="integer", "requirement"="\d+", "description"="Id of the Anime resource", "required"="false"}
     *  },
     *  requirements={
     *      {"name"="access_token", "dataType"="string", "requirement"="ACCESS_TOKEN", "description"="Your access token", "required"="true"}
     *  }
     * )
     */
    public function getEpisodesAction(Request $request)
    {
        /** @var AnimesEpisodesRepository $episodesRepo */
        $episodesRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesEpisodes');

        if (empty($request->get('season')) && empty($request->get('anime'))) {
            throw new ParameterRequiredException(['season', 'anime']);
        }

        $episodes = [];
        if ($request->get('season') != "") {
            $episodes = $episodesRepo->getEpisodesBySeason($request->get('season'));
        } elseif ($request->get('anime') != "") {
            $episodes = $episodesRepo->getEpisodesByAnime($request->get('anime'));
        }

        $payload = [];
        foreach ($episodes as $episode) {
            $payload[] = $episode[0]->serialize(null, true);
        }
        $view = $this->view($payload, 200);
        return $this->handleView($view);
    }

    /**
     * @param $episode
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ApiDoc(
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when Resource could not be found",
     *  },
     *  resource=true,
     *  description="Return one episode resource",
     *  requirements={
     *      {"name"="episode", "dataType"="integer", "requirement"="\d+", "description"="Id of the episode resource"},
     *      {"name"="access_token", "dataType"="string", "requirement"="ACCESS_TOKEN", "description"="Your access token", "required"="true"}
     *  }
     * )
     */
    public function getEpisodeAction(AnimesEpisodes $episode)
    {
        if (null === $episode) {
            throw new ResourceNotFoundException($episode);
        }
        $view = $this->view($episode->serialize(null, true), 200);
        return $this->handleView($view);
    }

}
