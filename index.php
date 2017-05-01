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
include(__DIR__."/init.php");

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