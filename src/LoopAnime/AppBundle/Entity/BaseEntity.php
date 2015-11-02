<?php
namespace LoopAnime\AppBundle\Entity;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use LoopAnime\ApiBundle\Exceptions\ResponseNotFoundException;

class BaseEntity
{

    public function serialize($context = null, $toArray = false)
    {
        $serializer = SerializerBuilder::create()->build();
        if (null !== $context) {
            $context = SerializationContext::create()->setGroups(array($context));
        }
        $data = $serializer->serialize($this->getResponse(), 'json', $context);
        if ($toArray) {
            $data = json_decode($data, true);
        }
        return $data;
    }

    public function getResponse()
    {
        $class = explode("\\", get_class($this));
        $class = array_pop($class);
        $response = '\\LoopAnime\\ApiBundle\\Response\\' . $class . "Response";
        if (!class_exists($response)) {
            throw new ResponseNotFoundException($response, $this);
        }
        return new $response($this);
    }

}
