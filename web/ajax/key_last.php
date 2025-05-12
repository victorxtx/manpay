<?php
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once '../lib/tools.php';
session_start();
$identity = check_identity();
$res = [];
if ($identity == 'is_customer'){
	$res['flag'] = 1;
	echo json_encode($res['flag']);
	exit;
}
$conn = connect();
$sql_key_last = "SELECT `key_last` FROM `user` WHERE `username` = '{$_SESSION['username']}';";
$result_key_last = execute($conn, $sql_key_last);
$key_last = mysqli_fetch_row($result_key_last)[0];
$time_diff = 300 - time() + strtotime($key_last);
$res['flag'] = 0;
$res['time_diff'] = $time_diff;
echo json_encode($res);