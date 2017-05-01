<?php
/**
 * List class
 *
 * @author Pierre HUBERT
 */

class lists{

    /**
     * Get current list
     *
     * @return Mixed False for a failure, an array in case of success
     */
    public function getCurrent(){
        //Perform a request on the database
        $tableName = DB_PREFIX."sitesInformations, ".DB_PREFIX."sitesName";
        $conditions = "WHERE ".DB_PREFIX."sitesName.ID = ".DB_PREFIX."sitesInformations.ID_sitesName AND ".DB_PREFIX."sitesInformations.latest = 1";

        //Try to perform request
        $results = $this->parent->db->select($tableName, $conditions);

        if($results === false)
            return false; //An error occured
        
        //Give data a structure
        return $this->giveStructure($results);
        
    }

    /**
     * Get the list of a specified timestamp
     *
     * @param Intenger $time The time of the list
     * @return Mixed False for a failure, an array in case of success
     */
    public function getOnTimestamp($time){
        //Security
        $time = $time*1;

        //Perform a request on the database
        $tableName = DB_PREFIX."sitesInformations, ".DB_PREFIX."sitesName";
        $conditions = "INNER JOIN (SELECT MAX(ID) AS maxID, ID_sitesName FROM dw_sitesInformations  WHERE dw_sitesInformations.insertTime <= ? GROUP BY ID_sitesName) maxID ON maxID.maxID = dw_sitesInformations.id WHERE dw_sitesInformations.ID_sitesName = dw_sitesName.ID ORDER BY dw_sitesName.ID";
        $datacond = array($time);

        //Try to perform request
        $results = $this->parent->db->select($tableName, $conditions, $datacond);

        if($results === false)
            return false; //An error occured
        
        //Give data a structure
        return $this->giveStructure($results, true);
    }

    /**
     * Update the list
     *
     * @return Boolean True for a success
     */
    public function update(){
        //Get the new list source
        $listURL = $this->parent->config->get("decodexListURL");
        $listContent = file_get_web($listURL);
        if($listContent === false)
            return false;
        
        //Try to decode the list
        $newList = $this->decodeList($listContent);
        if($newList === false)
            return false;
        
        //Get current list
        $currentList = $this->getCurrent();
        
        //Compare each site
        foreach($currentList as $urlname=>$currentInfos){

            //We check if the current element exists in the new list
            if(!isset($newList[$urlname]))
                continue; //Skip current check

            //Compare the two site, if different, update the database
            if(json_encode($currentInfos["urls"]) != json_encode($newList[$urlname]["urls"]) OR
                $currentInfos["comment"] != $newList[$urlname]["comment"] OR
                $currentInfos["trustLevel"] != $newList[$urlname]["trustLevel"]
            )
            {
                //The lists are different, update the database
                if(!$this->parent->sites->update($currentInfos["ID_sitesName"], $newList[$urlname]))
                    return false; //An error occured while trying to update
            }
            //Remove the site from the new listlist
            unset($newList[$urlname]);
            unset($currentList[$urlname]);
        }

        //All remaining sites from newlist are new sites, they have to be inserted
        foreach($newList as $urlname=>$siteInfos){
            //Try to insert a new site
            if(!$this->parent->sites->insert($siteInfos))
                return false; //Couldn't insert a new site
        }

        //All remaining sites from current list are deleted sites, they have to be set as "not current"
        foreach($currentList as $urlname=>$siteInfos){
            //Mark site as deleted
            if(!$this->parent->sites->delete($siteInfos["ID_sitesName"]))
                return false; //Couldn't delete site
        }

        //This is a success
        return true;
    }

    /**
     * Decode a list provided by the Decodex webservice
     *
     * @param String $listContent The content of the list to decode
     * @return Mixed False for a failure / An array in case of success
     */
    private function decodeList($listContent){
        //Try to decode JSON content
        $listArray = json_decode($listContent, true);
        if($listArray === false)
            return false;
        
        //Check array content
        if(!isset($listArray["sites"]) OR !isset($listArray["urls"]))
            return false; //JSON source code can't be processed
        
        //Process sites
        $sitesInfos = array();
        foreach($listArray["sites"] as $id=>$infos){
            $sitesInfos[$id] = array(
                "trustLevel" => $infos[0],
                "comment" => $infos[1],
                "name" => $infos[2],
                "urlName" => $infos[3],
                "urls" => array(),
            );
        }

        //Process urls
        foreach($listArray["urls"] as $url=>$id){
            //Add url (if an associated website exists)
            if(isset($sitesInfos[$id])){
                $sitesInfos[$id]["urls"][] = $url;
            }
        }

        //Replace IDs by URL names
        $return = array();
        foreach($sitesInfos as $process){
            $return[$process['urlName']] = $process;
        }

        //Return result
        return $return;
    }

    /**
     * Give a correct structure to a list
     *
     * @param Array $sitesList The list of sites to structure
     * @param Boolean $deleteRemoved Optionnal, define if entries marked has deleted has to be returned or not
     * @return Array The structured list
     */
    private function giveStructure($sitesList, $deleteRemoved=false){
        $return = array();

        //Process list
        foreach($sitesList as $process){
            $return[$process["urlName"]] = $process;

            //Decode URLs
            $return[$process["urlName"]]["urls"] = json_decode($return[$process["urlName"]]["urls"], true);

            //Check if we have to remove deleted entries
            if($deleteRemoved){
                if($process["comment"] == "The website was removed from the list.")
                    //Remove entry
                    unset($return[$process["urlName"]]);
            }
        }

        //Return result
        return $return;
    }

}