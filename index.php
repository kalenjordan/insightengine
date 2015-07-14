<?php

require_once(dirname(__FILE__) . '/vendor/autoload.php');
$ok = @session_start();
if(!$ok){
    session_regenerate_id(true);
    session_start();
}

$local = new Model_LocalConfig();
$local->configureDatabase();

if ($local->getHideExceptions()) {
    ini_set('display_errors', 'Off');
} else {
    error_reporting(-1);
    ini_set('display_errors', 'On');
}

$base = $local->getBaseRoute();
try {
    Toro::serve(array(
        "$base/"                    => "Controller_Index",
        "$base/manage"              => "Controller_Manage_Tags",
        "$base/manage/account"      => "Controller_Manage_Account",
        "$base/manage/logout"       => "Controller_Manage_LogOut",
    ));
} catch (Exception $e) {
    if ($local->getHideExceptions()) {
        mail("kalen@magemail.co", "MageHero Exception: " . $e->getMessage(), $e->getTraceAsString());
        die("Uh-oh.  Something's not right.  Heroes have been deployed to fix it.");
    } else {
        throw $e;
    }
}
