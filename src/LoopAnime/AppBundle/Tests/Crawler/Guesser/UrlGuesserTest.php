<?php

namespace LoopAnime\AppBundle\Tests\Crawler\Guesser;

use LoopAnime\AppBundle\Crawler\Guesser\UrlGuesser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UrlGuesserTest extends KernelTestCase
{

    /**
     * @test
     */
    public function should_return_matchs()
    {
        $content = "asdasd asda dasd ad asd asdsad asdasd <a href='http://www.some.com/'>aasdasd</a> asdasd:
<a href='http://www.some.com/episode1'>episode 1</a>
<a href='http://www.some.com/episode2'>episode 2</a>
<a href='http://www.some.com/episode3'>episode 3</a>
<a href='http://www.some.com/episode4'>episode 4</a>
<a href='http://www.some.com/episode5'>episode 5</a>";
        $guesser = new UrlGuesser($content, ['episode1']);
        $guesser->guess();
        $this->assertEquals(true, $guesser->isExactMatch());
        $this->assertEquals('http://www.some.com/episode1', $guesser->getUri());
        $this->assertEquals('episode1 === episode1 (100)', $guesser->getLog());
    }

    /**
     * @test
     */
    public function should_not_be_extact_match_when_not_found()
    {
        $content = "asdasd asda dasd ad asd asdsad asdasd <a href='http://www.some.com/'>aasdasd</a> asdasd:
<a href='http://www.some.com/episode1'>episode 1</a>
<a href='http://www.some.com/episode2'>episode 2</a>
<a href='http://www.some.com/episode3'>episode 3</a>
<a href='http://www.some.com/episode4'>episode 4</a>
<a href='http://www.some.com/episode5'>episode 5</a>";
        $guesser = new UrlGuesser($content, ['episode8']);
        $guesser->guess();
        $this->assertEquals(false, $guesser->isExactMatch());
    }

    /**
     * @test
     */
    public function should_be_able_to_parse_special_chars()
    {
        $content = "asdasd asda dasd ad asd asdsad asdasd <a href='http://www.some.com/'>aasdasd</a> asdasd:
<a href='http://www.some.com/episode-very-special'>episode 1</a>
<a href='http://www.some.com/episode2'>episode 2</a>
<a href='http://www.some.com/episode3'>episode 3</a>
<a href='http://www.some.com/episode4'>episode 4</a>
<a href='http://www.some.com/episode5'>episode 5</a>";
        $guesser = new UrlGuesser($content, ['Episode% very% Special']);
        $guesser->guess();
        $this->assertEquals(true, $guesser->isExactMatch());
    }

}
