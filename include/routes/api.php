<?php
/**
 * Api Route to get the list of all api routes.
 * Method : GET
 * Route : /api/v1/routes
 */
(new \Core\Route("GET", "routes", [\Controllers\Api::class, "routes"]))->prefix("api/v1");