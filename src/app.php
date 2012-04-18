<?php

use Symfony\Component\HttpFoundation\Response;

$app->get('/hello', function() use ($app) {

    return new Response('Hello World!');

})->bind('foo');

$app->get('/', function() use ($app) {

    return $app['twig']->render('start.html.twig');

});
