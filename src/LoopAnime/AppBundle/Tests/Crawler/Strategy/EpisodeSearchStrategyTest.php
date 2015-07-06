<?php
namespace LoopAnime\AppBundle\Tests\Crawler\Strategy;


use LoopAnime\AppBundle\Crawler\Hoster\Anime44Hoster;
use LoopAnime\AppBundle\Crawler\Hoster\AnitubeHoster;
use LoopAnime\AppBundle\Crawler\Strategy\EpisodeSearchStrategy;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EpisodeSearchStrategyTest extends KernelTestCase
{

    protected $crawlerRepo;

    public function setUp()
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * @test
     */
    public function should_be_able_to_execute()
    {
        $anime = $this->prophesize(Animes::class);
        $anime->getTitle()->willReturn('Naruto Shippuden');
        $anime->getId()->willReturn(1);
        $anime->reveal();

        $season = $this->prophesize(AnimesSeasons::class);
        $season->getAnime()->willReturn($anime);
        $season->getSeason()->willReturn(1);
        $season = $season->reveal();

        $episode = $this->prophesize(AnimesEpisodes::class);
        $episode->getSeason()->willReturn($season);
        $episode->getEpisode()->willReturn(1);
        $episode->reveal();
        $hoster = $this->prophesize(AnitubeHoster::class);

        $episodeStrategy = static::$kernel->getContainer()->get('crawler.strategy.episode');
        $uri = $episodeStrategy->execute($episode, $hoster);
        $this->assertEquals('', $uri);
    }

}
