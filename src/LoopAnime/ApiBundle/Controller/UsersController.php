<?php

namespace LoopAnime\ApiBundle\Controller;

use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends BaseController {

    public function getUsersAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('LoopAnimeUsersBundle:Users');
        $payload = $this->paginateObject($request, $repository, []);

        $view = $this->view($payload, 200);
        return $this->handleView($view);
    }

    public function getUserAction(Users $user)
    {
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    public function getUserLoggedAction()
    {
        $user = $this->getUser();
        $user = $this->getDoctrine()->getRepository('LoopAnimeUsersBundle:Users')->find($user);
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    public function getUserCommentsAction(Users $user)
    {
        $comments = $this->getDoctrine()->getRepository('LoopAnimeCommentsBundle:Comments')->findBy(['idUser' => $user->getId()]);
        $view = $this->view($comments, 200);
        return $this->handleView($view);
    }

}
