<?php

ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/errors.log");

set_time_limit(10);
define('ABSPATH', true);

require_once "functions.php";

$result = json_decode($argv[1], true);

if ($result === null) {
    
    die("Well, That's smart guess, But sry I'm smarter than you (=");

}

SendMessage(111999636, 'Hello World !');