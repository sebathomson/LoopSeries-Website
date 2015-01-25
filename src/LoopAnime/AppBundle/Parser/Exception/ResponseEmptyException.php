<?php
/**
 * Created by PhpStorm.
 * User: luislopes
 * Date: 23/01/2015
 * Time: 15:15
 */

namespace LoopAnime\AppBundle\Parser\Exception;


class ResponseEmptyException extends \Exception {

    protected $message = "Response is empty";

}