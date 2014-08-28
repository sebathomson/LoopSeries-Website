<?php

namespace LoopAnime\AdminBundle\Controller;

use LoopAnime\AdminBundle\Form\Type\AddNewAnimeType;
use LoopAnime\AdminBundle\Form\Type\CrawlEpisodesType;
use LoopAnime\CrawlersBundle\Services\crawlers\CrawlerService;
use LoopAnime\CrawlersBundle\Services\hosters\Anime44;
use LoopAnime\CrawlersBundle\Services\hosters\Anitube;
use LoopAnime\ShowsAPIBundle\Entity\AnimesAPI;
use LoopAnime\ShowsAPIBundle\Entity\AnimesAPIRepository;
use LoopAnime\ShowsAPIBundle\Services\Apis\TheTVBD;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use LoopAnime\ShowsBundle\Entity\AnimesSeasonsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{

    public function addAnimeAction(Request $request)
    {
        $form = $this->createForm(new AddNewAnimeType($this->getDoctrine()->getManager()));
        $form->submit($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $theTVDb = new TheTVBD($this->getDoctrine()->getManager());
            $animeInformation = $theTVDb->getAnimeInformation($data['tvdb_id']);
            $idAnime = 0;

            /** @var AnimesAPIRepository $animesApiRepo */
            $animesApiRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsAPIBundle\Entity\AnimesAPI');
            $anime = $animesApiRepo->findOneBy(['apiAnimeKey' => $data['tvdb_id']]);
            if ($anime === null) {
                echo "Anime doesn't exists, creating new anime!";

            } else {
                echo "Anime already exists. Updating anime key " . $anime->getIdAnime() . "!";
                $idAnime = $anime->getIdAnime();
            }

            $idAnime = $this->insAnime($animeInformation['geral'], $idAnime);
            $this->insSeason($idAnime, $animeInformation['seasons']);

            // Insert into the Assocation Collection
            $animeAPI = new AnimesAPI();
            $animeAPI->setApiAnimeKey($data['tvdb_id']);
            $animeAPI->setIdAnime($idAnime);
            $animeAPI->setIdApi(2);
            $this->getDoctrine()->getManager()->persist($animeAPI);
            $this->getDoctrine()->getManager()->flush($animeAPI);
        }

        return $this->render('LoopAnimeAdminBundle:admin:addAnime.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Insert a new Anime Head Information
     * @param array $animeHeadArr
     * @param int $idAnime Number of anime or empty to create new one
     * @return int ID of the last inserted id table animes
     */
    private function insAnime($animeHeadArr, $idAnime = 0)
    {

        $fields = $values = "";
        $notUpdKeys = ["ratingCount, ratingUp, ratingDown, rating"];
        $updateString = "last_updated=NOW(), ";

        foreach ($animeHeadArr as $key => $value) {
            $value = addslashes($value);
            $fields .= "$key,";
            $values .= "'" . $value . "',";

            if (!in_array($key, $notUpdKeys) and $value != "")
                $updateString .= "$key='$value',";
        }

        $fields = trim($fields, " ,");
        $values = trim($values, " ,");
        $updateString = trim($updateString, " ,");

        $date_now = date("Y-m-d h:m:s");

        $db = $this->getDoctrine()->getConnection();
        if (!empty($idAnime)) {
            $query = "UPDATE animes SET $updateString WHERE id_anime = '$idAnime'";
            $stmp = $db->prepare($query);
            $stmp->execute();
            return $idAnime;
        } else {
            $query = "INSERT INTO animes ($fields, create_time) VALUES ($values, '$date_now')";
            $stmp = $db->prepare($query);
            $stmp->execute();
            return $db->lastInsertId();
        }
    }

    /**
     * Insert Season information
     * @param integer $idAnime
     * @param array $seasonsArr seasson information with episodes information
     * @param object $api in use
     */
    private function insSeason($idAnime, $seasonsArr)
    {

        foreach ($seasonsArr as $key => $season) {

            if (isset($season['poster']))
                $poster = $season['poster'];
            else
                $poster = "";

            $date_now = date("Y-m-d H:i:s");
            $db = $this->getDoctrine()->getConnection();
            /** @var AnimesSeasonsRepository $seasonsRepo */
            $seasonsRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');
            /** @var AnimesSeasons $season */
            $seasonObj = $seasonsRepo->findOneBy(['idAnime' => $idAnime, 'season' => $key]);
            if ($seasonObj !== null) {
                $idSeason = $seasonObj->getId();

                if ($poster != "")
                    $upd_poster = ", poster = '$poster'";
                else
                    $upd_poster = "";

                $query = "UPDATE animes_seasons SET number_episodes = '" . count($season['episodes']) . "' $upd_poster WHERE id_season = '$idSeason'";
                $stmp = $db->prepare($query);
                $stmp->execute();
            } else {
                $query = "INSERT INTO animes_seasons (id_anime, season, number_episodes, season_title, poster, create_time) VALUES ('$idAnime','$key','" . count($season['episodes']) . "','','$poster', '$date_now')";
                $stmp = $db->prepare($query);
                $stmp->execute();
                $idSeason = $db->lastInsertId();
            }
            foreach ($season['episodes'] as $epKey => $episodeArr) {
                $this->insEpisode($episodeArr, $idSeason);
            }
        }
    }

    /**
     * Insert Episode information
     * @param array $episodeArr
     * @param integer $idSeason
     * @return integer Last ID of Episode inserted
     */
    private function insEpisode($episodeArr, $idSeason)
    {
        $fields = "id_season,";
        $values = "'$idSeason',";
        $notUpdKeys = ["ratingCount, ratingUp, ratingDown, rating"];
        $updateString = "last_updated=NOW(), ";

        foreach ($episodeArr as $key => $value) {
            $value = addslashes($value);
            $fields .= "$key,";
            $values .= "'" . $value . "',";

            if (!in_array($key, $notUpdKeys) and $value != "")
                $updateString .= "$key='$value', ";
        }

        $fields = trim($fields, " ,");
        $values = trim($values, " ,");
        $updateString = trim($updateString, " ,");

        $date_now = date("Y-m-d H:i:s");
        $db = $this->getDoctrine()->getConnection();
        /** @var AnimesEpisodesRepository $aEpisodesRepo */
        $aEpisodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
        /** @var AnimesEpisodes $episode */
        $episode = $aEpisodesRepo->findOneBy(['idSeason' => $idSeason, 'episode' => $episodeArr['episode']]);
        if ($episode !== null) {
            $query = "UPDATE animes_episodes SET $updateString WHERE id_episode = '" . $episode->getId() . "'";
            $stmp = $db->prepare($query);
            $stmp->execute();
            return $episode->getId();
        } else {
            $query = "INSERT INTO animes_episodes ($fields, create_time) VALUES ($values, '$date_now')";
            $stmp = $db->prepare($query);
            $stmp->execute();
            return $db->lastInsertId();
        }
    }

    public function populateLinksAction(Request $request)
    {
        set_time_limit(0); ob_start();
        $form = $this->createForm(new CrawlEpisodesType($this->getDoctrine()->getManager()));
        $form->submit($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $hoster = strtolower($data['hoster']);
            $anime = $data['anime'];
            $all = $data['all'];
            /** @var Animes $animeObj */
            $animeObj = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\Animes')->find($anime);
            /** @var AnimesEpisodesRepository $aEpisodesRepo */
            $aEpisodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
            /** @var AnimesEpisodes[] $episodes */
            $episodes = $aEpisodesRepo->getEpisodes2Update($anime, $hoster, $all);

            switch ($hoster) {
                case "anitube":
                    $hoster = new Anitube();
                    break;
                case "anime44":
                    $hoster = new Anime44();
                    break;
                default:
                    throw new \Exception("I dont have the hoster $hoster");
                    break;
            }

            $crawler = new CrawlerService($animeObj, $hoster, $this->getDoctrine()->getManager());

            foreach ($episodes as $episode) {
                $bestMatchs = $crawler->crawlEpisode($episode);
                var_dump($bestMatchs);
                if(($bestMatchs['percentage'] == "100") and count($bestMatchs['mirrors']) > 0) {
                    foreach ($bestMatchs['mirrors'] as $mirror) {
                        $url = parse_url($mirror);
                        $link = New AnimesLinks();
                        $link->setIdEpisode($episode->getId());
                        $link->setHoster($hoster->getName());
                        $link->setLink($mirror);
                        $link->setStatus(1);
                        $link->setIdUser(0);
                        $sublang = $hoster->getSubtitles();
                        $link->setLang("JAP");
                        $link->setSubtitles((!empty($sublang) ? 1 : 0));
                        $link->setSubLang($sublang);
                        $link->setFileType("mp4");
                        $link->setCreateTime(new \DateTime("now"));
                        $link->setUsed(0);
                        $link->setUsedTimes(0);
                        $link->setReport(0);
                        $link->setQualityType('SQ');
                        $link->setFileServer($url['host']);
                        $link->setFileSize("0");
                        $this->getDoctrine()->getManager()->persist($link);
                        $this->getDoctrine()->getManager()->flush();
                    }
                }
                ob_flush();
            }
        }
        $form = $form->createView();
        return $this->render('LoopAnimeAdminBundle:admin:crawl4Episodes.html.twig', ['form' => $form]);
    }


}