<?php

require_once __DIR__.'/../vendor/.composer/autoload.php';

$app = require __DIR__.'/../src/bootstrap.php';
require __DIR__.'/../src/app.php';

$app->run();
