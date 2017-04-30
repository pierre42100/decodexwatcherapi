<?php
/**
 * Authentication class
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
     * Handle a Rest authentication
     *
     * @return Boolean True for a success
     */
    public function restAuth(){
        //Authentication not required in debug mode
        if($this->parent->config->get("site_mode") == "debug")
            return true;

        //Check given tokens
		if(!isset($_POST['token1']) OR !isset($_POST['token2']) OR !isset($_POST['token3']))
			return false;
		
		//Extract tokens
		$tokens = array(
			"token1"=>$_POST['token1'],
			"token2"=>$_POST['token2'],
			"token3"=>$_POST['token3']
		);

		//Check login tokens
		if(!$this->checkTokens($tokens))
			return false;

        //Else it is a success
        return true;
    }

    /**
     * Check given authentication tokens
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