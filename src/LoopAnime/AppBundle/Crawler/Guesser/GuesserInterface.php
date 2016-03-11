<?php
namespace LoopAnime\AppBundle\Crawler\Guesser;


interface GuesserInterface
{

    /**
     * @return array|null
     */
    public function getFullLog();

    public function guess();

    public function isExactMatch();

    public function getLog();

    public function getUri();

    public function getCompPercentage();
}
