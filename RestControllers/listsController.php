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
		
		//Authentication required (protected method)
		if(!DW::get()->auth->restAuth())
			Rest_fatal_error(401, "Authentication required !");
		
		//Try to update list
		if(!DW::get()->lists->update())
			Rest_fatal_error(500, "Couldn't update Decodex list !");
		
		//Else it is a success
		return array("success" => "This list was successfully updated !");
	} 
}