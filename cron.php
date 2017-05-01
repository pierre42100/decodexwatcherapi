<?php
/**
 * Cron file
 *
 * @author Pierre HUBERT
 */

/**
 * Page initiator
 */
require(__DIR__."/init.php");

/**
 * Security - Opening this file from a browser is prohibited
 */
if(isset($_SERVER['HTTP_HOST'])){
    //The error code is the unique indication we'll give to user
    http_response_code(401);

    //But we log the error
    $dw->log->logMessage(__FILE__, "ACCESS DENIED - Trying to run cron job from a browser !");

    //Now we quit script
    exit();
}

/**
 * Try to update Decodex list
 */
if($dw->lists->update()){
    //There isn't any mail to be sent
    $dw->log->logMessage(__FILE__, "Decodex list was successfully updated !");
}
else {
    //An error happened
    $dw->log->logMessage(__FILE__, "ERROR - An error occurred while trying to update Decodex list !");
}

/**
 * Clean logs
 */
if(!$dw->log->clean()){
    //Couldn't clean logs
    $dw->log->logMessage(__FILE__, "ERROR - Couldn't clean log folder.");
}
else{
    $dw->log->logMessage(__FILE__, "Log folder was cleaned.");
}