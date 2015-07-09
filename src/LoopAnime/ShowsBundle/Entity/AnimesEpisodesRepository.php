<?php

namespace LoopAnime\ShowsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * animes_episodesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnimesEpisodesRepository extends EntityRepository
{

    public function getMostViewsEpisodes($getResults = true)
    {
        $q = $this->createQueryBuilder('ae')
                ->where('ae.airDate <= CURRENT_TIMESTAMP()')
                ->orderBy('ae.views','DESC')
                ->addOrderBy('ae.views','DESC')
                ->getQuery();

        if($getResults) {
            return $q->getResult();
        } else {
            return $q;
        }
    }

    public function getMostRatedEpisodes($getResults = true)
    {
        $q = $this->createQueryBuilder('ae')
                ->select('ae')
                ->where('ae.airDate <= CURRENT_TIMESTAMP()')
                ->orderBy('ae.rating','DESC')
                ->addOrderBy('ae.ratingCount','DESC')
                ->addOrderBy('ae.ratingUp','DESC')
                ->getQuery();

        if($getResults) {
            return $q->getResult();
        } else {
            return $q;
        }
    }

    public function getUserHistoryEpisodes(Users $user, $getResults = true)
    {

        $userId = $user->getId();
        $q = $this->createQueryBuilder('ae')
            ->select('ae')
            ->join('ae.episodeViews','views')
            ->where('views.idUser = :idUser')
            ->andWhere('ae.airDate <= CURRENT_TIMESTAMP()')
            ->orderBy('views.viewTime','DESC')
            ->setParameter('idUser',$userId)
            ->getQuery();

        if($getResults)
            return $q->getResult();
        else
            return $q;
    }

    /**
     * @param $idAnime
     * @param bool $getResults
     * @param bool $episodeNumber
     * @return AnimesEpisodes|\Doctrine\ORM\Query
     */
    public function getEpisodesByAnime($idAnime, $getResults = true, $episodeNumber = false) {
        $query = $this->createQueryBuilder('ae')
            ->select('ae')
            ->addSelect('a.id')
            ->addSelect('a.title')
            ->addSelect('ase.season')
            ->join('ae.season','ase')
            ->join('ase.anime','a')
            ->where('a.id = :idAnime')
            ->setParameter('idAnime',$idAnime);
        if(!empty($episodeNumber)) {
            $query->andWhere('ae.episode = :numberEpisode')->setParameter('numberEpisode',$episodeNumber);
        }
        $query = $query->getQuery();
        if($getResults)
            return $query->getResult();
        else
            return $query;
    }

    /**
     * @param $idSeason
     * @param bool $getResults
     * @param bool $episodeNumber
     * @return array|\Doctrine\ORM\Query
     */
    public function getEpisodesBySeason($idSeason, $getResults = true, $episodeNumber = false) {
        $query = $this->createQueryBuilder('ae')
                    ->select('ae')
                    ->addSelect('ase.season')
                    ->join("ae.season","ase")
                    ->where("ase.id = :idSeason")
                    ->setParameter('idSeason',$idSeason);
        if($episodeNumber !== false && !empty($episodeNumber)) {
            $query->andWhere("ae.episode = :numEpisode")->setParameter('numEpisode',$episodeNumber);
        }
        if($getResults)
            return $query->getQuery()->getResult();
        else
            return $query->getQuery();
    }

