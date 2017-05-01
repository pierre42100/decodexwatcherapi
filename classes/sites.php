<?php
/**
 * Sites main object
 *
 * @author Pierre HUBERT
 */
class sites {

	/**
	 * Public constructor
	 */
	public function __construct(){
		//Nothing now
	}

	/**
	 * Try to get a site ID from a given name
	 *
	 * @param String $name The name of the site
	 * @param String $nameType Optionnal, the type of the name of the site
	 * @return Mixed False for a failure / The ID of the website else
	 */
	public function getIDfromName($name, $nameType="urlname"){
		//Try to perform a request on the database
		$tableName = DB_PREFIX."sitesName";
		$conditions = "WHERE ".($nameType == "urlname" ? "urlName" : "name")." = ?";
		$values = array($name);

		//Try to perform request
		if(!$result = $this->parent->db->select($tableName, $conditions, $values))
			return false;
		
		//Process result
		if(count($result) == 0)
			return false; //No ID was found
		else
			return $result[0]["ID"]; //An ID was found
	}

	/**
	 * Try to get a site ID from a given URL
	 *
	 * @param String $url The URL of the site
	 * @return Mixed The ID of the webiste or false in case of failure
	 */
	private function getIDfromURL($url){

		//Prepare URL for the request
		//Remove any www. or http:// or https://
		$url = str_replace(array("www.", "http://", "https://"), "", $url);
		if(preg_match("</>", $url)){
			$arrayUrl = explode("/", $url);
			if($arrayUrl[count($arrayUrl)-1] == "")
				unset($arrayUrl[count($arrayUrl)-1]); //Remove last entry if empty
			$url = implode("/", $arrayUrl);
		}

		$url = "%\"".$url."\"%";
		
		//Try to perform a request on the database
		$tableName = DB_PREFIX."sitesInformations";
		$conditions = "WHERE urls LIKE ? ORDER BY ID DESC LIMIT 1";
		$values = array($url);
		if(!$result = $this->parent->db->select($tableName, $conditions, $values))
			return false;

		//Process result
		if(count($result) == 0)
			return false; //No ID was found
		else
			return $result[0]["ID_sitesName"]; //An ID was found
	}

	/**
	 * Generate a site ID, given its name and urlName
	 *
	 * @param String $name The name of the website
	 * @param String $urlName The URL name of the website
	 * @return Mixed The ID of the website in case of success / False for a failure
	 */
	private function generateSiteID($name, $urlName){
		//Perform a request on the database
		$tableName = DB_PREFIX."sitesName";
		$values = array(
			"name" => $name,
			"urlName" => $urlName
		);

		//Try to perform request
		if(!$this->parent->db->addLine($tableName, $values))
			return false;
		
		//Try to get site ID
		if(!$siteID = $this->getIDfromName($urlName))
			return false; //An error occurred
		
		//Return new site ID
		return $siteID;
	}

	/**
	 * Insert a new siteInformations line
	 *
	 * @param Array $siteInfos Informations about the line to add
	 * @param Boolean $latest Optionnal, define if the line as to marked as the latest
	 * @return Boolean True for a success
	 */
	private function insertSiteInformations($siteInfos, $latest=true){
		//Perform a request on the database
		$tableName = DB_PREFIX."sitesInformations";
		$values = array(
			"ID_sitesName" => $siteInfos["ID"],
			"insertTime" => time(),
			"latest" => ($latest ? 1 : 0),
			"urls" => json_encode($siteInfos["urls"]),
			"comment" => $siteInfos["comment"],
			"trustLevel" => $siteInfos["trustLevel"]
		);

		//Try to insert the line
		if(!$this->parent->db->addLine($tableName, $values))
			return false; //Something happened
		else
			return true; //It is a success
	}

