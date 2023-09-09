<?php

if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
	header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
	die ("Access Denied!");
}

// Constants

define("BASE_URL", "http://localhost/GitHub/task/Task%20A/");


define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('FCPATH', str_replace('includes', '', pathinfo(__FILE__)['dirname']));

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