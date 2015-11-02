<?php
namespace LoopAnime\ApiBundle\Exceptions;

class ResponseNotFoundException extends \Exception
{

    protected $message = 'Response % for the entity %s was not found!';

    public function __construct($response, $class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        parent::__construct(sprintf($this->message, $response, $class));
    }

}
