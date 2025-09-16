<?php

namespace Controllers;

class Welcome extends \Core\Controller{

    static public function dashboard(){

        static::render("Welcome.dashboard");
    }
}
