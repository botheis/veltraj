<?php

namespace Core;

class Session{

    static private $_instance = NULL;

    private $_options;
    
    /**
     * Create an unique instance of Session object
     * @return \Core\Session object
     */
    static public function getInstance(){
        return static::$_instance = (static::$_instance == NULL) ? new \Core\Session() : static::$_instance;
    }


    /**
     * Instanciate a new object of \Core\Session
     * @note don't call directly this method
     */
    private function __construct(){
        $this->_options = [];
    }

    /**
     * Start / Activate the session
     * @note Use setOption method to modify session options
     */
    public function start(){
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start($this->_options);
        }
    }


    /** 
     * Stop the current session
     */
    public function stop(){
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start($this->_options);
        }
        session_destroy();
    }

    /**
     * If the session needs to be reset, stop the current session and start just after
     */
    public function restart(){
        $this->stop();
        $this->start();
    }

    /**
     * Check if the specified key exists in SESSION datas.
     * @return true When the session possesses a non-null value corresponding to $key.
     * @return false when the session has no value set for the specified key.
     */
    public function has(string $key) : bool{
        if(!isset($_SESSION[$key])){
            return false;
        }
        return ($_SESSION[$key] !== NULL) ? true : false;
    }

    /**
     * Get the value associated to the specified key
     * @param string(default="") $key the wanted key
     * @return mixed the value corresponding to the key. If no data has been associated to the key, returns NULL.
     */
    public function get(string $key="") : mixed{
        if($key == ""){
            return $_SESSION;
        }

        return ($this->has($key)) ? $_SESSION[$key] : NULL;
    }

    /**
     * Associate a value to a key in SESSION.
     * @param string $key the key to associate to the value
     * @param mixed $value the value to associate
     * @return mixed the previous value associated to the key.
     */
    public function set(string $key, mixed $value) : mixed{
        $old = $this->get($key);
        $_SESSION[$key] = $value;
        return $old;
    }

    /** 
     * Set session options, see https://www.php.net/manual/fr/session.configuration.php
     * @param string $key the key to associate the value
     * @param string $value the value associated to the key
     */
    public function setOption(string $key, string $value){
        $this->_options[$key] = $value;
    }
};

?>