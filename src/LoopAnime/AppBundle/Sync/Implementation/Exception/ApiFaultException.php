<?php
/**
 * Created by PhpStorm.
 * User: luislopes
 * Date: 26/01/2015
 * Time: 21:45
 */

namespace LoopAnime\AppBundle\Sync\Implementation\Exception;


class ApiFaultException extends \Exception {

    public function __construct($api, $error)
    {
        parent::__construct(sprintf('API %s has returned an error %s',$api,$error));
    }

}