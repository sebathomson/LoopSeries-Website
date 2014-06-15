<?php

namespace LoopAnime\CommentsBundle\Controller;

use Knp\Component\Pager\Paginator;
use LoopAnime\CommentsBundle\Entity\Comments;
use LoopAnime\CommentsBundle\Entity\CommentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentsController extends Controller
{
    public function setCommentOnEpisode($idEpisode, $text, $title = "Comment")
    {
        if (!$user = $this->getUser()) {
            throw new \Exception("You need to be logged on to create a comment.");
        }

        if (empty($idEpisode) || empty($text)) {
            throw new \Exception("There must be a valid comment and a valid episode.");
        }

        $comment = new Comments();
        $comment->setIdUser($user->getId());
        $comment->setComment($text);
        $comment->setCommentTitle($title);
        $comment->setIdEpisode($idEpisode);

        $this->getDoctrine()->getManager()->persist($comment);
        $this->getDoctrine()->getManager()->flush();

        return true;
    }

    public function listEpisodesCommentsAction($idEpisode, Request $request)
    {
        /** @var CommentsRepository $commentsRepo */
        $commentsRepo = $this->getDoctrine()->getRepository('LoopAnime\CommentsBundle\Entity\Comments');

        $query = $commentsRepo->getCommentsByEpisode($idEpisode, false);

        if($request->getRequestFormat() === "html") {

            /** @var Paginator $paginator */
            $paginator  = $this->get('knp_paginator');
            $comments = $paginator->paginate(
                $query,
                $request->query->get('page', 1),
                10
            );

            return $this->render("LoopAnimeShowsBundle:Animes:listAnimes.html.twig", array("comments" => $comments));
        } elseif($request->getRequestFormat() === "json") {

            /** @var Comments[] $comments */
            $comments = $query->getResult();

            if(empty($comments)) {
                throw $this->createNotFoundException("The anime does not exists or was removed.");
            }

            $data = [];
            foreach($comments as $comment) {
                $data["payload"]["comments"][] = $this->convert2Array($comment);
            }

            return new JsonResponse($data);
        } else {
            return $this->createNotFoundException("Invalid format required.");
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
