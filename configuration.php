<?php

defined("ACCESS") or die;

class Configuration {
    
    public static $db_host = "localhost";
    public static $db_name = "test";
    public static $db_user = "test";
    public static $db_password = "R3kd/f38Jm";
    public static $db_table_prefix = "";
}

// Richiedo tutte le classi.
$files = scandir("classes");
foreach($files as $file)
    if(substr($file, strlen($file) - 3) == "php")
        require_once("classes/" . $file);

