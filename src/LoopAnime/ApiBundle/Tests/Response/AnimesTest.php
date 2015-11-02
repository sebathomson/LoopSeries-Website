<?php
namespace LoopAnime\ApiBundle\Tests\Response;

use LoopAnime\ShowsBundle\Entity\Animes;

class AnimesTest extends \PHPUnit_Framework_TestCase
{

    private function generateAnime()
    {
        $anime = new Animes();
        $anime->setLastUpdated(1445953472);
        return $anime;
    }

    public function testResponse()
    {
        $anime = $this->generateAnime();
        $data = $anime->serialize();
        $this->assertEquals('{"title":"TBA","poster":"","genres":"TBA","themes":"TBA","plot_summary":"TBA","running_time":"30","start_time":"TBA","end_time":"TBA","status":"continuing","rating":0,"imdb_id":"0","rating_count":0,"rating_up":0,"rating_down":0,"last_updated":1445953472,"last_update":{},"create_time":{},"type_series":"anime","big_poster":"","animes_seasons":[]}', $data);
    }

}
