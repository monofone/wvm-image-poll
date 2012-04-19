<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;

$app->get('/hello', function() use ($app) {

    return new Response('Hello World!');

})->bind('foo');

$app->get('/', function() use ($app) {
    
    $finder = Finder::create()->files()->name('thumb.*.jpg')->in(__DIR__.'/../web/photos');
    
    foreach($finder as $file){
        $images[] = array(
                    'thumbname' => $file->getRelativePathname(),
                    'filename' => substr($file->getRelativePathname(),6)
                );
    }
    
    return $app['twig']->render('start.html.twig', array('images' => $images));

});
