<?php

namespace LoopAnime\ShowsAPIBundle\Services\Apis;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use LoopAnime\ShowsAPIBundle\Entity\APIS;
use LoopAnime\ShowsAPIBundle\Entity\APISRepository;

class TheTVBD {

    /** @var APIS  */
    private $api;
    private $apiLinks;
    private $ApiMirrors;

    /**
     * Constructor
     * @param ObjectManager|EntityManager $em
     * @throws \Exception
     */
    public function __construct(ObjectManager $em) {

        /** @var APISRepository $apisRepo */
        $apisRepo = $em->getRepository('LoopAnime\ShowsAPIBundle\Entity\APIS');
        $api = $apisRepo->findBy(['api' => "TheTVBD"]);

        if(count($api) === 0)
            throw new \Exception("API does not exists");

        $this->api = array_pop($api);

        // API's links to the information and his keys
        $this->apiLinks = [
            'show_info'		    => "http://services.tvrage.com/myfeeds/showinfo.php?key={key}&sid={sid}",
            'full_show_info'	=> "{mirror}/api/{api_key}/series/{anime_key}/all/en.xml",
            'mirrors_info'		=> "http://thetvdb.com/api/{api_key}/mirrors.xml",
            'images_download'	=> "{mirror}/banners/{filename}"
        ];

        $this->ApiMirrors = [];
        $this->ApiMirrors[] = "http://thetvdb.com/";
    }

    /**
     * Returns the mirrors available
     * @see http://www.thetvdb.com/wiki/index.php?title=API:mirrors.xml
     * @return boolean
     */
    private function getMirrors() {
        echo $mirrors_link = $this->setLink($this->apiLinks["mirrors_info"], "");
        if($result = simplexml_load_file($mirrors_link)) {
            foreach($result->mirror as $mirror) {
                $mirrors_arr["id"] 			= (string)$mirror->id;
                $mirrors_arr["mirrorpath"] 	= (string)$mirror->mirrorpath;
                $mirrors_arr["typemask"] 	= (string)$mirror->typemask;
                $this->ApiMirrors[] = $mirrors_arr;
            }
            return true;
        } else
            return false;
    }

    /**
     * Download and returns a internal link to the image location
     * @param string $image String of image position on the api
     * @return string Internal Link to the image
     */
    private function downloadImage($image) {

        if($image == "")
            return "";

        // Construct the download link
        $download_link = $this->setLink($this->apiLinks["images_download"], "", $image);

        // Checks if the path already exists or create one
        $path = dirname(__FILE__) . "/../../../../../web/img/episodes/thetvdb/". $image;
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        // Output Path
        $output = $path;

        // Check if the image as been already downloaded, download it if it wasnt
        if(!file_exists($output))
            file_put_contents($output, file_get_contents($download_link));


        // Construct a valid address for the img
        $output = '/img/episodes/thetvdb/'. $image;

        return $output;
    }

    /**
     * Parse a link and build it with the correct keys to retrieve information
     * @param string $link
     * @param string $animeKey
     * @return string Builded Link to get contents from
     */
    private function setLink( $link, $animeKey, $filename = "") {
        $mirror 	= $this->ApiMirrors[0];
        $apiKey	    = $this->api->getApiKey();

        $link = str_replace( array( "{mirror}", "{api_key}" , "{anime_key}", "{filename}" ) , array( $mirror, $apiKey , $animeKey, $filename ) , $link );

        return (string) $link;
    }

    /**
     * @param $animeKey
     * @return array
     * @throws Exception
     */
    public function     getAnimeInformation( $animeKey ) {

        // Anime key to be identified on the API
        //$anime_key 			= $this->getAnimeApiKey($id_anime);
        $anime_information 	= array();

        // Try get the information about the anime
        if(!$page_content	 = file_get_contents($this->setLink($this->apiLinks['full_show_info'], $animeKey)))
            throw new Exception( "Unable to get information from the link.");

        if($page_content == "")
            throw new Exception( "Response is empty." );

        // Parse the information returned
        if(!($response_simple = simplexml_load_string($page_content,'SimpleXMLElement',LIBXML_NOCDATA)))
            throw new Exception( "Unable to parse the content returned." );

        $Show = $response_simple->Series;

        $anime_information['geral']['title'] 		= (string)$Show->SeriesName;
        $anime_information['geral']['poster']		= $this->downloadImage((string)$Show->poster);
        $anime_information['geral']['themes']		= "";
        $anime_information['geral']['plotSummary'] 	= (string)$Show->Overview;
        $anime_information['geral']['runningTime'] 	= (string)$Show->Runtime;
        $anime_information['geral']['startTime'] 	= (string)$Show->FirstAired;
        $anime_information['geral']['endTime'] 		= "";
        $anime_information['geral']['status']		= (string)$Show->Status;
        $anime_information['geral']['rating']		= (string)$Show->Rating;
        $anime_information['geral']['imdb_id']		= (string)$Show->IMDB_ID;
        $anime_information['geral']['ratingCount']	= (string)$Show->RatingCount;
        $anime_information['geral']['genres']		= trim(str_replace("|",",",(string)$Show->Genre)," ,");

        $anime_information['seasons'] = array();

        foreach($response_simple->Episode as $episode) {

            $season_no = (string)$episode->SeasonNumber;

            $ep = array();
            //$ep['id_season']	= "";
            $ep['episode'] 		= (string)$episode->EpisodeNumber;
            //$ep['season_number'] = (string)$episode->SeasonNumber;
            $ep['episode_title'] = (string)$episode->EpisodeName;
            $ep['poster'] 		= $this->downloadImage((string)$episode->filename);
            $ep['rating'] 		= (string)$episode->Rating;
            $ep['views'] 		= 0;
            $ep['comments'] 	= 0;
            $ep['air_date']		= (string)$episode->FirstAired;
            $ep['ratingCount']	= (string)$episode->RatingCount;
            $ep['summary']		= (string)$episode->Overview;
            $ep['imdb_id']		= (string)$episode->IMDB_ID;
            $ep['absolute_number'] = (string)$episode->absolute_number;

            $anime_information['seasons'][$season_no]['episodes'][] = $ep;

        }

        return $anime_information;
    }

}
