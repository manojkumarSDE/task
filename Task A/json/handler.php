<?php 

	session_start();

	if(empty($_SESSION['role_id'])){
		header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
		exit('Access Denied!');
	}

	if(isset($_GET['users'])){

		require_once '../includes/DataBase.php';

		$db = new DataBase;

		if(!$db->check_permission($_SESSION['role_id'], 'View Users')) {

			header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
			exit('Access Denied!');

		}

		echo json_encode($db->get_users(), JSON_NUMERIC_CHECK);
	}

?>