<?php


$file = BASENAME."/include/config.ini";

$config = [];
if(is_file($file)){
    $config = parse_ini_file($file, true);
}

unset($file);
?>