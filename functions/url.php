<?php
/**
 * URL functions
 *
 * @author Pierre HUBERT
 */

/**
 * Try to download a file from a specific location
 *
 * @param String $url The URL to download
 * @return Mixed False for a failure / File content in case of result
 */
function file_get_web($url){
    // create a new cURL resource
    $ch = curl_init();

    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    // grab URL and pass it to the browser
    $response = curl_exec($ch);

    //We check if there was an error
    if(curl_errno($ch))
        return false;
    
    //We check if the response code is 200
    if(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) != 200)
        return false;

    // close cURL resource, and free up system resources
    curl_close($ch);

    //Return result
    return $response;
}