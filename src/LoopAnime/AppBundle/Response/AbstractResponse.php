<?php
namespace LoopAnime\AppBundle\Response;

class AbstractResponse implements ResponseInterface
{

    protected $errors;
    protected $payload;

    /**
     * @return boolean
     */
    public function isError()
    {
        if(count($this->errors) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function send()
    {
        return [
            'isError' => $this->isError(),
            'payload' => $this->getPayLoad(),
        ];
    }

    public function setError($errorMessage, $errorCode)
    {
        $this->errors[] = ['error' => $errorMessage, 'code' => $errorCode];
    }

    public function setPayloadData($payloadData)
    {
        $this->payload = $payloadData;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
