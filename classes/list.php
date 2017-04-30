<?php
/**
 * List class
 *
 * @author Pierre HUBERT
 */

class lists{

    /**
     * Update lists
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
        $arrayList = $this->decodeList($listContent);
        if($arrayList === false)
            return false;
        
        print_r($arrayList);

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

}