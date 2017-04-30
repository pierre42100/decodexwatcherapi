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
     * Update the informations about a site
     *
     * @param Integer $siteID The ID of the site
     * @param Array $siteInfos The informations about the website
     * @return Boolean True for a success
     */
    public function update($siteID, $siteInfos){
        
        //First, mark all previous entry as "old"
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
        
        //Insert the new line
        $siteInfos["ID"] = $siteID;
        if(!$this->insertSiteInformations($siteInfos))
            return false; //Something went wrong

        //Everything went good
        return true;
    }
}