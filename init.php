<?php
/**
 * Page initiator
 *
 * @author Pierre HUBERT
 */

//Define the base of the project
define("PROJECT_PATH", __DIR__."/");

//Include classes
foreach(glob(PROJECT_PATH."classes/*.php") as $classFile){
    require_once $classFile;
}

//Include functions
foreach(glob(PROJECT_PATH."functions/*.php") as $funcFile){
    require_once $funcFile;
}

//Create root object
$dw = new DW();

//Create configuration element
$config = new config();
$dw->register("config", $config);

//Include configuration
foreach(glob(PROJECT_PATH."config/*.php") as $confFile){
    require $confFile;
}

//Connexion to the database
$db = new DBLibrary(($dw->config->get("site_mode") == "debug"));
$dw->register("db", $db);
$db->openSQLite(PROJECT_PATH."data/".$dw->config->get("database_filename"));
define("DB_PREFIX", $dw->config->get("database_prefix"));

//Register auth class
$auth = new Auth();
$dw->register("auth", $auth);

//Register list class
$lists = new lists();
$dw->register("lists", $lists);

//Register sites class
$sites = new sites();
$dw->register("sites", $sites);

//Register changes class
$changes = new changes();
$dw->register("changes", $changes);

//Register log class
$log = new Log();
$dw->register("log", $log);