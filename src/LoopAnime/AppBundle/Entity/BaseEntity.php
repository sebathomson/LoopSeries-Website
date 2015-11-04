<?php
namespace LoopAnime\AppBundle\Entity;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use LoopAnime\ApiBundle\Exceptions\ResponseNotFoundException;

class BaseEntity
{

    public function serialize($context = null, $toArray = false, $constructorArguments = [])
    {
        $serializer = SerializerBuilder::create()->build();
        if (null !== $context) {
            $context = SerializationContext::create()->setGroups(array($context));
        }
        $data = $serializer->serialize($this->getResponse($constructorArguments), 'json', $context);
        if ($toArray) {
            $data = json_decode($data, true);
        }
        return $data;
    }

    public function getResponse($constructorArguments = [])
    {
        $class = explode("\\", get_class($this));
        $class = array_pop($class);
        $response = '\\LoopAnime\\ApiBundle\\Response\\' . $class . "Response";
        if (!class_exists($response)) {
            throw new ResponseNotFoundException($response, $this);
        }
        if (method_exists($response, "create")) {
            $constructorArguments[] = $this;
            return call_user_func_array([$response, "create"], $constructorArguments);
        }
        return new $response($this);
    }

}
