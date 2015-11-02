<?php

namespace LoopAnime\ApiBundle\Controller;

use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use LoopAnime\AppBundle\Entity\BaseEntity;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends FOSRestController
{

    protected function paginateObject(Request $request, EntityRepository $repository, $whereCriteria = [])
    {
        $curPage = $request->getRequestUri();
        $page = $request->get('page', '1');
        $maxr = $request->get('maxr', '10');
        $skip = ($page - 1) * $maxr;

        /** @var BaseEntity[] $objects */
        $objects = $repository->findBy($whereCriteria, null, $maxr, $skip);
        $totalObjects = count($repository->findAll());

        $response = ['payload' => []];
        foreach ($objects as $object) {
            if ($data = $object->serialize(null, true)) {
                $response['payload'][] = $data;
            } else {
                $response['payload'][] = $object;
            }
        }
        $response['pagination']['total'] = $totalObjects;
        $response['pagination']['hasNext'] = false;
        $response['pagination']['hasPrev'] = false;
        $response['pagination']['currentPage'] = $page;
        $response['pagination']['maxRecords'] = $maxr;
        if (!strpos($curPage, "page=")) {
            $curPage .= "&page=" . $page;
        }
        if (($skip + $maxr) < $totalObjects) {
            $response['pagination']['nextPage'] = preg_replace("/page=\\d+/i", "page=" . ($page + 1), $curPage);
            $response['pagination']['hasNext'] = true;
        }
        if ($skip > 0) {
            $response['pagination']['prevPage'] = preg_replace("/page=\\d+/i", "page=" . ($page - 1), $curPage);
            $response['pagination']['hasPrev'] = true;
        }
        return $response;
    }

}
