<?php

namespace LoopAnime\Helpers\Crawlers;

class Anitube {

    private static $configLink = "http://www.anitube.se/nuevo/econfig.php?key={key}";

    public static function crawlVideoLink($videoLink) {

        $configLink = str_replace("{key}", basename($videoLink), self::$configLink);

        if($playlist_xml = simplexml_load_file($configLink)) {

            $playlist_link = (string) $playlist_xml->playlist;
            // Check if its a part of a playlist, grab from the playlist
            if($playlist_link != "") {
                if($playlist_xml = simplexml_load_file($playlist_link)) {
                    $video_link 	= (string) $playlist_xml->trackList->track->file;
                    $videohd_link 	= (string) $playlist_xml->trackList->track->filehd;
                    $html5_link 	= (string) $playlist_xml->trackList->track->html5;
                } else {
                    return false;
                }
            } else {
                $video_link 	= (string) $playlist_xml->file;
                $videohd_link 	= (string) $playlist_xml->filehd;
                $html5_link 	= (string) $playlist_xml->html5;
            }

            $videoArr = array(
                "HQ"	=> $videohd_link,
                "SQ"	=> $html5_link,
                "DQ"	=> $video_link
            );

            return $videoArr;
        }
        return false;
    }

}