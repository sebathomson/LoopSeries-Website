<?php

namespace LoopAnime\AppBundle\Enum;

class BaseEnum {

    static public function getAsArray()
    {
        $refl = new \ReflectionClass(get_called_class());

        return $refl->getConstants();
    }

    static public function isValid($key)
    {
        $consts = self::getAsArray();

        return in_array($key, $consts);
    }

    static public function get($key) {
        if (self::isValid($key)) {
            $array = self::getAsArray();
            return $array[$key];
        }
        return false;
    }

}
