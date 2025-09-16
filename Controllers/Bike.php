<?php

namespace Controllers;

class Bike extends \Core\Controller{

    static public function list(){

        static::render("Bikes.list");
    }
}
