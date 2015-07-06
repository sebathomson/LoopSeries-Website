<?php
namespace LoopAnime\AppBundle\Utility;


class StringUtility
{

    public static function cleanStringUrlMatcher($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return trim(strtolower(preg_replace('/-+/', '-', $string))); // Replaces multiple hyphens with single one.
    }

}
