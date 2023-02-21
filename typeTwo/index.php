<?php

require_once 'vendor/autoload.php';

use App\Controllers\ApplicationController;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$application = new ApplicationController();

$application->run();
