<?php
/**
 * Entry point for all the incoming requests
 */

// Can be used everywhere
define("BASENAME", dirname(__DIR__));

// Maqe sure all the mecanics are protected in a private scope
require_once(BASENAME."/include/bootstrap.php");