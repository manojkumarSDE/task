<?php

if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
	header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
	die ("Access Denied!");
}

require_once 'GeneralConfig.php';

class DataBase extends GeneralConfig {

	public function __construct(){
		parent:: __construct();

		$this->connect = new mysqli($this->servername, $this->username, $this->password, $this->databasename);

		if($this->connect->connect_error){
			die('Connection failed: '. $this->connect->connect_error);
		}

	}

	public function sanitize($data){
		return mysqli_real_escape_string($this->connect, $data);
	}
 
	public function check_permission($role_id, $permission_name){
		$smt = $this->connect->prepare("SELECT count(1) FROM user_role_permissions WHERE role_id = ? AND permission_id = (SELECT permission_id FROM permissions WHERE permission_name like ?) ");
		$smt->bind_param("is", $role_id, $permission_name);

		$smt->execute();
		$result = $smt->get_result();

		if ($result->num_rows == 0) {
			return false; 
		}

		return true;
	}

	public function login(){

		try {

		$username = $this->sanitize($_POST['username']);
		$password = $this->sanitize($_POST['password']);

		$smt = $this->connect->prepare("SELECT * FROM users WHERE username = ?");
		$smt->bind_param("s", $username);
		$smt->execute();
		$result = $smt->get_result();

		if($result->num_rows === 1){

			$row = $result->fetch_assoc();
			$hashed_password = $row['password'];

			if(password_verify($password, $hashed_password)){

				if($row['role_id'] === 3 && $row['approval_status'] === 'Pending'){
					throw new Exception('Admin Approval Pending');
				}

				session_start();

				$_SESSION['id'] = $row['id'];
				$_SESSION['username'] = $row['username'];
				$_SESSION['role_id'] = $row['role_id'];

				switch($row['role_id']) {
					case 1:
					header('Location: ' . BASE_URL . 'manage/superadmin/');
					break;
					case 2:
					header('Location: ' . BASE_URL . 'manage/admin/');
					break;
					case 3:
					header('Location: ' . BASE_URL . 'manage/user/');
					break;
				}
			}
		}

			throw new Exception('Username or Password is Wrong');

		} catch (Exception $e) {
			return $e->getMessage();
		}

	}

	public function delete_user($id){

		$smt = $this->connect->prepare("DELETE FROM users WHERE role_id = 3 AND id = ?");
		$smt->bind_param("i", $id);

		if(!$smt->execute()){
			return false;
		}

		return true;

	}

	public function get_users(){

		$smt = $this->connect->prepare("SELECT * FROM users WHERE role_id = 3 ORDER BY id DESC");
		$smt->execute();
		$result = $smt->get_result();

		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function get_user($user_id){

	}

	public function file_upload($file_name) {

		if(!empty($_FILES[$file_name])){

	    $path = FCPATH.'uploads/';
	    $file = $this->random_timestamp(). '.'.pathinfo($_FILES[$file_name]['name'], PATHINFO_EXTENSION);
	    $path = $path . $file;

	    if(move_uploaded_file($_FILES[$file_name]['tmp_name'], $path)) {
	      	return $file;
	    } else {
	        throw new Exception("There was an error uploading the file, please try again!");
	    }

		} else {
			throw new Exception($file_name . " No File Found");
		}

	}

	public function loginFormValidation(){

		$errors = [];

	    $username = $_POST['username'];
	    if (empty($username)) {
	        $errors['username'] = 'Username is required';
	    }

	    $password = $_POST['password'];
	    if (empty($password)) {
	        $errors['password'] = 'Password is required';
	    }

	    return $errors;
	}

	public function registerFormValidation(){
		$errors = [];

	    $username = $_POST['username'];
	    if (empty($username)) {
	        $errors['username'] = 'Username is required';
	    }

	    $password = $_POST['password'];
	    if (empty($password)) {
	        $errors['password'] = 'Password is required';
	    }

	    $mobile = $_POST['mobile'];
	    if (empty($mobile)) {
	        $errors['mobile'] = 'Mobile is required';
	    } elseif (!preg_match('/^\d{10}$/', $mobile)) {
	        $errors['mobile'] = 'Invalid mobile number';
	    }

	    $email = $_POST['email'];
	    if (empty($email)) {
	        $errors['email'] = 'Email is required';
	    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	        $errors['email'] = 'Invalid email address';
	    }

	    $address = $_POST['address'];
	    if (empty($address)) {
	        $errors['address'] = 'Address is required';
	    }

	    $gender = $_POST['gender'];
	    if (empty($gender)) {
	        $errors['gender'] = 'Gender is required';
	    }

	    $dob = $_POST['dob'];
	    if (empty($dob)) {
	        $errors['dob'] = 'Date of Birth is required';
	    }

	    // File Validation

	    $allowed = array("image/jpg", "image/jpeg", "image/gif", "image/png");

	    $profilePicture = $_FILES['profilePicture'];
	    $signature = $_FILES['signature'];

	    if (empty($profilePicture['name'])) {
	        $errors['profilePicture'] = 'Profile Picture is required';
	    }		

		$file_type = $profilePicture['type'];

		if(!in_array($file_type, $allowed)) {
			$errors['profilePicture'] = 'Only jpg, jpeg, gif, and png files are allowed for Profile Picture';
		}

	    if (empty($signature['name'])) {
	        $errors['signature'] = 'Signature Picture is required';
	    }

		$file_type = $signature['type'];

		if(!in_array($file_type, $allowed)) {
			$errors['signature'] = 'Only jpg, jpeg, gif, and png files are allowed for Signature';
		}

	    	return $errors;

	}

	public function register(){
		try {

		$username = $this->sanitize($_POST['username']);
		$password = $this->sanitize($_POST['password']);
		$mobile = $this->sanitize($_POST['mobile']);
		$email = $this->sanitize($_POST['email']);
		$address = $this->sanitize($_POST['address']);
		$gender = $this->sanitize($_POST['gender']);
		$dob = $this->sanitize($_POST['dob']);

		$profile_picture = $this->file_upload('profilePicture');
		$signature = $this->file_upload('signature');

		$hash_pwd = password_hash($password, PASSWORD_DEFAULT);

		$smt = $this->connect->prepare("INSERT INTO users (username, password, role_id, mobile, email, address, gender, dob, profile_picture, signature, approval_status) VALUES (?, ?, 3, ?, ?, ?, ?, ?, '$profile_picture', '$signature', 'Pending') ");

		$smt->bind_param("sssssss", $username, $hash_pwd, $mobile, $email, $address, $gender, $dob);
		$smt->execute();

		if($smt->affected_rows == 0){
			throw new Exception('Failed to Save Data');
		}

		$smt->close();

		}catch(Exception $e) {
			return $e->getMessage();
		}

		return true;

	}

	public function random_timestamp(){
		date_default_timezone_set('Asia/Kolkata');
 		return rand().'_'.date('Ymdhis', time());
	}

}

	//$db = new DataBase();

	//echo '<pre>'; print_r($db->login('admin', '123'));
	//echo '<pre>'; print_r($db->get_users());
	//echo '<pre>'; print_r($db->delete_user(8));

?>