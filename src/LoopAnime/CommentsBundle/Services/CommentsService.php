<?php

namespace LoopAnime\CommentsBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\CommentsBundle\Entity\Comments;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Component\Validator\Constraints\DateTime;

class CommentsService
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function setCommentOnEpisode(AnimesEpisodes $episode, Users $user, $text, $title = "Comment")
    {

        $comment = new Comments();
        $comment->setUser($user);
        $comment->setComment($text);
        $comment->setCommentTitle($title);
        $comment->setIdEpisode($episode->getId());
        $comment->setCreateTime(new \DateTime("now"));
        $comment->setRatingCount(0)->setRatingDown(0)->setRatingUp(0);

        $this->em->persist($comment);
        $this->em->flush();

        return true;
    }
}