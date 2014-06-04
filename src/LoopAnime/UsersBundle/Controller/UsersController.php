<?php
/**
 * Created by PhpStorm.
 * User: joshlopes
 * Date: 28/05/2014
 * Time: 19:30
 */

namespace LoopAnime\UsersBundle\Controller;


use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller {

    public function setPreferences(Users $user, Request $request) {

        // Togle Show Specials
        if($request->get("showSpecials")) {

        }

    }

} 