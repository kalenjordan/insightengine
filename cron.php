<?php

ini_set('display_errors', 1);
require_once(dirname(__FILE__) . '/vendor/autoload.php');

$local = new Model_LocalConfig();
$local->configureDatabase();

$cron = new Model_Cron();
if (isset($argv[1]) && $argv[1]) {
    $cron->setUsername($argv[1]);
}

if (isset($argv[2]) && $argv[2]) {
    $cron->setTagToProcess($argv[2]);
}

$cron->run();