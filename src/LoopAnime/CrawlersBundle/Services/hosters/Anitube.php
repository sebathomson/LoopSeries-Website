<?php

namespace LoopAnime\CrawlersBundle\Services\hosters;

class Anitube extends Hosters {

    public function isNeededLook4Anime()
    {
        return false;
    }

    public function getAnimesSearchLink()
    {
        return false;
    }

    public function getEpisodesSearchLink()
    {
        return "http://www.anitube.se/search/?search_id={search_term}";
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
        if(preg_match('/^.+search\/\?.+$/',$link)) {
            $link = str_replace("search/?","search/".$this->page."/?",$link);
        }
        $link = preg_replace('/search\/(\d)\/\?/','search/'.$this->page.'/?',$link);

        $webpageContent = file_get_contents($link);
        if($this->lastPageContent === $webpageContent)
            return false;

        $this->lastPageContent = $webpageContent;
        return $link;
    }

    public function getPageParameter()
    {
        return false;
    }

    /**
     * Parse the XML of playlist and searchs for the video file
     * @param string $link Link to the XML file / playlist
     * @return string|boolean
     */
    public function getEpisodeDirectLink($link)
    {

        $configLink = "http://www.anitube.se/nuevo/econfig.php?key={key}";
        $configLink = str_replace("{key}", basename($link), $configLink);
        $playerConfig = $configLink;

        if ($playerXML = simplexml_load_file($playerConfig)) {
            $playlistLink = (string)$playerXML->playlist;
            // Check if this file is a playlist else
            if ($playlistLink != "") {
                if ($playlist_xml = simplexml_load_file($playlistLink)) {
                    $video_link = (string)$playlist_xml->trackList->track->file;
                    $videohd_link = (string)$playlist_xml->trackList->track->filehd;
                    $html5_link = (string)$playlist_xml->trackList->track->html5;
                }
            } else {
                $video_link = (string)$playerXML->file;
                $videohd_link = (string)$playerXML->filehd;
                $html5_link = (string)$playerXML->html5;
            }

            if(!empty($videohd_link))
                return $videohd_link;
            elseif(!empty($html5_link))
                return $html5_link;
            elseif(!empty($video_link))
                return $video_link;
        }
        return false;
    }
}