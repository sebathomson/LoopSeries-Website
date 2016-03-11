<?php
namespace LoopAnime\AppBundle\Crawler\Guesser;


use LoopAnime\AppBundle\Crawler\Enum\GuessTypesEnum;

abstract class AbstractGuesser implements GuesserInterface
{
    /** @var  array */
    protected $allAttempts;
    /** @var  array */
    protected $titles;
    /** @var  array */
    protected $bestMatch;
    /** @var string */
    protected $content;
    /** @var string */
    protected $domain;

    public function isExactMatch()
    {
        return $this->bestMatch['type'] === GuessTypesEnum::EXACT_MATCH;
    }

    public function getLog()
    {
        if ($this->bestMatch['type'] === GuessTypesEnum::NO_MATCH) {
            return 'NO MATCH';
        }

        return $this->bestMatch['match1'] . " === " . $this->bestMatch['match2'] . " (" . $this->bestMatch['percentage'] . ")";
    }

    /**
     * @param string $match1
     * @param string $match2
     * @param string $percentage
     * @param string $uri
     */
    protected function addAttempt($match1, $match2, $percentage, $uri) {
        $this->allAttempts[] = [
            'match1' => $match1,
            'match2' => $match2,
            'percentage' => $percentage,
            'uri' => $uri
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFullLog() {
        return $this->allAttempts;
    }

    /**
     * @return string|null
     */
    public function getUri()
    {
        if ($this->bestMatch['type'] === GuessTypesEnum::NO_MATCH) {
            return null;
        }
        return $this->bestMatch['uri'];
    }

    public function getCompPercentage()
    {
        return !empty($this->bestMatch['percentage']) ? $this->bestMatch['percentage'] : 0;
    }

}
