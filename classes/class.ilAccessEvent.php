<?php

/*
 * Access Event:
 * 
 * This class discribes a read or write event from a user
 * 
 * 
 */

class ilAccessEvent
{
	public $obj_id;
	public $usr_id;
	public $timestamp;
	
	public function __construct($obj_id, $timestamp, $user)
	{
		$this->obj_id = $obj_id;
		$this->timestamp = $timestamp;
		$this->usr_id = $user;		
	}
	
}

?>