<?php

namespace LoopAnime\ShowsAPIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APIS
 *
 * @ORM\Table("apis")
 * @ORM\Entity(repositoryClass="LoopAnime\ShowsAPIBundle\Entity\APISRepository")
 */
class APIS
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_api", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="api", type="string", length=255)
     */
    private $api;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=255)
     */
    private $apiKey;


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
     * Set api
     *
     * @param string $api
     * @return APIS
     */
    public function setApi($api)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * Get api
     *
     * @return string 
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     * @return APIS
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return array
     */
    public function convert2Array()
    {
        return [
            "id" => $this->getId(),
            "api" => $this->getApi(),
            "apiKey" => $this->getApiKey()
        ];
    }
}
