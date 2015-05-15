<?php

namespace LoopAnime\AppBundle\Enum;

class BaseEnum {

    public static function getAsArray()
    {
        $refl = new \ReflectionClass(get_called_class());

        return $refl->getConstants();
    }

    public static function isValid($key)
    {
        $consts = self::getAsArray();

        return in_array($key, $consts);
    }

    public static function get($key) {
        if (self::isValid($key)) {
            $array = self::getAsArray();
            return $array[$key];
        }
        return false;
    }

    public static function getAsChoices()
    {
        $array = array_values(self::getAsArray());
        $array = array_combine($array, $array);

        return $array;
    }
}
