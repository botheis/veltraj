<?php

/**
 * Call all the mecanics in private scope
 */


/**
 * AUTOLOADERS
 */
require_once(BASENAME."/include/autoload.php");

$composer = BASENAME."/vendor/autoload.php";
if(is_file($composer)){
    require_once($composer);
}
unset($composer);


/**
 * INITIALIZATIONS
*/
$request = \Core\Request::getInstance();
$config = [];
require_once(BASENAME."/include/config.php");

$session = \Core\Session::getInstance();
$session->start();

// Get the routemodes
if(!empty($config["system"]["routemodes"])){
    $routemodes = explode(',', $config["system"]["routemodes"]);
}
else{
    // Or use web by default
    $routemodes = ["web"];
}
$output = "";

/**
 * LOAD THE ROUTES
 */

// Load the routes for each modes defined in the configuration
foreach($routemodes as $mode){
    // Get the methods for each specific modes
    if(!empty($config[$mode]["methods"])){
        $configmethods = $config[$mode]["methods"];
    }
    else{
        // By default we define the mode 'web', and by default its methods are GET and POST
        $configmethods = "GET,POST";
    }
    $configmethods = explode(",", $configmethods);

    // Get the params types allowed for each specific modes
    if(!empty($config[$mode]["paramtypes"])){
        $paramtypes = $config[$mode]["paramtypes"];
    }
    else{
        $paramtypes = "str";
    }
    $paramtypes = explode(",", $paramtypes);
    // Load the route file corresponding to the mode
    $filename = BASENAME.'/include/routes/'.$mode.'.php';
    if(is_file($filename)){
        require_once($filename);
    }
}
// Cleanup to be sure no old datas are accessible
unset($mode);
unset($configmethods);
unset($routemodes);
unset($paramtypes);
unset($filename);


// Test the route
$result = \Core\Route::test($request->method(), $request->uri());
if($result != NULL){
    $route = $result["route"];
    $params = $result["params"];
    // Optionnal : preprocess before all calls (specific to each modes)
    $preprocessfile = BASENAME.'/include/preprocess/'.$route->getMode().'.php';
    if(is_file($preprocessfile)){
        require_once($preprocessfile);

        foreach($preprocess as $process){
            $process->execute();
        }
        unset($preprocess);
        unset($process);
    }
    unset($preprocessfile);


    // Execute the route callable
    $route->call($params);
    
    // Optionnal : postprocess after all calls (specific to each modes)
    $postprocessfile = BASENAME.'/include/postprocess/'.$route->getMode().'.php';
    if(is_file($postprocessfile)){
        require_once($postprocessfile);
        
        if(count($postprocess) != 0){
            ob_start();
            $output = ob_get_contents();
            foreach($postprocess as $process){
                $process->execute();
            }
            ob_end_clean();
            unset($postprocess);
            unset($process);
        }
    }
    unset($postprocessfile);
}
else{
    http_response_code(404);
    $output = "Page not found";
}

echo $output;