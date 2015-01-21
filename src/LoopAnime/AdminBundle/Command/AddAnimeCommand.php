<?php
/**
 * Created by PhpStorm.
 * User: luislopes
 * Date: 20/09/2014
 * Time: 20:54
 */

namespace LoopAnime\AdminBundle\Command;

use LoopAnime\ShowsAPIBundle\Entity\AnimesAPI;
use LoopAnime\ShowsAPIBundle\Entity\AnimesAPIRepository;
use LoopAnime\ShowsAPIBundle\Services\Apis\TheTVBD;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use LoopAnime\ShowsBundle\Entity\AnimesSeasonsRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddAnimeCommand extends ContainerAwareCommand {

    /** @var OutputInterface */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('admin:import:add-anime')
            ->setDescription('Adds one anime to the database')
            ->addArgument('tvdbId',null,InputArgument::REQUIRED,'TVDB ID',null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tvdbId = $input->getArgument('tvdbId');
        $this->output = $output;

        $doctrine = $this->getContainer()->get('doctrine');
        $theTVDb = new TheTVBD($doctrine->getManager());
        $animeInformation = $theTVDb->getAnimeInformation($tvdbId);
        $idAnime = 0;

        /** @var AnimesAPIRepository $animesApiRepo */
        $animesApiRepo = $doctrine->getRepository('LoopAnime\ShowsAPIBundle\Entity\AnimesAPI');
        $anime = $animesApiRepo->findOneBy(['apiAnimeKey' => $tvdbId]);
        if ($anime === null) {
            $output->writeln("<info>Anime doesn't exists, creating new anime!</info>");
        } else {
            $output->writeln("<warning>Anime already exists. Updating anime key " . $anime->getIdAnime() . "!</warning>");
            $idAnime = $anime->getIdAnime();
        }

        $idAnime = $this->insAnime($animeInformation['geral'], $idAnime);
        $this->insSeason($idAnime, $animeInformation['seasons']);

        // Insert into the Assocation Collection
        $animeAPI = new AnimesAPI();
        $animeAPI->setApiAnimeKey($tvdbId);
        $animeAPI->setIdAnime($idAnime);
        $animeAPI->setIdApi(2);
        $doctrine->getManager()->persist($animeAPI);
        $doctrine->getManager()->flush($animeAPI);
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

            if (!in_array($key, $notUpdKeys) && $value != "")
                $updateString .= "$key='$value',";
        }

        $fields = trim($fields, " ,");
        $values = trim($values, " ,");
        $updateString = trim($updateString, " ,");

        $date_now = date("Y-m-d h:m:s");

        $db = $this->getContainer()->get('doctrine')->getConnection();
        if (!empty($idAnime)) {
            $query = "UPDATE animes SET $updateString WHERE id_anime = '$idAnime'";
            $stmp = $db->prepare($query);
            $stmp->execute();

            $this->output->writeln("Anime with the ID $idAnime updated!");
            return $idAnime;
        } else {
            $query = "INSERT INTO animes ($fields, create_time) VALUES ($values, '$date_now')";
            $stmp = $db->prepare($query);
            $stmp->execute();

            $newAnimeId = $db->lastInsertId();
            $this->output->writeln("<success>New Anime with the ID: $newAnimeId inserted!</success>");
            return $newAnimeId;
        }


    }

    /**
     * Insert Season information
     * @param integer $idAnime
     * @param array $seasonsArr seasson information with episodes information
     */
    private function insSeason($idAnime, $seasonsArr)
    {
        $this->output->writeln("Inserting Seasons for the Anime $idAnime...");

        foreach ($seasonsArr as $key => $season) {

            if (isset($season['poster']))
                $poster = $season['poster'];
            else
                $poster = "";

            $date_now = date("Y-m-d H:i:s");
            $db = $this->getContainer()->get('doctrine')->getConnection();
            /** @var AnimesSeasonsRepository $seasonsRepo */
            $seasonsRepo = $this->getContainer()->get('doctrine')->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');
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

                $this->output->writeln("Season with the id $idSeason Updated!");
            } else {
                $query = "INSERT INTO animes_seasons (id_anime, season, number_episodes, season_title, poster, create_time) VALUES ('$idAnime','$key','" . count($season['episodes']) . "','','$poster', '$date_now')";
                $stmp = $db->prepare($query);
                $stmp->execute();
                $idSeason = $db->lastInsertId();

                $this->output->writeln("<success>New Season with the ID $idSeason Inserted!</success>");
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
        $this->output->writeln("Inserting Episodes for the Season $idSeason...");

        $fields = "id_season,";
        $values = "'$idSeason',";
        $notUpdKeys = ["ratingCount, ratingUp, ratingDown, rating"];
        $updateString = "last_updated=NOW(), ";

        foreach ($episodeArr as $key => $value) {
            $value = addslashes($value);
            $fields .= "$key,";
            $values .= "'" . $value . "',";

            if (!in_array($key, $notUpdKeys) && $value != "")
                $updateString .= "$key='$value', ";
        }

        $fields = trim($fields, " ,");
        $values = trim($values, " ,");
        $updateString = trim($updateString, " ,");

        $date_now = date("Y-m-d H:i:s");
        $db = $this->getContainer()->get('doctrine')->getConnection();
        /** @var AnimesEpisodesRepository $aEpisodesRepo */
        $aEpisodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
        /** @var AnimesEpisodes $episode */
        $episode = $aEpisodesRepo->findOneBy(['idSeason' => $idSeason, 'episode' => $episodeArr['episode']]);
        if ($episode !== null) {
            $query = "UPDATE animes_episodes SET $updateString WHERE id_episode = '" . $episode->getId() . "'";
            $stmp = $db->prepare($query);
            $stmp->execute();

            $this->output->writeln('Episode with the id ' . $episode->getId() . ' updated!');
            return $episode->getId();
        } else {
            $query = "INSERT INTO animes_episodes ($fields, create_time) VALUES ($values, '$date_now')";
            $stmp = $db->prepare($query);
            $stmp->execute();

            $this->output->writeln('<success>New Episode with the id ' . $episode->lastInsertId() . ' inserted!</success>');
            return $db->lastInsertId();
        }
    }

}