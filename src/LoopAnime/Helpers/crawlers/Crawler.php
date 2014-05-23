<?php
/**
 * Created by PhpStorm.
 * User: joshlopes
 * Date: 23/05/2014
 * Time: 22:26
 */

namespace LoopAnime\Helpers\Crawlers;


use LoopAnime\Helpers\Crawlers\Anime44;
use LoopAnime\Helpers\Crawlers\Anitube;

class Crawler {

    /**
     * @param $hoster
     * @param $link
     *
     * @return array|bool
     */
    public static function crawlVideoLink($hoster, $link)
    {

        switch(strtolower($hoster)) {
            case "anime44":
                return Anime44::crawlVideoLink($link);
                break;
            case "anitube":
                return Anitube::crawlVideoLink($link);
                break;
        }

        return false;

    }

} 