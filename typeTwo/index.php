<?php

require_once 'vendor/autoload.php';

use App\Application;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$application = new Application();

echo $application->run();
