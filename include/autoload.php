<?php
/**
 * Call all Objects from the project and 
 */
spl_autoload_register(function($call){
    $call = trim($call);
    $call = str_replace("\\", "/", $call);

    $filename = BASENAME."/".$call.".php";

    
    if(is_file($filename)){
        require_once($filename);
    }
});

?>