<?php
/*
文件: ajax/notify_method_change.php
功能: 会员在后台修改支付成功时的通知方式
类型: AJAX
发起:
	js/admin.js: 237
	js/merch.js: 175
验证: 是
输入: GET
{
	"method": <method>
}
method:
	0: GET
	1: POST
输出:
{
	"flag": <flag>,
}
flag:
	0: 成功
	1: 身份错误, 需要会员
	2: 数据库写入时, 影响行数不是 1, 表示更新动作落空了
	3: 数据库写入时, 写入行为出错
*/

$res = [];
$method = $_GET['method'];
if (!in_array($method, ['0', '1'])){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once '../lib/tools.php';
session_start();
$identity = check_identity();
if ($identity != 'is_member'){
	$res['flag'] = 1;
	echo json_encode($res['flag']);
	exit;
}

$user = $_SESSION['username'];
$conn = connect();
$sql_update_method =
"UPDATE
	`user`
SET
	`notify_method` = $method
WHERE
	`username` = '$user'
AND
	`notify_method` <> $method;";
$result_update_method = execute($conn, $sql_update_method);
if (!$result_update_method){ // update fail
	$res['flag'] = 3; // db error
	echo json_encode($res);
	exit;
}
if ($conn->affected_rows == 1){ //affected_rows
	$_SESSION['notify_method'] = $method;
	$res['flag'] = 0; // ok
	echo json_encode($res);
	exit;
}
else{ // 0
	$res['flag'] = 2; // affected == 0
	echo json_encode($res);
	exit;
}