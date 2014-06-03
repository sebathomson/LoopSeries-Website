<?php

namespace LoopAnime\ShowsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\FOSUserBundle;
use FOS\UserBundle\Model\User;

/**
 * animes_episodesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnimesEpisodesRepository extends EntityRepository
{

    public function getMostViewsEpisodes()
    {
        $where_clause = "animes_episodes.air_date <= NOW()";
        $order_by = "views DESC";
        if(true)
            $limit = "20";
        else
            $limit = "12";
        return $this->_em->createQuery("SELECT * FROM animes_episodes WHERE $where_clause ORDER BY $order_by LIMIT $limit")
            ->getResult();
    }

    public function getMostRatedEpisodes()
    {
        $where_clause = "animes_episodes.air_date <= NOW()";
        $order_by = "rating DESC, ratingCount DESC, ratingUp DESC";
        if(true)
            $limit = "20";
        else
            $limit = "12";
        return $this->_em->createQuery("SELECT * FROM animes_episodes WHERE $where_clause ORDER BY $order_by LIMIT $limit")
            ->getResult();
    }

    public function getUserHistoryEpisodes(User $user)
    {
        $where_clause = "animes_episodes.air_date <= NOW()";
        $order_by = "views.view_time DESC";

        if(!$user->getId())
            throw new \Exception("I shouldnt be here without a user");

        $query = "SELECT
					  views.completed,
					  views.current_time,
					  animes_episodes.id_episoqde,
					  animes_episodes.episode,
					  animes_episodes.episode_title,
					  animes_episodes.poster,
					  animes_episodes.rating,
					  animes_episodes.views,
					  animes_episodes.ratingCount
					FROM views
					  JOIN animes_episodes USING (id_episode)
					WHERE views.id_user = '".$user->getId()."' AND $where_clause
					ORDER BY $order_by";

        return $this->_em->createQuery("$query")
            ->getResult();
    }

    /**
     * @param $idAnime
     * @param bool $getResults
     * @return array|\Doctrine\ORM\Query
     */
    public function getEpisodesByAnime($idAnime, $getResults = true) {
        $query = "SELECT ae
                FROM
                    LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae
                    JOIN ae.animesSeasons ase
                    JOIN ase.animes a
                WHERE
                    a.id = '".$idAnime."'";
        if($getResults)
            return $this->_em->createQuery($query)->getResult();
        else
            return $this->_em->createQuery($query);
    }

    /**
     * @param $idSeason
     * @param bool $getResults
     * @return array|\Doctrine\ORM\Query
     */
    public function getEpisodesBySeason($idSeason, $getResults = true) {
        $query = "SELECT ae, ase.season
                FROM
                    LoopAnime\ShowsBundle\Entity\AnimesEpisodes ae
                    JOIN ae.animesSeasons ase
                WHERE
                    ae.season = '".$idSeason."'";
        if($getResults)
            return $this->_em->createQuery($query)->getResult();
        else
            return $this->_em->createQuery($query);
    }

    /*public function getUserFutureEpisodes(User $user)
    {

        $user->getPreferences();

    }
$user_obj = new Users($_SESSION["user_info"]);

if($user_obj->getUserPreference("future_list_specials") == "0")
$where_clause .= " AND animes_seasons.season > 0";

$query = "SELECT
					animes_episodes.*
					FROM
						users_favorites
						JOIN animes USING(id_anime)
						JOIN animes_seasons USING(id_anime)
						JOIN animes_episodes USING(id_season)
					WHERE
						users_favorites.id_user = '".$id_user."'
						AND animes_episodes.air_date > NOW()
						AND $where_clause";
		case "future_episodes":
			$show_info = true;
			$order_by = "";
			if(!$user_obj->getIsLogged()) {
                include("templates/login_required.php");
                exit;
            }

			$episodes = $anime_obj->getUserFutureEpisodes( $user_obj->getUserInfo("id_user"), "animes_episodes.air_date > NOW()",  "0", "12", $order_by );
			break;
		case "to_see":
			$show_info = true;
			if($user_obj->getUserPreference("track_episodes_sort") == "")
                $order = "DESC";
            else
                $order = strtoupper($user_obj->getUserPreference("track_episodes_sort"));

			$order_by = "animes_seasons.season $order, animes_episodes.episode $order";
			if(!$user_obj->getIsLogged()) {
                include("templates/login_required.php");
                exit;
            }

			$where_clause .= "  AND (views.id_view IS NULL OR views.completed = 0)";

			$episodes = $anime_obj->getUser2SeeEpisodes( $user_obj->getUserInfo("id_user"), $where_clause, "0", "12", $order_by );

			break;
		default:
			$order_by = "";
			break;
    }*/

}
