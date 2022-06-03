<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
use Routes\Router;
$route = new Router();
$route ->execute();
?>
