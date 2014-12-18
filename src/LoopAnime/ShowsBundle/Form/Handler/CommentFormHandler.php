<?php

namespace LoopAnime\ShowsBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class RegistrationFormHandler {

    public function __construct(FormInterface $form, Request $request)
    {
        $this->form = $form;
        $this->request = $request;
    }

    protected function onSuccess()
    {

    }
}