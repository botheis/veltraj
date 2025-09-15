<?php

namespace Middlewares;

class APIContentType extends \Core\Middleware{

    /**
     * Apply a content-type header for each page calling this middleware
     */
    static public function handler(){
        header("Content-type: Application/json");
    }
}