<?php
/**
 * Change getter main class
 *
 * @author Pierre HUBERT
 */

class changes{
    /**
     * Public constructor
     */
    public function __construct(){
        //Nothing now
    }

    /**
     * Get changes in a specified interval
     *
     * @param Integer $from Begin of the interval
     * @param Integer $to The end of the interval
     * @return Mixed False for a failure / An array with changes in case of success
     */
    public function get($from, $to){
        //Perform a request on the database
		$tableName = DB_PREFIX."sitesInformations, ".DB_PREFIX."sitesName";
        $conditions = "WHERE ".DB_PREFIX."sitesName.ID = ".DB_PREFIX."sitesInformations.ID_sitesName AND ".DB_PREFIX."sitesInformations.insertTime >= ?  AND ".DB_PREFIX."sitesInformations.insertTime <= ?";
		$values = array($from*1, $to*1);

        //Try to perform request
        $changes = $this->parent->db->select($tableName, $conditions, $values);
        if($changes === false)
            return false; //An error occured while trying to perform the request
        
        //Process result
        foreach($changes as $id=>$process){
            $changes[$id]["urls"] = json_decode($process["urls"], true);
        }

        //Return result
        return $changes;
    }
}