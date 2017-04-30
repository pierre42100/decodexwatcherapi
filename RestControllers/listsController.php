<?php
/**
 * Lists management controller
 *
 * @author Pierre HUBERT
 */

class listsController {

	/**
	 * Get the complete list
	 *
	 * @url GET /list/get/current
	 */
	public function getList(){
		
		//Try to get the current list
		if(!$list = DW::get()->lists->getCurrent())
			Rest_fatal_error(500, "Couldn't get current list !");

		//Return the list
		return $list;
	}
	

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