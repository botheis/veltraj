<?php
namespace Core;

/**
 * Centralize all datas concerning incoming request
 */
class Request{

    static private $_instance;
    private $_client;
    private $_ip;
    private $_protocol;
    private $_method;
    private $_uri;
    private $_port;
    private $_get;
    private $_post;
    private $_headers;

    /**
     * Initialize the instance of \Core\Request.
     * @return \Core\Request object with datas concerning the incoming request
     */
    static public function getInstance(){
        return static::$_instance = (static::$_instance == NULL) ? new \Core\Request() : static::$_instance;
    }

    /**
     * Instanciate the object
     * @note Do not call this method through new \Core\Request.
     */
    private function __construct(){
        $this->_client = (!empty($_SERVER["HTTP_USER_AGENT"])) ? htmlentities($_SERVER["HTTP_USER_AGENT"]) : "";

        $this->_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? htmlentities($_SERVER['REMOTE_ADDR']) : "0.0.0.0";
        $this->_protocol = (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https' && $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";

        $this->_method = (!empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? strtoupper(htmlentities($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) : htmlentities($_SERVER['REQUEST_METHOD']);
        
        $this->_uri = (!empty($_GET['uri'])) ? htmlspecialchars(trim($_GET['uri'], '/')) : '';
        unset($_GET['uri']);

        $this->_port = (!empty($_SERVER['SERVER_PORT'])) ? htmlentities($_SERVER['SERVER_PORT']) : '80';
        $this->_get = &$_GET;
        $this->_post = &$_POST;
        $this->_headers = getallheaders();
    }

    /**
     * Detect if the key is within the $_GET parameters
     * 
     * @param string $key the researched key
     * @return bool true if the key exists
     * @return bool false if the key doesn't exist or is NULL
     * @note A false value is detected as existing value
     */
    public function hasGet(string $key):bool{
        if(isset($this->_get[$key])){
            return ($this->_get[$key] !== NULL) ? true : false;
        }
        else{
            return false;
        }
    }

    /**
     * Detect if the key is within the $_POST parameters
     * @param string $key the researched key
     * @return bool true if the key exists
     * @return bool false if the key doesn't exist or is NULL
     * @note A false value is detected as existing value
     */
    public function hasPost(string $key):bool{
        if(isset($this->_post[$key])){
            return ($this->_post[$key] !== NULL) ? true : false;
        }
        else{
            return false;
        }
    }

    /**
     * Detect if the key is within the request headers
     * @param string $key the researched key
     * @return bool true if the key exists
     * @return bool false if the key doesn't exist or is NULL
     * @note A false value is detected as existing value
     */
    public function hasHeader(string $key):bool{
        if(isset($this->_headers[$key])){
            return ($this->_headers[$key] !== NULL) ? true : false;
        }
        else{
            return false;
        }
    }

    /**
     * Pick a value from the $_GET parameters.
     * @param string(default="") $key Corresponds to the wanted key.
     * @return mixed the value corresponding to the given key. If $key is empty, returns all the array.  If the key is not found, returns NULL
     */
    public function get(string $key=""):mixed
    {
        if($key == ""){
            return $this->_get;
        }

        return ($this->hasGet($key)) ? $this->_get[$key] : NULL;
    }

    /**
     * Pick a value from the $_POST parameters.
     * @param string(default="") $key Corresponds to the wanted key.
     * @return mixed the value corresponding to the given key. If $key is empty, returns all the array.  If the key is not found, returns NULL
     */
    public function post(string $key=""):mixed{
        if($key == ""){
            return $this->_post;
        }

        return ($this->hasPost($key)) ? $this->_post[$key] : NULL;
    }

    /**
     * Pick a value from the request headers.
     * @param string(default="") $key Corresponds to the wanted key.
     * @return mixed the value corresponding to the given key. If $key is empty, returns all the array.  If the key is not found, returns NULL.
     */
    public function header(string $key=""):string{
        if($key == ""){
            return $this->_headers;
        }

        return ($this->hasHeader($key)) ? $this->_headers[$key] : NULL;
    }

    /**
     * Get the user's client.
     * @return string the client description.
     */
    public function client(){return $this->_client;}
    
    /**
     * Get the user ip.
     * @return string the user's ip.
     */
    public function ip(){return $this->_ip;}

    /**
     * Get the request protocol.
     * @return string the used protocol (http or https).
     */
    public function protocol(){return $this->_protocol;}

    /**
     * Get the request method
     * @return string the incoming request method. If the method has been overriden, give the overriden method.
     */
    public function method(){return $this->_method;}

    /**
     * Get the called uri
     * @return string the uri asked by the client.
     */
    public function uri(){return $this->_uri;}

    /**
     * Get the request port
     * @return string the port used for the incoming request.
     * @note the port is not cast in integer
     */
    public function port(){return $this->_port;}
};

?>