	/**
	 * Insert a new site
	 *
	 * @param Array $siteInfos Informations about the new website
	 * @return Boolean True for a success
	 */
	public function insert(array $siteInfos){
		//Check if the site has already an ID or not
		if(!$siteID = $this->getIDfromName($siteInfos['urlName'], "urlname")){
			//Try to generate a new ID
			if(!$siteID = $this->generateSiteID($siteInfos["name"], $siteInfos['urlName']))
				return false; //Couldn't generate siteID
		}

		//Insert informations about the website in the main database
		$siteInfos["ID"] = $siteID;
		if(!$this->insertSiteInformations($siteInfos))
			return false; //Couldn't insert a new line in the database

		//Else the site was successfully inserted
		return true;
	}
	
	/**
	 * Mark all entries about a siteID as "old"
	 *
	 * @param Integer $siteID The ID of the site to update
	 * @return Boolean True for a success
	 */
	private function markSiteEntriesAllOld($siteID){
		//Perform a request on the database
		$tableName = DB_PREFIX."sitesInformations";
		$conditions = "ID_sitesName = ?";
		$modifs = array(
			"latest" => 0
		);
		$whereValues = array(
			$siteID
		);

		//Try to perform request on the database
		if(!$this->parent->db->updateDB($tableName, $conditions, $modifs, $whereValues))
			return false; //Couldn't update database
		
		//Else it is a success
		return true;
	}

	/**
	 * Update the informations about a site
	 *
	 * @param Integer $siteID The ID of the site
	 * @param Array $siteInfos The informations about the website
	 * @return Boolean True for a success
	 */
	public function update($siteID, $siteInfos){
		
		//First, mark all previous entry as "old"
		if(!$this->markSiteEntriesAllOld($siteID))
			return false;
		
		//Insert the new line
		$siteInfos["ID"] = $siteID;
		if(!$this->insertSiteInformations($siteInfos))
			return false; //Something went wrong

		//Everything went good
		return true;
	}

	/**
	 * Delete a site
	 *
	 * @param Integer $siteID The ID of the website to delete
	 * @return Boolean True for a success
	 */
	public function delete($siteID){
		//Mark all site entries as old
		if(!$this->markSiteEntriesAllOld($siteID))
			return false;
		
		//Insert an "old" line that indicate the website was remoed
		$siteInfos = array(
			"ID" => $siteID,
			"urls" => "[]",
			"comment" => "The website was removed from the list.",
			"trustLevel" => 4,
		);

		//Insert an information line on the database
		if(!$this->insertSiteInformations($siteInfos, false))
			return false; //Something went wrong

		//Everything went good
		return true;
	}

	/**
	 * Get informations about a website
	 *
	 * @param Integer $siteID The ID of the website
	 * @param Integer $nb The number of versions to get (0 to unlimited)
	 * @return Array An array containing all the informations
	 */
	private function getInfosWebsite($siteID, $nb=1){
		//Perform a request on the database
		$tableName = DB_PREFIX."sitesInformations, ".DB_PREFIX."sitesName";
        $conditions = "WHERE ".DB_PREFIX."sitesName.ID = ".DB_PREFIX."sitesInformations.ID_sitesName AND ".DB_PREFIX."sitesName.ID = ?";
		$values = array($siteID*1);

		//Add a limit if required
		if($nb!=0){
			$conditions .= " LIMIT ".$nb*1;
		}

		//Try to perform request on database
		if(!$infosSite = $this->parent->db->select($tableName, $conditions, $values))
			return false;
		
		//We check if we didn't get anything
		if(count($infosSite) == 0)
			return false;

		//Process values
		foreach($infosSite as $id=>$process){
			$infosSite[$id]["urls"] = json_decode($infosSite[$id]["urls"], true);
		}

		//Else return all values
		return $infosSite;
	}

	/**
	 * Try to get informations about a website given an URL
	 *
	 * @param String $url The URL on wich we should perform researchs
	 * @param String $limit Optionnal, the number of lines of informations to get (0 for unlimited)
	 * @return Array The informations about the website
	 */
	public function getInfosFromURL($url, $limit=1){
		//First, try to determine siteID
		if(!$siteID = $this->getIDfromURL($url))
			return false;
		
		//Then get informations about a website
		if(!$infosSite = $this->getInfosWebsite($siteID, $limit))
			return false;
		
		//Else everything is OK
		if($limit == 1)
			return $infosSite[0]; //First line only
		else
			return $infosSite; //Every lines else
	}
}