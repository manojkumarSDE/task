<?php

	require_once '../../includes/GeneralConfig.php';

	session_start();

	if(empty($_SESSION['role_id'])){
		header('location: ' . BASE_URL);
	}

    if($_SESSION['role_id'] !== 2){
        header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
        die ("Access Denied!");
    }

	if(isset($_GET['logout'])){
		session_destroy();
		header('Location: ' . BASE_URL);
	}

	if(!empty($_GET['deleteUser'])) {

		require_once '../../includes/DataBase.php';

		$db = new DataBase;

		if(!$db->check_permission($_SESSION['role_id'], 'Delete User')) {

			header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
			exit('Access Denied!');

		}

		if($db->delete_user($_GET['deleteUser'])){
			header('Location: ' . BASE_URL . 'manage/admin/?deleteUser=success');
		}else{
			header('Location: ' . BASE_URL . 'manage/admin/?deleteUser=failed');
		}

	}
?>