<?php
namespace LoopAnime\UsersBundle\Controller;


use Knp\Component\Pager\Paginator;
use LoopAnime\ShowsAPIBundle\Services\SyncAPI\MAL;
use LoopAnime\ShowsAPIBundle\Services\SyncAPI\TraktTV;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;
use LoopAnime\UsersBundle\Entity\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserCPController extends Controller
{

    public function indexAction(Request $request)
    {
        /** @var Users $user */
        $user = $this->getUser();

        if (empty($user))
            throw $this->createNotFoundException("User not found!");

        $form = $this->createForm('loopanime_user_usercp', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Users $updatedUser */
            $updatedUser = $this->getUser();
            $updatedUser->setUsername($form->get('username')->getData());
            $updatedUser->setBirthdate($form->get('birthdate')->getData());
            $updatedUser->setNewsletter($form->get('newsletter')->getData());
            $updatedUser->setCountry($form->get('country')->getData());
            $updatedUser->setLang($form->get('lang')->getData());
            $updatedUser->setCountry($form->get('country')->getData());
            $updatedUser->setAvatarFile($form->get('avatarFile')->getData());

            $em = $this->getDoctrine()->getManager();
            $updatedUser->uploadAvatar();
            $em->persist($updatedUser);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Your account was updated successfully'
            );
        }

        $syncTraktForm = $this->createForm('loopanime_sync_form_trakttv');
        $syncTraktForm->handleRequest($request);
        $trakt = $this->get("sync.trakt");
        if ($syncTraktForm->isSubmitted() && $syncTraktForm->isValid()) {
            $data = $syncTraktForm->getData();
            $user->setTraktUsername($data['username']);
            $user->setTraktPassword($data['password']);
            if($trakt->checkIfUserExists($user)) {
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                $trakt->importSeenEpisodes();
            }
        }

        $mal = $this->get("sync.mal");
        $syncMalForm = $this->createForm('loopanime_sync_form_myanimelist');
        $syncMalForm->handleRequest($request);
        if($syncMalForm->isSubmitted() && $syncMalForm->isValid()) {
            $data = $syncMalForm->getData();
            $user->setMALUsername($data['username']);
            $user->setMALPassword($data['password']);
            if($mal->checkIfUserExists($user)) {
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                $mal->importSeenEpisodes();
            }
        }

        return $this->render('LoopAnimeUsersBundle:userscp:home.html.twig', [
            'form' => $form->createView(),
            'syncTraktForm' => $syncTraktForm->createView(),
            'syncMALForm' => $syncMalForm->createView(),
        ]);
    }

    public function listFavAnimesAction(Request $request)
    {
        /** @var UsersFavoritesRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\UsersFavorites');
        $animesq = $usersRepo->getUsersFavoriteAnimes($this->getUser(), true);

        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $userFavorites = $paginator->paginate(
            $animesq,
            $request->query->get('page', 1),
            $request->query->get('maxr', 20)
        );

        if ($request->getRequestFormat() === "json") {
            $data = [];
            foreach ($userFavorites as $animeInfo) {
                $data["payload"]["animes"][] = $animeInfo->convert2Array();
            }
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeUsersBundle:UsersCP:favoriteAnimesList.html.twig", ["userFavorites" => $userFavorites]);
    }

    private function updateTrackSystem()
    {

    }

}