<?php
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
switch ($identity){
case 'is_customer':
	$res['code'] = 'customer';
	echo json_encode($res);
	exit;
case 'is_neither':
	$res['code'] = 'neither';
	echo json_encode($res);
	exit;
case 'both':
	$res['code'] = 'both';
	echo json_encode($res);
	exit;
}
if (!empty($_GET)){
	$res['code'] = 'get_not_null';
	echo json_encode($res);
	exit;
}
$input = file_get_contents('php://input');
if (empty($input)){
	$res['code'] = 'post_null';
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST === false){
	$res['code'] = 'post_malformat';
	echo json_encode($res);
	exit;
}
if (count($_POST) != 3){
	$res['code'] = 'arg_count_error';
	echo json_encode($res);
	exit;
}
$input_keys = array_keys($_POST);
foreach ($input_keys as $input_key){
	if (!in_array($input_key, ['filename', 'method', 'layout'])){
		$res['code'] = 'arg_name_error';
		echo json_encode($res);
		exit;
	}
}
$filename = $_POST['filename'];
$method = $_POST['method'];
$path = "../img/qr-files/$method/$filename";
if (!file_exists($path)){
	$res['code'] = 'file_not_exists';
	echo json_encode($res);
	exit;
}
$result = unlink($path);
if (!$result){
	$res['code'] = 'del_error';
	echo json_encode($res);
	exit;
}
$res['code'] = 'del_ok';
// 删除成功，开始调整数据库 range 内 sequence
// $res['code'] 表示删除状态
// 若 $res['code'] == del_error，会结束执行，返回前端
// 若 $res['code'] == 'del_ok'，表示删除成功，继续修改 redis，
// 对 redis 的修改结果填充 $res['db_status']
$dataLayout = $_POST['layout'];
try{
	$redis = redis_connect();
	$redis->select(REDIS_DBNM_QPAY);
	$redis->del(REDIS_ZKEY_QR_IMGS);
	foreach ($dataLayout as $value){
		$redis->zAdd(REDIS_ZKEY_QR_IMGS, 0, json_encode($value));
	}
}
catch(Exception $e){
	$res['db_status'] = 'db_error';
	echo json_encode($res);
	exit;
}
$res['db_status'] = 'db_ok';
echo json_encode($res);
exit;