<?php

namespace LoopAnime\CrawlersBundle\Services\crawlers;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\CrawlersBundle\Entity\AnimeCrawlerSeasonSettings;
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
    /** @var AnimeCrawlerSeasonSettings|null */
    private $seasonSettings;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function setConsoleOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function crawlEpisode(Animes $anime, Hosters $hoster, AnimesEpisodes $episode)
    {
        $this->anime = $anime;
        $this->hoster = $hoster;
        $this->episode = $episode;
        $this->resetInstance();
        if ($this->getCrawlSettings()) {
            $season = $this->episode->getSeason()->getSeason();
            $this->seasonSettings = $this->getCrawlSettings()->getMinimalSeasonSettings($season);
            if ($this->seasonSettings) {
                var_dump($this->seasonSettings->toArray());
            }
        }
        $this->createTitleMatchers();
        $this->output('Possible Title Matchers: ' . implode(", ", $this->possibleTitleMatchs));
        $this->episodesListUrl = $this->getEpisodesListUrl();
        $this->output('<comment>Episodes List: '.$this->episodesListUrl.'</comment>');
        $this->createEpisodeMatchers();
        $this->output('Possible Episode Titles Matchers: ' . implode(", ", $this->possibleEpisodesMatchs));

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

    private function createTitleMatchers()
    {
        if ($this->getCrawlSettings() !== null) {
            if (!empty($this->seasonSettings) && !empty($this->seasonSettings->getAnimeTitle())) {
                return $this->possibleTitleMatchs = [$this->cleanTitle($this->seasonSettings->getAnimeTitle())];
            }
        }
        $this->possibleTitleMatchs[] = $this->cleanTitle($this->anime->getTitle());
        $this->possibleTitleMatchs = array_unique($this->possibleTitleMatchs);
    }

    private function createEpisodeMatchers()
    {
        if ($this->getCrawlSettings() !== null) {
            $absoluteNumber = $this->episode->getAbsoluteNumber();
            if (!empty($this->seasonSettings)) {
                if ($this->seasonSettings->getReset() && $this->seasonSettings->getSeason() === $this->episode->getSeason()->getSeason()) {
                    $absoluteNumber = $this->episode->getEpisode();
                }
                if (!empty($this->seasonSettings->getHandicap())) {
                    $absoluteNumber += $this->seasonSettings->getHandicap();
                }
                if (!empty($this->seasonSettings->getEpisodeTitle())) {
                    $this->possibleEpisodesMatchs[] = $this->cleanEpisode($this->seasonSettings->getEpisodeTitle() . " " . $absoluteNumber);
                }
                if (!empty($this->seasonSettings->getAnimeTitle())) {
                    $this->possibleEpisodesMatchs[] = $this->cleanEpisode($this->seasonSettings->getAnimeTitle() . " " . $absoluteNumber);
                }
            }
        }

        $this->possibleEpisodesMatchs[] = $this->cleanEpisode($this->anime->getTitle() . " " . $this->episode->getAbsoluteNumber());
        $this->possibleEpisodesMatchs = array_unique($this->possibleEpisodesMatchs);
    }

    private function getEpisodesListUrl()
    {
        if ($this->hoster->isNeededLook4Anime()) {
            $secondGuess = $firstGuess = $this->crawlAnimeSearchs4EpisodesList($this->anime->getTitle());
            if (!empty($this->seasonSettings)) {
                $secondGuess = $this->crawlAnimeSearchs4EpisodesList($this->seasonSettings->getAnimeTitle());
            }
            if (round($secondGuess['percentage']) >= round($firstGuess['percentage'])) {
                return $secondGuess['uri'];
            }
            return $firstGuess['uri'];
        } else {
            return $this->hoster->getEpisodesSearchLink();
        }
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

    private function cleanElement($element)
    {

        // Check if splited by hiphen or underscore
        if (substr_count($element, "-") < substr_count($element, "_"))
            $basename = trim(str_replace("_", " ", $element));
        else
            $basename = trim(str_replace("-", " ", $element));

        return $basename;
    }

    private function cleanEpisode($episode)
    {
        $basename = $this->cleanElement($episode);
        $episodeCleanTags = ['EPISODE', 'FINAL', 'SEASON', '(FINAL)'];
        if ($this->getCrawlSettings() !== null)
            $episodeCleanTags = array_unique(array_merge($episodeCleanTags, explode(",", $this->getCrawlSettings()->getEpisodeClean())));

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

    private function crawlAnimeSearchs4EpisodesList($title)
    {
        $search_term = urlencode($title);
        $search_link = str_replace("{search_term}", $search_term, $this->hoster->getAnimesSearchLink());
        $this->output('Search Link: ' . $search_link);

        $grabedMatchs = $this->crawlWebpage($search_link);
        foreach($grabedMatchs as &$match) {
            $match['text'] = $this->cleanTitle($match['text']);
        }

        $bestMatch = $this->matchMaker($grabedMatchs, $this->possibleTitleMatchs);
        return $bestMatch;
    }

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

    private function getCrawlSettings()
    {
        if($this->crawlerSettings === false) {
            $crawlerRepo = $this->em->getRepository('LoopAnime\CrawlersBundle\Entity\AnimesCrawlers');
            $this->crawlerSettings = $crawlerRepo->findOneBy(['anime' => $this->anime]);
        }
        return $this->crawlerSettings;
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
        unset($this->crawlerSettings);
        unset($this->seasonSettings);

        $this->hoster->resetInstance();
        $this->crawlerSettings = false;
    }

}
