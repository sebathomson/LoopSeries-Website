<?php

namespace LoopAnime\ShowsAPIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnimesAPI
 *
 * @ORM\Table("animes_api")
 * @ORM\Entity(repositoryClass="LoopAnime\ShowsAPIBundle\Entity\AnimesAPIRepository")
 */
class AnimesAPI
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_anime_api", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_anime", type="integer")
     * @ORM\ManyToMany(targetEntity="LoopAnime\ShowsBundle\Entity\Animes")
     */
    private $idAnime;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_api", type="integer")
     * @ORM\ManyToMany(targetEntity="LoopAnime\ShowsAPIBundle\Entity\APIS", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $idApi;

    /**
     * @var string
     *
     * @ORM\Column(name="api_anime_key", type="string", length=100)
     */
    private $apiAnimeKey;


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
     * Set idAnime
     *
     * @param integer $idAnime
     * @return Animes_API
     */
    public function setIdAnime($idAnime)
    {
        $this->idAnime = $idAnime;

        return $this;
    }

    /**
     * Get idAnime
     *
     * @return integer 
     */
    public function getIdAnime()
    {
        return $this->idAnime;
    }

    /**
     * Set idApi
     *
     * @param integer $idApi
     * @return Animes_API
     */
    public function setIdApi($idApi)
    {
        $this->idApi = $idApi;

        return $this;
    }

    /**
     * Get idApi
     *
     * @return integer 
     */
    public function getIdApi()
    {
        return $this->idApi;
    }

    /**
     * Set apiAnimeKey
     *
     * @param string $apiAnimeKey
     * @return Animes_API
     */
    public function setApiAnimeKey($apiAnimeKey)
    {
        $this->apiAnimeKey = $apiAnimeKey;

        return $this;
    }

    /**
     * Get apiAnimeKey
     *
     * @return string 
     */
    public function getApiAnimeKey()
    {
        return $this->apiAnimeKey;
    }
}
