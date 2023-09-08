<?php

require_once 'GeneralConfig.php';

class DataBase extends GeneralConfig {

	public function __construct(){
		parent:: __construct();

		$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->databasename);

		if($this->conn->connect_error){
			die('Connection failed: '. $this->conn->connect_error);
		}

	}

	public function sanitize($data){
		return mysqli_real_escape_string($this->conn, $data);
	}

	public function login(){

		$username = $this->sanitize($_POST['username']);
		$password = $this->sanitize($_POST['password']);

		$smt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
		$smt->bind_param("s", $username);
		$smt->execute();
		$result = $smt->get_result();

		if($result->num_rows === 1){

			$row = $result->fetch_assoc();
			$pwd = $row['password'];

			if($pwd === md5($password)){
				return true;
			}
		}

		return false;

	}

	function file_upload($file_name) {

		if(!empty($_FILES[$file_name])){

		$allowed = array("image/jpg", "image/jpeg", "image/gif", "image/png");

		$file_type = $_FILES[$file_name]['type']; //returns the mimetype

		if(!in_array($file_type, $allowed)) {
			throw new Exception("Only jpg, jpeg, gif, and png files are allowed.");
		}

	    $path = './uploads/';
	    $file = $this->random_timestamp(). '.'.pathinfo($_FILES[$file_name]['name'], PATHINFO_EXTENSION);
	    $path = $path . $file;

	    if(move_uploaded_file($_FILES[$file_name]['tmp_name'], $path)) {
	      	return $file;
	    } else {
	        throw new Exception("There was an error uploading the file, please try again!");
	    }

		} else {
			throw new Exception($file_name . "No File Found");
		}

	}

	public function register(){
		$name = $this->sanitize($_POST['name']);
		$mobile = $this->sanitize($_POST['mobile']);
		$email = $this->sanitize($_POST['email']);
		$address = $this->sanitize($_POST['address']);
		$gender = $this->sanitize($_POST['gender']);
		$dob = $this->sanitize($_POST['dob']);

		$trans = [];

		try {

			$profilePicture = $this->file_upload('profilePicture');
			$signature = $this->file_upload('signature');

			$sm = $this->conn->prepare("INSERT INTO");

		}catch(Exception $e) {
			$trans[] = $e->getMessage();
		}

		if(!empty($log)){
			return $log;
		}else{
			return true;
		}

	}

	public function random_timestamp(){
		date_default_timezone_set('Asia/Kolkata');
 		return rand().'_'.date('Ymdhis', time());
	}

}

	$db = new DataBase();

	//echo '<pre>'; print_r($db->login('superadmin', '123'));
	echo '<pre>'; print_r($db->register());

?>