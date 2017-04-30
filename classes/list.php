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
        
        echo $listContent;

        return true;
    }

}