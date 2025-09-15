<?php
/**
 * Display the registered bikes
 * Method : GET
 * Route : /bikes
 */
new \Core\Route(["GET"], "bikes", [\Controllers\Bike::class, "list"]);

/**
 * Welcome page displays a dashboard of monitored datas and statistics
 * Method : GET
 * Route : /
 */
new \Core\Route(["GET"], "", [\Controllers\Welcome::class, "dashboard"]);
