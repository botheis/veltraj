<?php

namespace Core;

use Exception;

class Middleware{
    protected $_params;

    public function __construct(...$params){
        $this->_params = $params;
    }

    public function execute(){
        try{
            call_user_func_array([static::class, "handler"], $this->_params);
        }
        catch(\TypeError $e){
            
        }
    }
};

?>