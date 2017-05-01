<?php
/**
 * Log service main method
 *
 * @author Pierre HUBERT
 */

/**
 * Log class method
 */
class Log {
	/**
	 * Public constructor
	 */
	public function __construct(){
		//Nothing for now
	}

	/**
	 * Log a message
	 *
	 * @param String $fileName The name of the file that call the function
	 * @param String $message The message to log
	 * @return Boolean True for a success
	 */
	public function logMessage($fileName, $message){
		//Generate the message to write
		$write = date('l jS \of F Y h:i:s A')." - ".$fileName." - ".$message."\n";

		//Get the week number
		$dayNumber = floor(time()/604800);

		//Determine file name
		$fileName = PROJECT_PATH."data/".$this->parent->config->get("logFolder").$dayNumber;

		//Write the line
		if($logFile = fopen($fileName, "a")){

			//Write the new line
			fputs($logFile, $write);

			//Close the file
			fclose($logFile);

			//Change file permissions if required
			if(substr(decoct(fileperms($fileName)), 2) != "0666")
				chmod($fileName, 0666);
				
			//Everything went good
			return true;
		}

		//Else there was an error
		return false;
	}

	/**
	 * Clean log folder
	 *
	 * @return Boolean True for a success
	 */
	public function clean(){

		//Get the week number
		$dayNumber = floor(time()/604800);

		//Determine log folder name
		$logFolderName = PROJECT_PATH."data/".$this->parent->config->get("logFolder");
		$dayNumberExpire = floor(time()/604800)-$this->parent->config->get("keepLogs");

		//Get the file list
		foreach(glob($logFolderName."*") as $fileName){
			//Determine day number
			$dayNumber = str_replace($logFolderName, "", $fileName);

			//If the file is too old, delete it
			if($dayNumber<$dayNumberExpire){
				if(!unlink($fileName))
					$error = true;
			}
		}
		
		//We check if there was an error while try to delete log files
		if(isset($error))
			return false; //An error occured

		//If we arrive here, the log was successfully cleaned 
		return true;
	}
}