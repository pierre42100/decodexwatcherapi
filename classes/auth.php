<?php
/**
 * Authentification class
 *
 * @author Pierre HUBERT
 */

class auth {

    /**
     * Public constructor
     */
    public function __construct(){
        //Nothing now
    }

    /**
     * Check given tokens
     *
     * @param array $tokens An array containing all the login tokens
     * @return Boolean True or false depending of the success of the operation
     */
    public function checkTokens(array $tokens){
        //Get real tokens
        $goodTokens = $this->parent->config->get("priviliegedTokens");

        //Check tokens
        foreach($goodTokens as $name=>$value){
            if(!isset($tokens[$name]))
                return false; //At least one token is missing
            
            if($tokens[$name] !== $value)
                return false; //A token is invalid
        }

        //Else tokens are valid
        return true;
    }

}