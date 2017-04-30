<?php
/**
 * Lists management controller
 *
 * @author Pierre HUBERT
 */

class listsController {

	/**
	 * Get the current list (complete)
	 *
	 * @url GET /list/get
	 */
	

	/**
	 * Update the current list
	 *
	 * @url POST /list/update
	 */
	public function updateList(){
		//Check given tokens
		if(!isset($_POST['token1']) OR !isset($_POST['token2']) OR !isset($_POST['token3']))
			Rest_fatal_error(401, "Tokens are required !");
		
		//Extract tokens
		$tokens = array(
			"token1"=>$_POST['token1'],
			"token2"=>$_POST['token2'],
			"token3"=>$_POST['token3']
		);

		//Check login tokens
		if(!DW::get()->auth->checkTokens($tokens))
			Rest_fatal_error(401, "Please check your tokens !");
		
		//Try to update list
		if(!DW::get()->list->update())
			Rest_fatal_error(500, "Couldn't update Decodex list !");
		
		//Else it is a success
		return array("success" => "This list was successfully updated !")
	} 
}