    /**
     * @param $idEpisode
     * @param bool $nextEpisode
     * @throws \Exception
     * @return array|\Doctrine\ORM\Query|null
     */
    public function getNavigateEpisode($idEpisode, $nextEpisode = true) {

        /** @var AnimesEpisodes $episode */
        $episode = $this->find($idEpisode);
        if(!$episode) {
            throw new NotFoundResourceException('Episode was not found');
        }

        /** @var AnimesSeasons $season */
        $season = $this->_em->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons')->find($episode->getSeason());

        if($nextEpisode) {
            $lookUpEpisode = $episode->getEpisode() + 1;
            $lookUpSeason = $season->getSeason() + 1;
        } else {
            $lookUpEpisode = $episode->getEpisode() - 1;
            $lookUpSeason = $season->getSeason() - 1;
        }

        // Try find the next episode by episode number
        $query = $this->createQueryBuilder('ae')
                ->select('ae')
                ->addSelect('ase.season')
                ->join('ae.season','ase')
                ->where('ae.episode = :episodeNumber')
                ->andWhere('ae.season = :idSeason')
                ->setParameter('episodeNumber',$lookUpEpisode)
                ->setParameter('idSeason',$episode->getSeason())
                ->getQuery();

        $result = $query->getOneOrNullResult();
        if($result) {
            return $result;
        }

        // Try to find the prev episode by changing the season
        /** @var Animes $idAnime */
        $idAnime = $season->getAnime();

        $LookSeason = $this->_em->getRepository('LoopAnimeShowsBundle:AnimesSeasons')->findOneBy(['season' => $lookUpSeason, 'anime' => $idAnime->getId()]);

        if($LookSeason) {

            $query = $this->createQueryBuilder('ae')
                    ->select('ae')
                    ->addSelect('ase.season')
                    ->join('ae.season','ase')
                    ->where('ae.season = :idSeason')
                    ->setParameter('idSeason',$LookSeason->getId());

            if($nextEpisode) {
                $query->orderBy('ae.episode','ASC');
            } else {
                $query->orderBy('ae.episode','DESC');
            }
            $result = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
            if($result) {
                return $result;
            }
        }

        return null;
    }

    public function getRecentEpisodes($getResults = true)
    {
        $q = $this->createQueryBuilder('ae')
                    ->select('ae')
                    ->join('ae.links','l')
                    ->where('ae.airDate <= CURRENT_TIMESTAMP()')
                    ->andWhere('l.id IS NOT NULL')
                    ->orderBy('ae.airDate','DESC')
                    ->getQuery();
        if($getResults) {
            return $q->getResult();
        } else {
            return $q;
        }
    }

    public function getUserRecentsEpisodes(Users $user, $getResults = true) {

        $userId = $user->getId();
        $userPreferences = $user->getPreferences();
        $order = "ASC";
        if($userPreferences !== null) {
            $order = $userPreferences->getTrackEpisodesSort();
        }

        $q = $this->createQueryBuilder('ae')
            ->select('ae')
            ->join('ae.season','ase')
            ->join('ase.anime','a')
            ->join('a.userFavorites','uf')
            ->leftjoin('ae.episodeViews','views')
            ->where('uf.idUser = :idUser')
            ->andWhere('(views.id IS NULL OR views.completed = 0)')
            ->andWhere('ae.airDate <= CURRENT_TIMESTAMP()')
            ->orderBy('ase.season',$order)
            ->addOrderBy('ae.episode',$order)
            ->setParameter('idUser',$userId)
            ->getQuery();

        if($getResults)
            return $q->getResult();
        else
            return $q;
    }

    public function getUserFutureEpisodes(Users $user, $getResults = true)
    {
        $userId = $user->getId();
        $q = $this->createQueryBuilder('ae')
            ->select('ae')
            ->join('ae.season','ase')
            ->join('ase.anime','a')
            ->join('a.userFavorites','uf')
            ->where('uf.idUser = :idUser')
            ->andWhere('ae.airDate > CURRENT_TIMESTAMP()')
            ->orderBy('ae.airDate','ASC')
            ->setParameter('idUser',$userId);

        if($user->getPreferences() !== null) {
            if($user->getPreferences()->getFutureListSpecials())
                $q->andWhere('ase.season > 0');
        }

        $q = $q->getQuery();

        if($getResults)
            return $q->getResult();
        else
            return $q;
    }

