<?php

namespace LoopAnime\Helpers\Crawlers;

class Anime44 {

    private static $configLink = "http://www.anitube.se/nuevo/econfig.php?key={key}";

    public static function crawlVideoLink($videoLink) {

        $webpage_content = file_get_contents($videoLink);
        $url = parse_url($videoLink);
        $host = str_replace(array("www.",".com",".pt",".info",".es",".me",".net",".com.br","embed."),"",$url["host"]);

        switch($host) {
            case "play44":
            case "videofun":
                $offset = 0;
                $webpage_content = self::extractContent($webpage_content, "playlist:", $offset, "[", "[", "]");
                $i = 1;
                $offset = 0;
                while(substr_count($webpage_content, "url:") >= $i) {
                    $i++;
                    $videoLink = "http://" . trim(self::extractContent($webpage_content, "{", $offset, "url:", 'http://', ','),"',".'"');
                    if(!strpos($videoLink, ".jpg"))
                        break;
                }
                break;
            case "video44":
                $offset = 0;
                $videoLink = self::extractContent($webpage_content, '"player.swf"', $offset, "file:", '"', '"');
                break;
            case "yourupload":
                $offset = 0;
                $videoLink = "http:" . self::extractContent($webpage_content, 'jwplayer', $offset, "'file':", "'http:", "'");
                break;
        }

        if(isset($videoLink) && !empty($videoLink)) {
            return array("SQ" => urldecode($videoLink));
        } else {
            return false;
        }


    }

    // Extract content
    private static function extractContent($webpage_content, $offset_content, &$offset, $look4var, $from_string, $to_string) {
        $offset 	= strpos($webpage_content, $offset_content, $offset);
        $var 		= strpos($webpage_content, $look4var, $offset);
        $pos_init 	= strpos($webpage_content, $from_string, $var) + strlen($from_string);
        $pos_end 	= strpos($webpage_content, $to_string, $pos_init);
        $offset		= $pos_end;

        $substr = substr($webpage_content, $pos_init, $pos_end - $pos_init);
        return $substr;
    }

}