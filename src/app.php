<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;

$app->before(function(Request $request) use ($app) {
    $app['session']->start();
    if($app['session']->get('redirected')){
        $app['session']->set('redirected', false);
        throw new \Exception('General Error' );
    }
    $username = $request->get('user');
    if (!$app['userhandler']->check() ||($username !== null && $app['session']->get('username') !== $username )) {
        if (!$app['userhandler']->checkUser($username)) {
            $app['session']->set('redirected', true);
            return $app->redirect($app['url_generator']->generate('error'));
        }
    }
});

$app->get('/', function(Request $request) use ($app) {
    $finder = Finder::create()->files()->name('thumb.*.jpg')->in(__DIR__ . '/../web/photos');

    foreach ($finder as $file) {
        $thumbname = $file->getRelativePathname();
        $imageId = substr($thumbname, 6, -4);
        $images[] = array(
            'thumbname' => $thumbname,
            'imageid' => $imageId,
            'userVoted' => $app['imageService']->isImageVotedByUser($imageId, $app['session']->get('userId'))
        );
    }

    return $app['twig']->render('start.html.twig', array('images' => $images));
})->bind('start');

$app->get('/image/{imageId}', function($imageId) use ($app) {
            if (!$app['userhandler']->check()) {
                return $app->redirect($app['url_generator']->generate('error'));
            }
            $view['imageId'] = $imageId;
            $view['userVoted'] = $app['imageService']->isImageVotedByUser($imageId, $app['session']->get('userId'));
            $view['remainingVotes'] = $app['max_votes'] - $app['imageService']->getCountedVotes($app['session']->get('userId'));
            $view['maxVotes'] = $app['max_votes'];
            $view['filename'] = $imageId . '.jpg';

            return $app['twig']->render('image.html.twig', array('view' => $view));
        })->bind('image');

$app->get('/image/vote/{imageId}', function($imageId) use($app) {
            if (!$app['userhandler']->check()) {
                return $app->redirect($app['url_generator']->generate('error'));
            }

            $userId = $app['session']->get('userId');
            $voted = $app['imageService']->voteImage($imageId, $userId);
            $remainingVotes = $app['max_votes'] - $app['imageService']->getCountedVotes($userId);

            return new Response(json_encode(array('voted' => $voted, 'countedVotes' => $remainingVotes)));
        })->bind('vote_image');

$app->get('/error', function() use ($app) {
            return new Response('failed initializing session');
        })->bind('error');