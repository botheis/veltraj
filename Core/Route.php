<?php

namespace Core;

class Route{
    static private $_list = [];
    private $_modeMethods;
    private $_uri;
    private $_callable;
    private $_middlewares;
    private $_mode;
    private $_paramtypes;
    private $_description;


    /** Define a new Route
     * @param string|array $methods Method or list of methods allowed to use with this route. There are limitation through configuration. See mode in config.ini
     * @param string $uri The uri template to access to the ressource. It accepts :param as mandatory parameter, and ?param as optionnal parameter.
     * @param array|callable $callable is the function or method called through the route. Can be a class method, then use [\Path\to\MyController::class, "methodToCall"], or function reference
     * @param \Core\Middleware|array(default=[]) $middlewares take a middleware or a list of middlewares to call before calling $callable.
     */
    public function __construct(string|array $methods, string $uri, array|callable $callable, \Core\Middleware|array $middlewares=[]){
        global $mode;
        global $configmethods;
        global $paramtypes;

        $this->_mode = $mode;
        $this->_paramtypes = $paramtypes;

        // Store authorized methods into each route, depending on the mode
        $this->_modeMethods = $configmethods;

        $this->_uri = trim($uri, "/");
        $methods = (!is_array($methods)) ? [$methods] : $methods;
        $middlewares = (!is_array($middlewares)) ? [$middlewares] : $middlewares;

        $this->_callable = $callable;
        $this->_middlewares = $middlewares;
        $this->_description = "";
        // Setup the list for the current mode. If the mode doesn't exist on the list, add it with all the sub-arrays of methods allowed
        if(empty(static::$_list[$mode])){
            static::$_list[$mode] = [];
            foreach($this->_modeMethods as $authorizedMethod){
                static::$_list[$mode][$authorizedMethod] = [];
            }
        }

        // Add the route only if the configuration allows the method
        foreach($methods as $method){
            if(in_array($method, $this->_modeMethods)){
                static::$_list[$mode][$method][] = $this;
            }
        }
    }


    /**
     * Test the incomming method and uri and try to find a matching route.
     * @param string $method Corresponds to the request method.
     * @param string $uri Corresponds to the request uri.
     * @return array If a route matches with the incomming request.
     * @return NULL If no route has been found.
     */
    static public function test(string $method, string $uri): array|NULL{
        global $config;

        $selected = NULL;
        $matches = [];

        foreach(static::$_list as $mode=>$_method){
            // To be sure we quit this loop if a route has been found
            if($selected != NULL){
                break;
            }

            // In the current mode, there is no route matching with the method asked: we try with the next mode
            if(empty(static::$_list[$mode][$method])){
                continue;
            }

            // Found some routes for the current mode and the asked method
            foreach(static::$_list[$mode][$method] as $route){
                // If specified, get the paramter type
                $types = [];
                $regex = preg_replace_callback("#:([\w]+)-?([\w]+)?#", function($repl) use (&$types, $route){
                    $types[] = (!empty($repl[2]) && in_array($repl[2], $route->_paramtypes)) ? $repl[2] : "str";

                    return "([a-zA-Z0-9_\.]+)";
                }, $route->_uri);

                // If specified get the option type
                $regex = preg_replace_callback("#/\?([\w]+)-?([\w]+)?#", function($repl) use (&$types, $route){
                    $types[] = (!empty($repl[2]) && in_array($repl[2], $route->_paramtypes)) ? $repl[2] : "str";

                    return "/?([a-zA-Z0-9_\.]+)?";
                }, $regex);

                // Test the route
                preg_match("#^".$regex."$#", $uri, $matches);

                if($matches != []){
                    array_shift($matches);

                    // Cast the parameters with the proper type
                    $errorFlag = false;
                    for($i=0; $i<count($types); $i++){

                        // In a case of empty parameter but variable
                        if(!isset($matches[$i])){
                            $matches[] = NULL;
                            continue;
                        }

                        // If the cast is not possible, we have a route problem : wrong route
                        switch($types[$i]){
                            case "upperstr":
                                $matches[$i] = strtoupper(htmlentities($matches[$i]));
                                break;
                            case "lowerstr":

                                $matches[$i] = strtolower(htmlentities($matches[$i]));
                                break;
                            case "bool":
                                $matches[$i] = (strtolower($matches[$i]) == "false" || $matches[$i] == "0") ? false : true;
                                break;
                            case "num":
                                $test = $matches[$i] = $matches[$i] + 0;
                                // The param type doesn't match with the route : 
                                if(is_numeric($test)){
                                    $matches[$i] = $test;
                                }
                                else{
                                    $errorFlag = true;
                                }
                                break;
                            case "str":
                                $matches[$i] = htmlspecialchars($matches[$i]);
                                break;
                            default:
                                $matches[$i] = htmlspecialchars($matches[$i]);
                                break;
                        }
                    }
                    // If no problem occurs, select this route
                    if($errorFlag == false){
                        $selected = $route;
                        break;
                    }
                    else{
                        $selected = NULL;
                        $matches = [];
                    }
                }
            }
        }
        // We have (or not) the route
        return ($selected != NULL) ? ["route"=>$selected, "params"=>$matches] : NULL;
    }


    /**
     * Add prefix to a route uri
     * @param string $name The prefix to add. Allows uri params and options (:param and ?option)
     */
    public function prefix(string $name){
        $this->_uri = trim($name).'/'.$this->_uri;
    }


    /**
     * Add suffix to a route uri
     * @param string $name The prefix to add. Allows uri params and options (:param and ?option)
     */
    public function suffix(string $name){
        $this->_uri = $this->_uri.'/'.trim($name);
    }


    /**
     * Get the route description. The description is used for APIs
     * @return string The route description
     */
    public function getDescription():string{return $this->_description;}


    /**
     * Set the route description.
     * @param string $desc The new description
     */
    public function setDescription(string $desc){$this->_description = $desc;}


    /**
     * Get the list of routes corresponding to the specified mode. If there no mode is specified, returns all the routes.
     * @param string(default="") $mode
     */
    static public function getList(string $mode="") : array{
        if($mode == ""){
            return static::$_list;
        }

        if(!empty(static::$_list[$mode])){
            return static::$_list[$mode];
        }

        return [];
    }

    /**
     * Get the modes loaded.
     * @return array The list of loaded modes.
     */
    static public function getModes() : array{
        $keys = array_keys(static::$_list);
        return $keys;
    }

    /**
     * Get the mode of the route. Can be useful for specific processings
     * @return string The mode.
     */
    public function getMode() : string{
        return $this->_mode;
    }

    /**
     * Call the route middlewares and execute the callable.
     * @param array(default=[]) $params the parameters found from the uri template
     */
    public function call(array $params=[]){
        try{
            call_user_func_array($this->_callable, $params);
        }
        catch(\TypeError $e){
        }
    }
};

?>