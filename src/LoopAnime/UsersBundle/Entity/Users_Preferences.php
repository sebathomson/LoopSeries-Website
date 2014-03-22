<?php

namespace LoopAnime\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users_Preferences
 *
 * @ORM\Table("users_preferences")
 * @ORM\Entity(repositoryClass="LoopAnime\UsersBundle\Entity\Users_PreferencesRepository")
 */
class Users_Preferences
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_preference", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer")
     * @ORM\OneToOne(targetEntity="LoopAnime\UsersBundle\Entity\Users", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $iduser;

    /**
     * @var integer
     *
     * @ORM\Column(name="full_screen", type="integer")
     */
    private $fullScreen;

    /**
     * @var integer
     *
     * @ORM\Column(name="public_profile", type="integer")
     */
    private $publicProfile;

    /**
     * @var integer
     *
     * @ORM\Column(name="share_lists", type="integer")
     */
    private $shareLists;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_videoq", type="string", length=10)
     */
    private $mobileVideoq;

    /**
     * @var string
     *
     * @ORM\Column(name="website_videoq", type="string", length=10)
     */
    private $websiteVideoq;

    /**
     * @var string
     *
     * @ORM\Column(name="mirrors_choice", type="string", length=20)
     */
    private $mirrorsChoice;

    /**
     * @var string
     *
     * @ORM\Column(name="mirrors_subtitles", type="string", length=20)
     */
    private $mirrorsSubtitles;

    /**
     * @var string
     *
     * @ORM\Column(name="automatic_track", type="string", length=20)
     */
    private $automaticTrack;

    /**
     * @var string
     *
     * @ORM\Column(name="track_episodes_sort", type="string", length=10)
     */
    private $trackEpisodesSort;

    /**
     * @var integer
     *
     * @ORM\Column(name="2_see_list_specials", type="integer")
     */
    private $toSeeListSpecials;

    /**
     * @var integer
     *
     * @ORM\Column(name="future_list_specials", type="integer")
     */
    private $futureListSpecials;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullScreen
     *
     * @param integer $fullScreen
     * @return Users_Preferences
     */
    public function setFullScreen($fullScreen)
    {
        $this->fullScreen = $fullScreen;

        return $this;
    }

    /**
     * Get fullScreen
     *
     * @return integer 
     */
    public function getFullScreen()
    {
        return $this->fullScreen;
    }

    /**
     * Set publicProfile
     *
     * @param integer $publicProfile
     * @return Users_Preferences
     */
    public function setPublicProfile($publicProfile)
    {
        $this->publicProfile = $publicProfile;

        return $this;
    }

    /**
     * Get publicProfile
     *
     * @return integer 
     */
    public function getPublicProfile()
    {
        return $this->publicProfile;
    }

    /**
     * Set shareLists
     *
     * @param integer $shareLists
     * @return Users_Preferences
     */
    public function setShareLists($shareLists)
    {
        $this->shareLists = $shareLists;

        return $this;
    }

    /**
     * Get shareLists
     *
     * @return integer 
     */
    public function getShareLists()
    {
        return $this->shareLists;
    }

    /**
     * Set mobileVideoq
     *
     * @param string $mobileVideoq
     * @return Users_Preferences
     */
    public function setMobileVideoq($mobileVideoq)
    {
        $this->mobileVideoq = $mobileVideoq;

        return $this;
    }

    /**
     * Get mobileVideoq
     *
     * @return string 
     */
    public function getMobileVideoq()
    {
        return $this->mobileVideoq;
    }

    /**
     * Set websiteVideoq
     *
     * @param string $websiteVideoq
     * @return Users_Preferences
     */
    public function setWebsiteVideoq($websiteVideoq)
    {
        $this->websiteVideoq = $websiteVideoq;

        return $this;
    }

    /**
     * Get websiteVideoq
     *
     * @return string 
     */
    public function getWebsiteVideoq()
    {
        return $this->websiteVideoq;
    }

    /**
     * Set mirrorsChoice
     *
     * @param string $mirrorsChoice
     * @return Users_Preferences
     */
    public function setMirrorsChoice($mirrorsChoice)
    {
        $this->mirrorsChoice = $mirrorsChoice;

        return $this;
    }

    /**
     * Get mirrorsChoice
     *
     * @return string 
     */
    public function getMirrorsChoice()
    {
        return $this->mirrorsChoice;
    }

    /**
     * Set mirrorsSubtitles
     *
     * @param string $mirrorsSubtitles
     * @return Users_Preferences
     */
    public function setMirrorsSubtitles($mirrorsSubtitles)
    {
        $this->mirrorsSubtitles = $mirrorsSubtitles;

        return $this;
    }

    /**
     * Get mirrorsSubtitles
     *
     * @return string 
     */
    public function getMirrorsSubtitles()
    {
        return $this->mirrorsSubtitles;
    }

    /**
     * Set automaticTrack
     *
     * @param string $automaticTrack
     * @return Users_Preferences
     */
    public function setAutomaticTrack($automaticTrack)
    {
        $this->automaticTrack = $automaticTrack;

        return $this;
    }

    /**
     * Get automaticTrack
     *
     * @return string 
     */
    public function getAutomaticTrack()
    {
        return $this->automaticTrack;
    }

    /**
     * Set trackEpisodesSort
     *
     * @param string $trackEpisodesSort
     * @return Users_Preferences
     */
    public function setTrackEpisodesSort($trackEpisodesSort)
    {
        $this->trackEpisodesSort = $trackEpisodesSort;

        return $this;
    }

    /**
     * Get trackEpisodesSort
     *
     * @return string 
     */
    public function getTrackEpisodesSort()
    {
        return $this->trackEpisodesSort;
    }

    /**
     * @return int
     */
    public function getToSeeListSpecials()
    {
        return $this->toSeeListSpecials;
    }

    /**
     * @param int $toSeeListSpecials
     */
    public function setToSeeListSpecials($toSeeListSpecials)
    {
        $this->toSeeListSpecials = $toSeeListSpecials;
    }

    /**
     * @return int
     */
    public function getFutureListSpecials()
    {
        return $this->futureListSpecials;
    }

    /**
     * @param int $futureListSpecials
     */
    public function setFutureListSpecials($futureListSpecials)
    {
        $this->futureListSpecials = $futureListSpecials;
    }

    /**
     * @return int
     */
    public function getIdUser()
    {
        return $this->iduser;
    }

    /**
     * @param int $id_user
     */
    public function setIdUser($iduser)
    {
        $this->iduser = $iduser;
    }

}
