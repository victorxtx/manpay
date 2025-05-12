<?php
/*
文件: ajax/reset_key.php
功能: 商家重置自己的商户密钥
类型: AJAX
发起: js/admin.js: 185
验证: 是
输入: GET
输出:
{
	"flag": 0,
	["time": <time>]
	"key": <key>
}
flag:
	1: neither
	2: 新 key 写入数据库出错
	3: time, 重置 key 有 5 分钟冷却时间，若时间在五分钟内，则返回 flag(3) 和 剩余时间
	4: is_customer
*/
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once '../lib/tools.php';
session_start();
$res = [];
$identity = check_identity();
if ($identity == 'is_customer'){
	$res['flag'] = 4;
	echo json_encode($res);
	exit;
}
else if ($identity == 'neither'){
	$res['flag'] = 1;
	echo json_encode($res['flag']);
	exit;
}
$conn = connect();
$sql_klast = "SELECT `key_last` FROM `user` WHERE `username` = '{$_SESSION['username']}';";
$result_klast = execute($conn, $sql_klast);
$timestamp_key_last = strtotime(mysqli_fetch_row($result_klast)[0]);
$now = time();
$time_diff = $now - $timestamp_key_last;
if ($time_diff < 300){
	$res['flag'] = 3;
	$res['time'] = 300 - $time_diff;
	echo json_encode($res);
	exit;
}
$key = '';
do{
	$key = create_secret(2, 'sha256');
	$sql_key_dup = "SELECT * FROM `user` WHERE `key` = '$key';";
	$result_key_dup = execute($conn, $sql_key_dup);
}
while ($result_key_dup->num_rows);

$sql_update_key = "UPDATE `user` SET `key` = '$key' WHERE `username` = '{$_SESSION['username']}';";
$result_update_key = @execute_bool($conn, $sql_update_key);
if ($result_update_key){
	$_SESSION['key'] = $key;
	$res['flag'] = 0;
	$res['key'] = $key;
	echo json_encode($res);
	exit;
}
$res['flag'] = 2;
echo json_encode($res);