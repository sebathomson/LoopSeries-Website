<?php

namespace LoopAnime\AppBundle\Response;

interface ResponseInterface {

    /**
     * @return boolean
     */
    public function isError();

    /**
     * @return array
     */
    public function send();
    public function setError($errorMessage, $errorCode);
    public function setPayloadData($payloadData);

}
