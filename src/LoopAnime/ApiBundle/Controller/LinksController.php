<?php
/**
 * Created by PhpStorm.
 * User: luislopes
 * Date: 08/09/2014
 * Time: 21:47
 */

namespace LoopAnime\ApiBundle\Controller;


use FOS\RestBundle\View\View;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use LoopAnime\ShowsBundle\Entity\AnimesLinksRepository;
use LoopAnime\ShowsBundle\Services\VideoService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LinksController extends BaseController {

    public function getLinksAction(Request $request)
    {
        /** @var AnimesLinksRepository $linksRepo */
        $linksRepo = $this->getDoctrine()->getRepository('LoopAnimeShowsBundle:AnimesLinks');
        if($request->get('episode') != "") {
            $links = $linksRepo->getLinksByEpisode($request->get('episode'));
        } else {
            throw new \Exception("Episode ID parameter is required");
        }

        $view = $this->view($links, 200);
        return $this->handleView($view);
    }

    public function getLinkAction(AnimesLinks $link)
    {
        if($link === null) {
           throw new NotFoundHttpException("Link not found");
        }

        /** @var VideoService $videoService */
        $videoService = $this->get('loopanime_video_service');
        $directLink = $videoService->getDirectVideoLink($link);

        /** @var View $view */
        $view = $this->view(['direct_link' => $directLink, $link], 200);
        return $this->handleView($view);
    }

} 