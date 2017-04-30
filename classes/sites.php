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

        return true;

    }
    
}