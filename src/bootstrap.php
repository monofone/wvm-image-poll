<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

// Create the application
$app = new Application();
$app['autoloader']->registerNamespaceFallback(__DIR__);

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options'            => array(
        'driver'    => 'pdo_sqlite',
        'path'      => __DIR__.'/app.sqlite',
    ),
    'db.dbal.class_path'    => __DIR__.'/vendor/doctrine2-dbal/lib',
    'db.common.class_path'  => __DIR__.'/vendor/doctrine2-common/lib',
));

$app->register(new Silex\Provider\SymfonyBridgesServiceProvider(), array(
    'symfony_bridges.class_path'  => __DIR__.'/vendor',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/vendor/twig/lib',
));

 $app['twig']->addGlobal('layout', $app['twig']->loadTemplate('layout.html.twig'));
// Add services to the DI container
//$app['my.service'] = function() {
//    // ...
//    return new My\Service();
//};
//$app['my.shared_service'] = $app->share(function() {
//    // ...
//    return new My\SharedService();
//});

// Configuration parameters
$app['debug'] = false;
//$app['my.param'] = '...';

// Override settings for your dev environment
$env = isset($_ENV['SILEX_ENV']) ? $_ENV['SILEX_ENV'] : 'dev';

if ('dev' == $env) {
    $app['debug'] = true;
    //$app['my.param'] = '...';
}
/* @var $app Silex\Application */
$app['max_votes'] = 5;
$app['imageService'] = $app->share(function() use ($app){
    return new Blage\ImageService($app['db'], array('max_votes' => $app['max_votes']));
});

$app['userhandler'] = $app->share(function() use ($app){
    return new Blage\UserManager($app['db'], $app['session']);
});
// Error handling
$app->error(function (\Exception $ex, $code) use ($app) {

    if ($app['debug']) {
        return;
    }

    if (404 == $code) {
        return new Response(file_get_contents(__DIR__.'/../web/404.html'));
    } else {
        // Do something more sophisticated here (logging etc.)
        return new Response('<h1>Error!</h1>');
    }

});

return $app;
