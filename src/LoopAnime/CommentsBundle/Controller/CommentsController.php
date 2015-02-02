<?php

namespace LoopAnime\CommentsBundle\Controller;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use LoopAnime\CommentsBundle\Entity\Comments;
use LoopAnime\CommentsBundle\Entity\CommentsRepository;
use LoopAnime\CommentsBundle\Services\CommentsService;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentsController extends Controller
{
    public function listEpisodesCommentsAction($idEpisode, Request $request)
    {
        /** @var AnimesEpisodes $episode */
        $episode = $this->getDoctrine()->getManager()->find('LoopAnime\ShowsBundle\Entity\AnimesEpisodes', $idEpisode);

        /** @var CommentsRepository $commentsRepo */
        $commentsRepo = $this->getDoctrine()->getRepository('LoopAnime\CommentsBundle\Entity\Comments');
        $query = $commentsRepo->getCommentsByEpisode($episode, false);

        /** @var Paginator $paginator */
        $paginator  = $this->get('knp_paginator');
        /** @var SlidingPagination $comments */
        $comments = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $request->query->get('maxr', 10)
        );

        if($request->getRequestFormat() === "json") {
            $data = [];
            foreach($comments as $comment) {
                $data["payload"]["comments"][] = $this->convert2Array($comment);
            }
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeShowsBundle:Animes:episodeComments.html.twig", array("comments" => $comments));
    }

    public function commentEpisode(Request $request)
    {
        $renderData["title"] = "Operation - Mark as (Un)Seen";
        $commentController = new CommentsService($this->getDoctrine()->getManager());
        $user = $this->getUser();
        /** @var AnimesEpisodes $episode */
        $episode = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes')->find($request->get('id_episode'));
        if($commentController->setCommentOnEpisode($episode, $user, $request->get('comment'))) {
            $renderData["msg"] = "Comment has been created successfully!";
        } else {
            $renderData["msg"] = "Technical error - Please try again later.";
        }
    }

    private function convert2Array(Comments $comment)
    {

        return array(
            "id" => $comment->getId(),
            "author" => $comment->getOwner(),
            "ratingUp" => $comment->getRatingDown(),
            "ratingDown" => $comment->getRatingDown(),
            "ratingCount" => $comment->getRatingCount(),
            "commentTitle" => $comment->getCommentTitle(),
            "createTime" => $comment->getCreateTime(),
            "comment" => $comment->getComment(),
            "id_user" => $comment->getIdUser(),
            "id_episode" => $comment->getIdEpisode()
        );

    }

}
