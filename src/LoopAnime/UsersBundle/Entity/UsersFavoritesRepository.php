<?php

namespace LoopAnime\UsersBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Users_FavoritesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsersFavoritesRepository extends EntityRepository
{
    public function getAnimeFavorite($idAnime, $idUser)
    {
        $query = "SELECT id_anime FROM users_favorites WHERE id_anime = '$idAnime' AND id_user = '$idUser'";

        return $this->_em->createQuery($query)->getOneOrNullResult();

    }
}
