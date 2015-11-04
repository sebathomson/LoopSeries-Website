<?php
namespace LoopAnime\ApiBundle\Response;

use JMS\Serializer\Annotation\Type;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use LoopAnime\ShowsBundle\Services\VideoService;

class AnimesLinksResponse
{

    /**
     * @Type("integer")
     */
    private $id;

    /**
     * @Type("integer")
     */
    private $idEpisode;

    /**
     * @Type("string")
     */
    private $hoster;

    /**
     * @Type("string")
     */
    private $link;

    /**
     * @Type("integer")
     */
    private $status;

    /**
     * @Type("integer")
     */
    private $idUser;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @Type("integer")
     */
    private $subtitles;

    /**
     * @Type("string")
     */
    private $lang;

    /**
     * @Type("string")
     */
    private $subLang;

    /**
     * @Type("integer")
     */
    private $used;

    /**
     * @Type("string")
     */
    private $fileType;

    /**
     * @Type("integer")
     */
    private $report;

    /**
     * @Type("string")
     */
    private $qualityType;

    /**
     * @Type("string")
     */
    private $fileSize;

    /**
     * @Type("string")
     */
    private $fileServer;

    /**
     * @Type("integer")
     */
    private $usedTimes;

    /**
     * @Type("array")
     */
    private $directLink;

    public function __construct(VideoService $videoService, AnimesLinks $link = null)
    {
        if (null !== $link) {
            $this->id = $link->getId();
            $this->idEpisode = $link->getIdEpisode();
            $this->link = $link->getLink();
            $this->hoster = $link->getHoster();
            $this->status = $link->getStatus();
            $this->createTime = $link->getCreateTime(true);
            $this->idUser = $link->getIdUser();
            $this->lang = $link->getLang();
            $this->subtitles = $link->getSubtitles();
            $this->subLang = $link->getSubLang();
            $this->usedTimes = $link->getUsedTimes();
            $this->used = $link->getUsed();
            $this->fileType = $link->getFileType();
            $this->report = $link->getReport();
            $this->qualityType = $link->getQualityType();
            $this->fileSize = $link->getFileSize();
            $this->fileServer = $link->getFileServer();
            $this->usedTimes = $link->getUsedTimes();
            
            $directLinks = $videoService->getDirectVideoLink($link);
            $this->directLink = !empty($directLinks) ? $directLinks : null;
        }
    }

    public static function create(VideoService $videoService, AnimesLinks $link = null)
    {
        return new AnimesLinksResponse($videoService, $link);
    }
    
}
