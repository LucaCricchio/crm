<?php

define("ACCESS", 1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

date_default_timezone_set('Europe/Rome');

require_once("configuration.php");
require_once("tasks.php");


Helper::getDb()->connect();

$task = Helper::get("task", "");

// Se non è loggato e la task non è authenticate_user si sta cercando di accedere ad una pagina
// che richiede il login. Comunico la sessione scaduta e chiudo lo script.
if(Helper::isLogged() === false && $task !== "authenticate_user") {
    echo Helper::$LOGIN_EXPIRED;
    exit();
}

$performer = new Tasks($task);
$out = $performer->execute();
if($out === false)
    echo Helper::$TASK_NOT_DEFINED;
else
    echo $out;
