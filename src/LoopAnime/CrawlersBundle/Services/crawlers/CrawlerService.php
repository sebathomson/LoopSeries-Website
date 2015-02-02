<?php

namespace LoopAnime\CrawlersBundle\Services\crawlers;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\CrawlersBundle\Entity\AnimesCrawlers;
use LoopAnime\CrawlersBundle\Services\hosters\Hosters;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerService
{

    /** @var Hosters */
    private $hoster;
    /** @var Animes */
    private $anime;
    /** @var AnimesEpisodes */
    private $episode;
    private $episodesListUrl;
    /** @var AnimesCrawlers */
    private $crawlerSettings = false;
    private $possibleTitleMatchs;
    private $possibleEpisodesMatchs;
    private $bestMatch;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
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
        $this->createTitleMatchers();
        $this->episodesListUrl = $this->getEpisodesListUrl();
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
        if ($this->getCrawlSettings() !== null)
            $titles = explode(",", $this->getCrawlSettings()->getTitleAdapted());
        $this->possibleTitleMatchs[] = $this->cleanTitle($this->anime->getTitle());
        if (!empty($titles)) {
            foreach ($titles as $title) {
                $this->possibleTitleMatchs[] = $this->cleanTitle($title);
            }
        }

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
            return $this->crawlAnimeSearchs4EpisodesList();
        } else {
            return $this->hoster->getEpisodesSearchLink();
        }
    }

    /**
     * Crawls the Search for Animes List for the right Anime with all Episodes
     * Usually websites that have Animes >> Episodes for e.g: Naruto Shippudden >> Naruto Shippuden 20
     */
    private function crawlAnimeSearchs4EpisodesList()
    {
        $search_term = $this->anime->getTitle();
        $search_term = urlencode($search_term);
        //$search_term = str_replace("+", "%20", $search_term);
        $search_link = str_replace("{search_term}", $search_term, $this->hoster->getAnimesSearchLink());
        $grabedMatchs = $this->crawlWebpage($search_link);
        $bestMatch = $this->matchMaker($grabedMatchs, $this->possibleTitleMatchs);
        return $bestMatch['uri'];
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
        if ($this->getCrawlSettings() !== null)
            $episodesTitles = explode(",", $this->getCrawlSettings()->getEpisodeAdapted());

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
