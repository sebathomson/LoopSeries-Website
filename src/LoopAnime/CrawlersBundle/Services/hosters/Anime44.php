<?php

namespace LoopAnime\CrawlersBundle\Services\hosters;

class Anime44 extends Hosters {

    protected $animeSearchLink = "http://www.anime44.com/anime/search?key={search_term}&search_submit=Go";

    /**
     * @return string
     */
    public function getName()
    {
        return "Anime44";
    }

    /**
     * @return string
     */
    public function getSubtitles()
    {
        return "EN";
    }

    public function isNeededLook4Anime()
    {
        return true;
    }

    public function getAnimesSearchLink()
    {
        return $this->animeSearchLink;
    }

    public function getEpisodesSearchLink()
    {
        return false;
    }

    public function isPaginated()
    {
        return true;
    }

    public function getNextPage($link)
    {
        $this->page++;
        if($this->page === 50) {
            throw new \Exception("Looping till the page 50, stoping here as i could be looping forever");
        }
        if(strpos($link,"/page/") === false) {
            $link = $link . '/page/' . $this->page;
        }
        $link = preg_replace('/page\/\d+/','page/'.$this->page,$link);

        try {
            $webpageContent = file_get_contents($link);
        } catch(\Exception $e) {
            return false;
        }
        if($this->lastPageContent === $webpageContent)
            return false;

        $this->lastPageContent = $webpageContent;
        return $link;
    }

    public function getPageParameter()
    {
        return false;
    }


    public function getEpisodeDirectLink($link)
    {
        $linkOriginal = $link;
        $webpage_content = file_get_contents($link);
        $url = parse_url($link);
        $host = str_replace(array("www.",".com",".pt",".info",".es",".me",".net",".com.br","embed.","org"),"",$url["host"]);
        switch($host) {
            case "play44":
            case "video44":
            case "byzoo":
                $matchs = [];
                preg_match_all("/_url.*=.*\"(.+)\"/m",$webpage_content,$matchs);
                $link = $matchs[1][0];
                break;
            case "videofun":
                $offset = 0;
                $webpage_content = $this->extractContent($webpage_content, "playlist:", $offset, "[", "[", "]");
                $i = 1;
                $offset = 0;
                while(substr_count($webpage_content, "url:") >= $i) {
                    $i++;
                    $link = "http://" . trim($this->extractContent($webpage_content, "{", $offset, "url:", 'http://', ','),"',".'"');
                    if(!strpos($link, ".jpg") && strlen($link) > 10)
                        break;
                }
                break;
            case "video44":
                $offset = 0;
                $link = $this->extractContent($webpage_content, '"player.swf"', $offset, "file:", '"', '"');
                break;
            case "yourupload":
                $offset = 0;
                $link = "http:" . $this->extractContent($webpage_content, 'jwplayer', $offset, "'file':", "'http:", "'");
                break;
        }
        if($linkOriginal === $link)
            return false;
        else
            return $link;
    }

    // Extract content
    private function extractContent($webpage_content, $offset_content, &$offset, $look4var, $from_string, $to_string) {
        $offset 	= strpos($webpage_content, $offset_content, $offset);
        $var 		= strpos($webpage_content, $look4var, $offset);
        $pos_init 	= strpos($webpage_content, $from_string, $var) + strlen($from_string);
        $pos_end 	= strpos($webpage_content, $to_string, $pos_init);
        $offset		= $pos_end;

        $substr = substr($webpage_content, $pos_init, $pos_end - $pos_init);
        return $substr;
    }
}
