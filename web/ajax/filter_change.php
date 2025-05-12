<?php
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once '../lib/tools.php';
session_start();
$identity = check_identity();
switch ($identity){
	case 'is_customer':
		$res['code'] = 'id_customer';
		echo json_encode($res);
		exit;
	case 'is_neither':
		$res['code'] = 'id_neither';
		echo json_encode($res);
		exit;
	case 'both':
		$res['code'] = 'id_both';
		echo json_encode($res);
		exit;
}
if (!isset($_GET['f'])){
	$res['code'] = 'no_arg';
	echo json_encode($res);
	exit;
}
$f = $_GET['f'];
if (!is_numeric($f) || !in_array($f, ['0', '1', '2'])){
	$res['code'] = 'arg_error';
	echo json_encode($res);
	exit;
}
$conn = connect();
$username = $_SESSION['username'];
$sql_filter = "SELECT `search_filter` FROM `user` WHERE `username` = '$username';";
$result_filter = execute($conn, $sql_filter);
$filter = $result_filter->fetch_row()[0];
if ($f == $filter){
	$res['code'] = 'value_same';
	echo json_encode($res);
	exit;
}
$sql_update_filter = "UPDATE `user` SET `search_filter` = $f WHERE `username` = '$username';";
$result_update_filter = execute_bool($conn, $sql_update_filter);
if ($result_update_filter){
	$_SESSION['search_filter'] = $f;
	$res['code'] = 'ok';
	echo json_encode($res);
	exit;
}
else{
	$res['code'] = 'db_error';
	$res['filter'] = $f;
	echo json_encode($res);
	exit;
}

