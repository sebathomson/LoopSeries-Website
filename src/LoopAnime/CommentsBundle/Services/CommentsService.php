<?php

namespace LoopAnime\CommentsBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\CommentsBundle\Entity\Comments;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\UsersBundle\Entity\Users;

class CommentsService
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function commentEpisode(AnimesEpisodes $episode, Users $user, $text, $title = "Comment")
    {
        $comment = new Comments();
        $comment->setUser($user);
        $comment->setComment($text);
        $comment->setCommentTitle($title);
        $comment->setEpisode($episode);

        $this->em->persist($comment);
        $this->em->flush();

        return true;
    }
}
