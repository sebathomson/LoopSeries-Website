<?php

namespace LoopAnime\AppBundle\Crawler\Hoster;

use LoopAnime\AppBundle\Crawler\Enum\AnimeHosterEnum;
use LoopAnime\AppBundle\Crawler\Enum\StrategyEnum;
use LoopAnime\AppBundle\Crawler\Enum\VideoQualityEnum;
use LoopAnime\AppBundle\Crawler\Strategy\EpisodeSearchStrategy;

class AnitubeHoster extends AbstractHoster
{

    protected $searchLink = "http://www.anitube.se/search/?search_id={search_term}";

    public function getNextPage($link, $page)
    {
        if(preg_match('/^.+search\/\?.+$/',$link)) {
            $link = str_replace("search/?","search/".$page."/?",$link);
        }
        return preg_replace('/search\/(\d)\/\?/','search/'.$page.'/?',$link);
    }

    /**
     * Parse the XML of playlist and searchs for the video file
     * @param string $link Link to the XML file / playlist
     * @return string|boolean
     */
    public function getEpisodeMirros($link)
    {

        $configLink = "http://www.anitube.se/nuevo/econfig.php?key={key}";
        $configLink = str_replace("{key}", basename($link), $configLink);
        $playerConfig = $configLink;
        $mirrors = [];

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
                $mirrors[VideoQualityEnum::HIGHT_QUALITY][] = $videohd_link;
            elseif(!empty($html5_link))
                $mirrors[VideoQualityEnum::DEFAULT_QUALITY][] = $html5_link;
            elseif(!empty($video_link))
                $mirrors[VideoQualityEnum::DEFAULT_QUALITY][] = $video_link;
        }

        return empty($mirrors) ? false : $mirrors;
    }

    public function getSubtitles()
    {
        return "BR";
    }

    public function getStrategy()
    {
        return StrategyEnum::STRATEGY_EPISODE_SEARCH;
    }

    public function getName()
    {
        return AnimeHosterEnum::HOSTER_ANITUBE;
    }
}
