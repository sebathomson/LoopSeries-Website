<?php

namespace LoopAnime\CrawlersBundle\Services\crawlers;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\CrawlersBundle\Entity\AnimesCrawlers;
use LoopAnime\CrawlersBundle\Services\hosters\Hosters;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerService
{

    /** @var Hosters */
    private $hoster;
    /** @var Animes */
    private $anime;
    /** @var AnimesEpisodes */
    private $episode;
    /** @var AnimesCrawlers */
    private $crawlerSettings = false;
    private $possibleTitleMatchs;
    private $possibleEpisodesMatchs;
    private $bestMatch;
    private $episodesListUrl;
    /** @var OutputInterface */
    private $output;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function setConsoleOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function output($message)
    {
        if ($this->output) {
            $this->output->writeln($message);
        }
    }

    public function resetInstance()
    {
        unset($this->bestMatch);
        unset($this->possibleEpisodesMatchs);
        unset($this->possibleTitleMatchs);
        $this->hoster->resetInstance();
        $this->crawlerSettings = false;
    }

    /**
     * @param Animes $anime
     * @param Hosters $hoster
     * @param AnimesEpisodes $episode
     * @return array
     * @throws \Exception
     */
    public function crawlEpisode(Animes $anime, Hosters $hoster, AnimesEpisodes $episode)
    {
        $this->anime = $anime;
        $this->hoster = $hoster;
        $this->episode = $episode;
        $this->resetInstance();
        $this->createTitleMatchers();
        $this->output('Possible Title Matchers: ' . implode(", ", $this->possibleTitleMatchs));
        $this->episodesListUrl = $this->getEpisodesListUrl();
        $this->output('<comment>Episodes List: '.$this->episodesListUrl.'</comment>');
        $this->createEpisodeMatchers();

        $bestMatchs = [];
        // If there is a direct search by episode name -- run for all possible matchs a search
        if (strpos("{search_term}", $this->episodesListUrl) !== false) {
            foreach ($this->possibleEpisodesMatchs as $term) {
                // Get Link Ready to the crawl
                $link = str_replace("{search_term}", $term, $this->episodesListUrl);
                $bestMatchs = $this->crawlEpisodesList($link);
                if ($bestMatchs['percentage'] === 100)
                    break;
            }
        } else {
            $bestMatchs = $this->crawlEpisodesList($this->episodesListUrl);
        }
        if($bestMatchs['percentage'] == "100") {
            $bestMatchs['mirrors'] = $this->crawlEpisodeVideos($bestMatchs);
        }
        return $bestMatchs;
    }

    /**
     * @return AnimesCrawlers
     */
    private function getCrawlSettings()
    {
        if($this->crawlerSettings === false) {
            $crawlerRepo = $this->em->getRepository('LoopAnime\CrawlersBundle\Entity\AnimesCrawlers');
            $this->crawlerSettings = $crawlerRepo->findOneBy(['idAnime' => $this->anime->getId()]);
        }
        return $this->crawlerSettings;
    }

    /**
     * @return string[]
     */
    private function createTitleMatchers()
    {
        $titles = [];
        if ($this->getCrawlSettings() !== null) {
            $titles = explode(",", $this->getCrawlSettings()->getTitleAdapted());

            //Season as new anime -- Grab settings
            if (!empty($this->getCrawlSettings()->getSeasonsAsNew())) {
                $seasonsAsNew = json_decode($this->getCrawlSettings()->getSeasonsAsNew(), true);
                foreach ($seasonsAsNew as $seasonAsNew) {
                    if ($seasonAsNew['season'] <= $this->episode->getSeason()->getSeason()) {
                        $titles = explode(",", $seasonAsNew['title']);
                    }
                }
            }
        }
        if (!empty($titles)) {
            foreach ($titles as $title) {
                $this->possibleTitleMatchs[] = $this->cleanTitle($title);
            }
        }
        $this->possibleTitleMatchs[] = $this->cleanTitle($this->anime->getTitle());
        return $this->possibleTitleMatchs;
    }

    private function cleanTitle($title)
    {
        $basename = $this->cleanElement($title);

        // Replace multiple whitespaces by just one
        $basename = preg_replace('/\s+/', ' ', $basename);

        $basename = explode(" ", $basename);
        $basename = array_unique($basename);

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

    private function getEpisodesListUrl()
    {
        if ($this->hoster->isNeededLook4Anime()) {
            $bestMatch = $this->crawlAnimeSearchs4EpisodesList($this->anime->getTitle());
            return $bestMatch['uri'];
        } else {
            return $this->hoster->getEpisodesSearchLink();
        }
    }

    /**
     * Crawls the Search for Animes List for the right Anime with all Episodes
     * Usually websites that have Animes >> Episodes for e.g: Naruto Shippudden >> Naruto Shippuden 20
     * @param $title
     * @param array $firstGuess
     * @return array
     */
    private function crawlAnimeSearchs4EpisodesList($title, $firstGuess = [])
    {
        $search_term = urlencode($title);
        //$search_term = str_replace("+", "%20", $search_term);
        $search_link = str_replace("{search_term}", $search_term, $this->hoster->getAnimesSearchLink());
        $this->output('Search Link: ' . $search_link);
        $grabedMatchs = $this->crawlWebpage($search_link);
        foreach($grabedMatchs as &$match) {
            $match['text'] = $this->cleanTitle($match['text']);
        }
        $bestMatch = $this->matchMaker($grabedMatchs, $this->possibleTitleMatchs);
        if (round($bestMatch['percentage']) !== 100 && !empty($this->getCrawlSettings()->getTitleAdapted()) && $title !== $this->getCrawlSettings()->getTitleAdapted()) {
            $this->crawlAnimeSearchs4EpisodesList($this->getCrawlSettings()->getTitleAdapted(), $bestMatch);
        }
        if (!empty($firstGuess) && round($firstGuess['percentage']) > round($bestMatch['percentage'])) {
            return $firstGuess;
        }
        return $bestMatch;
    }

    private function crawlWebpage($link)
    {
        $crawler = new Crawler(null, $link);
        $content = file_get_contents($link);
        $crawler->addHtmlContent($content, "UTF-8");
        $grabedMatchs = $crawler->filter("a")->each(function (Crawler $node) {
            $text = $this->cleanTitle($node->text());
            $uri = $node->link()->getUri();
            return ["uri" => $uri, "text" => $text];
        });
        return $grabedMatchs;
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
                $uri = $match2['uri'];
                $match2 = $match2["text"];
                similar_text($match1, $match2, $percentage);
                if ($bestPercentage < $percentage) {
                    $bestPercentage = $percentage;
                    $this->bestMatch = [
                        'uri' => $uri,
                        'log' => $match1 . "  ===== " . $match2 . " ====> " . $percentage,
                        'percentage' => $percentage,
                        'match1' => $match1,
                        'match2' => $match2
                    ];
                    //$this->output('Best Match: '. $match1 . " with possible matchs: ". $match2);
                }
                // If we found a perfect match than stop
                if ($percentage == 100)
                    break(2);
            }
        }
        return $this->bestMatch;
    }

    private function createEpisodeMatchers()
    {
        $episodesTitles = [];
        if ($this->getCrawlSettings() !== null) {
            // Episode Titles Adapted
            if (!empty($this->getCrawlSettings()->getEpisodeAdapted())) {
                $episodesTitles = explode(",", $this->getCrawlSettings()->getEpisodeAdapted());
            }

            // Seasons as New
            if (!empty($this->getCrawlSettings()->getSeasonsAsNew())) {
                $seasonsAsNew = json_decode($this->getCrawlSettings()->getSeasonsAsNew(), true);
                foreach ($seasonsAsNew as $seasonAsNew) {
                    if (!empty($seasonAsNew['season']) && $seasonAsNew['season'] <= $this->episode->getSeason()->getSeason()) {
                        $seasons = $this->anime->getAnimeSeasons();
                        $absolute = 0;
                        foreach ($seasons as $season) {
                            if ($season->getSeason() === $this->episode->getSeason()->getSeason()) {
                                $absolute += $this->episode->getEpisode();
                                break;
                            } elseif ($season->getSeason() >= $seasonAsNew['season']) {
                                $absolute += $season->getNumberEpisodes();
                            }
                        }
                        $this->output('<comment>Absolute number being used: '.$absolute.'</comment>');
                        $this->episode->setAbsoluteNumber($absolute);
                    }
                }
            }
        }

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

    private function cleanEpisode($episode)
    {
        $basename = $this->cleanElement($episode);
        $episodeCleanTags = ['EPISODE'];
        if ($this->getCrawlSettings() !== null)
            $episodeCleanTags = explode(",", $this->getCrawlSettings()->getEpisodeClean());

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
        if (!is_numeric($last_number) && is_numeric($before_last)) {
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
     * Crawls the Search for Animes List for the right Anime with all Episodes
     * Usually websites that have Animes >> Episodes for e.g: Naruto Shippudden >> Naruto Shippuden 20
     * @param $link
     * @throws \Exception
     * @return array
     */
    private function crawlEpisodesList($link)
    {
        $bestMatch['percentage'] = 0;

        if ($this->hoster->isPaginated()) {
            while (($linkCrawl = $this->hoster->getNextPage($link)) && $bestMatch['percentage'] < 100) {
                $grabedMatchs = $this->crawlWebpage($linkCrawl);
                foreach($grabedMatchs as &$match) {
                    $match['text'] = $this->cleanEpisode($match['text']);
                }
                $bestMatch = $this->matchMaker($grabedMatchs, $this->possibleEpisodesMatchs);
            }
        } else {
            $grabedMatchs = $this->crawlWebpage($link);
            $bestMatch = $this->matchMaker($grabedMatchs, $this->possibleEpisodesMatchs);
        }

        return $bestMatch;
    }

    private function crawlEpisodeVideos($bestMatchs)
    {
        if(empty($bestMatchs['uri']))
            throw new \Exception("URI of the best match is empty!");

        $crawler = new Crawler(null, $bestMatchs['uri']);
        $content = file_get_contents($bestMatchs['uri']);
        $crawler->addHtmlContent($content, "UTF-8");
        $filtered = $crawler->filter("iframe");
        $grabedMatchs = [];
        foreach($filtered as $iframe) {
            $src = $iframe->getAttribute("src");
            if((strpos($src,"embed") !== false || strpos($src,"mp4") !== false))
                $grabedMatchs[] = $src;
        };
        return $grabedMatchs;
    }

}
