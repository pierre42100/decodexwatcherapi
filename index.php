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
$dw = new decodexWatcher();

//Create configuration element
$config = new config();
$dw->register("config", $config);

//Include configuration
foreach(glob(PROJECT_PATH."config/*.php") as $confFile){
    require $confFile;
}