<?php
/**
 * Decodex watcher
 * A tool to analyse the evolution of the Decodex official websites list
 *
 * This project is a RestServer which is publicy available
 *
 * @author Pierre HUBERT
 */

/**
 * Page initiator
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
$db = new DBLibrary();
$dw->register("db", $db);
$db->openSQLite(PROJECT_PATH."data/".$dw->config->get("database_filename"));

//Register auth class
$auth = new Auth();
$dw->register("auth", $auth);

//Include RestControllers
foreach(glob(PROJECT_PATH."RestControllers/*.php") as $restControllerFile){
    require_once $restControllerFile;
}

//Include RestServer library
require PROJECT_PATH."3rdparty/RestServer/RestServer.php";

/**
 * Handle Rest requests
 */
$server = new \Jacwright\RestServer\RestServer($dw->config->get("site_mode"));

//Include controllers
foreach(get_included_files() as $filePath){
    if(preg_match("<RestControllers>", $filePath)){
        $className = strstr($filePath, "RestControllers/");
        $className = str_replace(array("RestControllers/", ".php"), "", $className);
        $server->addClass($className);
    }
}

//Hanlde
$server->handle();