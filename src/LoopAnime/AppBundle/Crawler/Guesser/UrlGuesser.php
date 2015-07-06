<?php
namespace LoopAnime\AppBundle\Crawler\Guesser;


use LoopAnime\AppBundle\Crawler\Enum\GuessTypesEnum;
use LoopAnime\AppBundle\Utility\StringUtility;
use Symfony\Component\Form\Guess\Guess;

class UrlGuesser implements GuesserInterface
{

    private $bestMatch;

    public function __construct($content, $titles)
    {
        $this->content = $content;
        $this->titles = !is_array($titles) ? [$titles] : $titles;
        $this->bestMatch = ['type' => GuessTypesEnum::NO_MATCH, 'percentage' => 0];
    }

    public function guess()
    {
        $matches = [];
        preg_match_all("/[\"'](http.+\\/(.*?))[\"']/mi", $this->content, $matches);

        foreach ($this->titles as $title) {
            $match1 = StringUtility::cleanStringUrlMatcher($title);
            foreach ($matches[2] as $key => $match) {
                $match2 = StringUtility::cleanStringUrlMatcher($match);
                similar_text($match1, $match2, $percentage);
                if ($this->bestMatch['percentage'] < $percentage) {
                    $this->bestMatch = [
                        'type' => GuessTypesEnum::BEST_MATCH,
                        'match1' => $match1,
                        'match2' => $match2,
                        'percentage' => $percentage,
                        'uri' => $matches[1][$key]
                    ];
                }
                if ($match1 === $match2 || $percentage == 100) {
                    $this->bestMatch['type'] = GuessTypesEnum::EXACT_MATCH;
                    return $this->bestMatch;
                }
            }
        }
        return $this->bestMatch;
    }

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

    public function getUri()
    {
        if ($this->bestMatch['type'] === GuessTypesEnum::NO_MATCH) {
            return false;
        }
        return $this->bestMatch['uri'];
    }

}
