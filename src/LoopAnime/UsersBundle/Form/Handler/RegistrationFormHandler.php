<?php

namespace LoopAnime\UsersBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use LoopAnime\GeneralBundle\Entity\Countries;

class RegistrationFormHandler extends BaseHandler {

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, EntityManager $entityManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->em = $entityManager;
    }

    /**
     * @param \FOS\UserBundle\Model\UserInterface|\LoopAnime\UsersBundle\Entity\Users $user
     * @param boolean $confirmation
     */
    protected function onSuccess(UserInterface $user, $confirmation)
    {
        $user->setCreateTime(New \DateTime("now"));
        $user->setStatus(0);

        $rep = $this->em->getRepository("LoopAnimeGeneralBundle:Countries");
        /** @var Countries[] $country **/
        $country = $rep->findBy(array("iso2"=>$user->getCountry()));

        $user->setLang($country[0]->getLanguage());

        parent::onSuccess($user, $confirmation);
    }
}