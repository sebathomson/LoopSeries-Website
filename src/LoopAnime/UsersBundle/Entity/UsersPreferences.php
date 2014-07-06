<?php

namespace LoopAnime\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users_Preferences
 *
 * @ORM\Table("users_preferences")
 * @ORM\Entity(repositoryClass="LoopAnime\UsersBundle\Entity\UsersPreferencesRepository")
 */
class UsersPreferences
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer")
     * @ORM\Id
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
     * @var Users
     *
     * ORM\OneToOne(targetEntity="LoopAnime\UsersBundle\Entity\Users")
     * ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     */
    //protected $users;


    /**
     * Set fullScreen
     *
     * @param integer $fullScreen
     * @return UsersPreferences
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
     * @return UsersPreferences
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
     * @return UsersPreferences
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
     * @return UsersPreferences
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
     * @return UsersPreferences
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
     * @return UsersPreferences
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
     * @return UsersPreferences
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
     * @return UsersPreferences
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
     * @return UsersPreferences
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
     * @param $iduser
     * @internal param int $id_user
     */
    public function setIdUser(Users $iduser)
    {
        $this->iduser = $iduser;
    }

}
