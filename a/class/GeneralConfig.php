<?php

class GeneralConfig {

	public $servername;
	public $username;
	public $password;
	public $database;

	protected function __construct(){
		$this->servername = 'localhost';
		$this->username = 'root';
		$this->password = '';
		$this->databasename = 'user_management';
	}

}
?>