<?php

require_once '../vendor/autoload.php';

use Silex\Application;

use ESExplorer\RouteController\DefaultRoute;

$app = new Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => dirname(__FILE__).'/../templates'
));

$app->mount('/', new DefaultRoute());

$app->run(); 