    /**
     * @param Animes $anime
     * @return mixed
     */
    public function getTotEpisodes(Animes $anime)
    {
        $query = $this->createQueryBuilder("ae")
                ->select("COUNT(ae)")
                ->join('ae.season','ase')
                ->join('ase.anime','a')
                ->where("a.id = :idAnime")
                ->setParameter("idAnime", $anime->getId())
                ->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getEpisodes2Update($idAnime, HosterInterface $hoster, $all = false)
    {
        $query = $this->createQueryBuilder('ae')
                ->select('ae')
                ->join('ae.season','ase')
                ->join('ase.anime','a')
                ->leftJoin('LoopAnime\ShowsBundle\Entity\AnimesLinks','el',"WITH","(el.hoster = :hoster AND el.idEpisode = ae.id)")
                ->where('ase.season > 0')
                ->andWhere('ae.airDate <= CURRENT_TIMESTAMP()')
                ->setParameter('hoster',$hoster->getName())
                ->groupBy('ae.id')
            ;
        if(!empty($idAnime)) {
            $query->andWhere('a.id = :idAnime')->setParameter('idAnime',$idAnime);
        }
        if(!$all) {
            $query->andWhere('el.id IS NULL');
        }
        return $query->getQuery()->getResult();
    }

    public function getEpisodesByAirDate(\DateTime $airDate, $hoster = null, $idAnime = null, $all = false)
    {
        $query = $this->createQueryBuilder('ae')
            ->select('ae')
            ->join('ae.season','ase')
            ->join('ase.anime','a')
            ->where('ase.season > 0')
            ->andWhere('ae.airDate = :airDate')
            ->setParameter('airDate', $airDate->format('Y-m-d'))
            ->groupBy('ae.id');

        if ($hoster && !$all) {
            $query
                ->leftJoin('LoopAnime\ShowsBundle\Entity\AnimesLinks','el',"WITH","(el.hoster = :hoster AND el.idEpisode = ae.id)")
                ->setParameter('hoster', $hoster)
                ->andWhere('el.id IS NULL')
                ;
        }
        if ($idAnime) {
            $query->andWhere('a.id = :idAnime')->setParameter('idAnime',$idAnime);
        }

        return $query->getQuery()->execute();
    }

    public function setRatingOnEpisode(AnimesEpisodes $episode, $ratingUp)
    {
        $session = new Session();
        $checksArr = $session->get('checks');

        if (isset($checksArr['rating']))
            $checkRatings = $checksArr['rating'];
        else
            $checkRatings = array();

        // Check if there is a rate already
        if (isset($checkRatings[$episode->getId()])) {
            // Change of heart - Up to Down
            if ($checkRatings[$episode->getId()] == "up" && !$ratingUp) {
                $episode->setRatingUp($episode->getRatingUp() - 1);
                $episode->setRatingDown($episode->getRatingDown() + 1);
            } elseif ($checkRatings[$episode->getId()] == "down" && $ratingUp) {
                $episode->setRatingUp($episode->getRatingUp() + 1);
                $episode->setRatingDown($episode->getRatingDown() - 1);
            }
        } else {
            $episode->setRatingCount($episode->getRatingCount() + 1);
            if ($ratingUp)
                $episode->setRatingUp($episode->getRatingUp() + 1);
            else
                $episode->setRatingDown($episode->getRatingDown() + 1);
        }
        $this->_em->persist($episode);
        $this->_em->flush($episode);

        // Sets on Session what pick he choose
        $checksArr['rating'][$episode->getId()] = ($ratingUp ? "up" : "down");
        $session->set('checks',$checksArr);
        return true;
    }

    /**
     * @param string $title
     * @param string $orderKey
     * @param string $order
     * @param bool $getQuery
     * @return array|\Doctrine\ORM\Query|Animes|null
     */
    public function getEpisodesByTitle($title, $orderKey = "episodeTitle", $order = "ASC", $getQuery = true)
    {
        $query = $this->createQueryBuilder("ae")
            ->select("ae")
            ->where('ae.episodeTitle LIKE :title')
            ->orderBy("ae.".$orderKey, $order)
            ->setParameter("title", ''.$title.'%')
            ->getQuery();

        if($getQuery) {
            return $query;
        } else {
            return $query->getResult();
        }
    }

    public function getEpisodesByDate(\DateTime $date, $incSpecial = true)
    {
        $query = $this->createQueryBuilder('ae')
                    ->select('ae')
                    ->addSelect('a.title')
                    ->where('ae.airDate = :airDate')
                    ->join('ae.season','ase')
                    ->join('ase.anime','a')
                    ->setParameter('airDate',$date);
        if (!$incSpecial) {
            $query->andWhere('ase.season > 0');
        }

        return $query->getQuery()->getResult();
    }

    public function getLatestEpisodes(Animes $anime, $maxr = 10)
    {
        $query = $this->createQueryBuilder('ae')
                    ->select('ae')
                    ->join('ae.season','ase')
                    ->where('ase.anime = :idAnime')
                    ->andWhere('ae.airDate <= CURRENT_TIMESTAMP()')
                    ->addOrderBy('ae.id','DESC')
                    ->setParameter('idAnime',$anime->getId())
                    ->setMaxResults($maxr)
                    ->getQuery();
        return $query->getResult();
    }

    public function incrementView(AnimesEpisodes $episode)
    {
        $episode->setViews($episode->getViews() + 1);
        $this->_em->persist($episode);
        $this->_em->flush();
    }
}
