<?php
namespace LoopAnime\UsersBundle\Controller;


use Knp\Component\Pager\Paginator;
use LoopAnime\ShowsAPIBundle\Services\SyncAPI\MAL;
use LoopAnime\ShowsAPIBundle\Services\SyncAPI\TraktTV;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;
use LoopAnime\UsersBundle\Entity\UsersRepository;
use LoopAnime\UsersBundle\Form\Type\SyncMAL;
use LoopAnime\UsersBundle\Form\Type\SyncTraktTv;
use LoopAnime\UsersBundle\Form\Type\UserCPFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Wubs\Trakt\Trakt;

class UserCPController extends Controller
{

    public function indexAction(Request $request)
    {

        /** @var UsersRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\Users');

        /** @var Users $user */
        $user = $this->getUser();
        $extra = [];
        $errors = [];

        $stats = $usersRepo->getStats($user);

        if (empty($user))
            throw $this->createNotFoundException("User not found!");

        $form = $this->createForm(new UserCPFormType($this->getDoctrine()->getManager()), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();
            /** @var Users $updatedUser */
            $updatedUser = $this->getUser();

            if ($form->get('buttonSubmit')->isClicked()) {
                $updatedUser->setUsername($form->get('username')->getData());
                $updatedUser->setBirthdate($form->get('birthdate')->getData());
                $updatedUser->setNewsletter($form->get('newsletter')->getData());
                $updatedUser->setCountry($form->get('country')->getData());
                $updatedUser->setLang($form->get('lang')->getData());
                $updatedUser->setCountry($form->get('country')->getData());
                $updatedUser->setAvatarFile($form->get('avatarFile')->getData());
            }

            if (count($errors) == 0) {
                $em = $this->getDoctrine()->getManager();

                $updatedUser->uploadAvatar();

                $em->persist($updatedUser);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Your account was updated successfully'
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    'There were one more more errors when attempt to update'
                );
            }

        }

        return $this->render('LoopAnimeUsersBundle:userscp:home.html.twig', array('form' => $form->createView(), 'extra' => $extra, 'stats' => $stats, 'errors' => $errors));
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

    public function listTrackedAnimesAction(Request $request)
    {
        /** @var TraktTV $trakt */
        $trakt = $this->get("sync.trakt");
        $syncTraktForm = $this->createForm(new SyncTraktTv($this->getDoctrine()->getManager(),$this->getUser()));
        $syncTraktForm->submit($request);
        if($syncTraktForm->isValid() && $request->request->has($syncTraktForm->getName())) {
            $data = $syncTraktForm->getData();
            /** @var Users $user */
            $user = $this->getUser();
            $user->setTraktUsername($data['username']);
            $user->setTraktPassword($data['password']);
            if($trakt->checkIfUserExists($user)) {
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                $trakt->importSeenEpisodes();
            }
        }

        /** @var MAL $mal */
        $mal = $this->get("sync.mal");
        $syncMALForm = $this->createForm(new SyncMAL($this->getDoctrine()->getManager(),$this->getUser()));
        $syncMALForm->submit($request);
        if($syncMALForm->isValid() && $request->request->has($syncMALForm->getName())) {
            $data = $syncMALForm->getData();
            /** @var Users $user */
            $user = $this->getUser();
            $user->setMALUsername($data['username']);
            $user->setMALPassword($data['password']);
            if($mal->checkIfUserExists($user)) {
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                $mal->importSeenEpisodes();
            }
        }

        /** @var UsersFavoritesRepository $usersRepo */
        $usersRepo = $this->getDoctrine()->getRepository('LoopAnime\UsersBundle\Entity\UsersFavorites');
        $animesq = $usersRepo->getUserTrackedEpisodes($this->getUser(), true);

        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $trackedEpisodes = $paginator->paginate(
            $animesq,
            $request->query->get('page', 1),
            $request->query->get('maxr', 20)
        );

        if ($request->getRequestFormat() === "json") {
            $data = [];
            foreach ($trackedEpisodes as $animeInfo) {
                $data["payload"]["animes"][] = $animeInfo->convert2Array();
            }
            return new JsonResponse($data);
        }
        return $this->render("LoopAnimeUsersBundle:UsersCP:trackSystem.html.twig",
            [
                "userFavorites" => [],
                "trackedEpisodes" => [],
                "syncTraktForm" => $syncTraktForm->createView(),
                "syncMALForm" => $syncMALForm->createView()
            ]);
    }

}