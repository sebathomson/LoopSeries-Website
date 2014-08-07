<?php

namespace LoopAnime\CrawlersBundle\Services;

use LoopAnime\CrawlersBundle\Entity\AnimesCrawlers;
use LoopAnime\CrawlersBundle\Services\hosters\Hosters;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerService extends Controller
{

    private $hoster;
    private $anime;
    /** @var AnimesEpisodes */
    private $episode;
    private $episodesListUrl;
    /** @var AnimesCrawlers */
    private $crawlerSettings;
    private $possibleTitleMatchs;
    private $possibleEpisodesMatchs;
    private $bestMatch;

    /**
     * Crawls the Search for Animes List for the right Anime with all Episodes
     * Usually websites that have Animes >> Episodes for e.g: Naruto Shippudden >> Naruto Shippuden 20
     */
    public function crawlAnimeSearchs4EpisodesList()
    {
        $search_term = $this->anime->getTitle();
        $search_term = urlencode($search_term);
        //$search_term = str_replace("+", "%20", $search_term);
        $search_link = str_replace("{search_term}", $search_term, $this->hoster->getAnimesSearchLink());

        $crawler = new Crawler();
        $crawler->addHtmlContent(file_get_contents($search_link));
        $grabedMatchs = [];
        $crawler->filter("a")->each(function (Crawler $node, $i) {
            $uri = $node->link()->getUri();
            $grabedMatchs[] = ["uri" => $uri, "text" => ""];
        });

        $bestMatch = $this->matchMaker($grabedMatchs, $this->possibleTitleMatchs);
        return $bestMatch['uri'];
    }

    /**
     * Crawls the Search for Animes List for the right Anime with all Episodes
     * Usually websites that have Animes >> Episodes for e.g: Naruto Shippudden >> Naruto Shippuden 20
     * @param $urlEpisodesLists
     * @return array
     */
    public function crawlEpisodesList($link)
    {
        $bestMatch['percentage'] = 0;

        if ($this->hoster->isPaginated()) {
            while ($linkCrawl = $this->hoster->getNextPage($link) && $bestMatch['percentage'] < 100) {
                $grabedMatchs = $this->crawlWebpage($linkCrawl);
                $bestMatch = $this->matchMaker($grabedMatchs, $this->possibleEpisodesMatchs);
            }
        } else {
            $grabedMatchs = $this->crawlWebpage($link);
            $bestMatch = $this->matchMaker($grabedMatchs, $this->possibleEpisodesMatchs);
        }

        return $bestMatch;
    }

    public function crawlWebpage($link)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent(file_get_contents($link));
        $grabedMatchs = [];
        $crawler->filter("a")->each(function (Crawler $node, $i) {
            $uri = $node->link()->getUri();
            $grabedMatchs[] = ["uri" => $uri, "text" => ""];
        });
        return $grabedMatchs;
    }

    public function __construct(Animes $anime, Hosters $hoster)
    {
        $this->hoster = $hoster;
        $this->anime = $anime;
        $this->episodesListUrl = $this->getEpisodesListUrl();
        $this->createTitleMatchers();
    }

    public function getAbsoluteNumber()
    {
        $absNumber = $this->episode->getAbsoluteNumber();
        $seasonsAsNew = $this->crawlerSettings->getSeasonsAsNew();

        // Check if this season is a new Anime in the hoster
        if (!empty($seasonsAsNew)) {
            $seasonAsNew = explode(",", $seasonsAsNew);
            foreach ($seasonAsNew as $season) {
                if ($season === $this->episode->getSeason()->getSeason()) {
                    $seasonsRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\AnimesSeasons');
                    $lastSeason = $seasonsRepo->findBy(['season' => ($season - 1), 'idAnime' => $this->anime->getId()]);
                    $episodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\AnimesEpisodes');
                    $lastEpisode = $episodesRepo->findBy(['idSeason' => $lastSeason->getId()], ['id' => "DESC"], 1);

                    $absNumber = $absNumber - $lastEpisode->getAbsoluteNumber();
                }
            }
        }
        return $absNumber;
    }

    /**
     * @return $this
     */
    public function getCrawlSettings()
    {
        $crawlerRepo = $this->getDoctrine()->getRepository('LoopAnime\CrawlersBundle\Entity\AnimesCrawlers');
        $this->crawlerSettings = $crawlerRepo->findBy(['idAnime' => $this->anime->getId()]);
        return $this;
    }

    /**
     * @param AnimesEpisodes $episode
     * @return array
     */
    public function crawlEpisode(AnimesEpisodes $episode)
    {
        $this->episode = $episode;
        $this->createEpisodeMatchers();

        $bestMatchs = [];
        // If there is a direct search by episode name -- run for all possible matchs a search
        if(strpos("{search_term}",$this->episodesListUrl) !== false) {
            foreach ($this->possibleEpisodesMatchs as $term) {
                // Get Link Ready to the crawl
                $link = str_replace("{search_term}", $term, $this->episodesListUrl);
                $bestMatchs = $this->crawlEpisodesList($this->episodesListUrl);
                if($bestMatchs['percentage'] === 100)
                    break;
            }
        } else {
            $bestMatchs = $this->crawlEpisodesList($this->episodesListUrl);
        }
        return $bestMatchs;
    }

    public function getEpisodesListUrl()
    {
        if ($this->hoster->isNeededLook4Anime()) {
            return $this->crawlAnimeSearchs4EpisodesList();
        } else {
            return $this->hoster->getEpisodesSearchLink();
        }
    }

    public function createTitleMatchers()
    {
        $titles = explode(",", $this->crawlerSettings->getTitleAdapted());
        $this->possibleTitleMatchs[] = $this->cleanTitle($this->anime->getTitle());
        if (!empty($titles)) {
            foreach ($titles as $title) {
                $this->possibleTitleMatchs[] = $this->cleanTitle($title);
            }
        }
    }

    public function createEpisodeMatchers()
    {
        if (empty($this->episode)) {
            throw new \Exception("Please provide an episode to the object by using crawlEpisode method");
        }
        $episodesTitles = explode(",", $this->crawlerSettings->getEpisodeAdapted());
        $this->possibleEpisodesMatchs[] = $this->cleanEpisode($this->anime->getTitle() . " " . $this->episode->getAbsoluteNumber());
        foreach ($this->possibleTitleMatchs as $possibleTitleMatch) {
            $this->possibleEpisodesMatchs[] = $this->cleanEpisode($possibleTitleMatch . " " . $this->episode->getAbsoluteNumber());
        }
        if (!empty($episodesTitles)) {
            foreach ($episodesTitles as $title) {
                $this->possibleEpisodesMatchs[] = $this->cleanEpisode($title . " " . $this->episode->getAbsoluteNumber());
            }
        }
    }

    public function cleanTitle($title)
    {
        $basename = $this->cleanElement($title);

        // Replace multiple whitespaces by just one
        $basename = preg_replace('/\s+/', ' ', $basename);

        $basename = explode(" ", $basename);
        $basename = array_unique($basename);

        return strtoupper(implode("", $basename));
    }

    public function cleanEpisode($episode)
    {
        $basename = $this->cleanElement($episode);
        $episodeCleanTags = explode(",", $this->crawlerSettings->getEpisodeClean());

        // Clean Rules on animes_crawlers->clean_episodes
        if (!empty($episodeCleanTags))
            foreach ($episodeCleanTags as $tag)
                $basename = trim(str_replace($tag, "", $basename));

        // Replace multiple whitespaces by just one
        $basename = preg_replace('/\s+/', ' ', $basename);

        $basename = explode(" ", $basename);
        $basename = array_unique($basename);
        $last_number = ltrim(array_pop($basename), "0");
        $before_last = ltrim(array_pop($basename), "0");

        // Probably they put a username follow the number attempt to clean the username
        if (!is_numeric($last_number) and is_numeric($before_last)) {
            $last_number = $before_last;
        } else {
            if (is_numeric($before_last))
                $last_number = $before_last . "-" . $last_number;
            else
                $last_number = $before_last . $last_number;
        }

        $basename[] = $last_number;
        return strtoupper(implode("", $basename));
    }

    /**
     * Cleans an element removing it whitespaces and duplicated words returning a simple string without it all
     * @param string $element
     * @return string
     */
    private function cleanElement($element)
    {

        // Check if splited by hiphen or underscore
        if (substr_count($element, "-") < substr_count($element, "_"))
            $basename = trim(str_replace("_", " ", $element));
        else
            $basename = trim(str_replace("-", " ", $element));

        return $basename;
    }

    /**
     * Does a Match between all possible titles and the titles which we got
     * @param array $grabedMatchers
     * @param array $possibleMatchers
     * @internal param array $possible_matchs All titles parsed on DOM @see crawling_video_links
     * @return array
     */
    protected function matchMaker(array $grabedMatchers, array $possibleMatchers)
    {
        foreach ($possibleMatchers as $match1) {
            $percentage = $bestPercentage = 0;
            foreach ($grabedMatchers as $match2) {
                $match2 = $match2["text"];
                similar_text($match1, $match2, $percentage);
                if ($bestPercentage < $percentage) {
                    $bestPercentage = $percentage;
                    $this->bestMatch = [
                        'uri' => $match2["uri"],
                        'log' => $match1 . "  ===== " . $match2 . " ====> " . $percentage,
                        'percentage' => $percentage,
                        'match1' => $match1,
                        'match2' => $match2
                    ];
                }
                // If we found a perfect match than stop
                if ($percentage == 100)
                    break(2);
            }
        }
        return $this->bestMatch;
    }

}