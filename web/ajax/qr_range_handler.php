<?php
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once '../lib/tools.php';
session_start();
$res = [];
$identity = check_identity();
switch($identity){
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
if (in_array($_SESSION['side'], $merch_side)){
	$res['flag'] = 'permission_deny';
	echo json_encode($res);
	exit;
}
$input = file_get_contents('php://input');
if (empty($input)){
	$res['code'] = 'input_empty';
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST == null || !is_array($_POST)){
	$res['code'] = 'input_illegal';
	echo json_encode($res);
	exit;
}
if (count($_POST) != 2){
	$res['code'] = 'elem_count_error';
	echo json_encode($res);
	exit;
}
$keys = array_keys($_POST);
if (!in_array([$keys[0], $keys[1]], [[0, 1], [2, 3], [4, 5]])){
	$res['code'] = 'index_illegal';
	echo json_encode($res);
	exit;
}
$range_index = intval(($keys[1] + 1) / 2);

// redis 不能根据 score 去更新 key，只能先根据 score 删除整个 member，再新增
try{
	$redis = redis_connect();
	$redis->select(REDIS_DBNM_QPAY);
	$res_zrem = @$redis->zRemRangeByScore(REDIS_ZKEY_AMOUNT_RANGE, $range_index, $range_index);
	$member['min'] = $_POST[$keys[0]];
	$member['max'] = $_POST[$keys[1]];
	$res_zadd = @$redis->zAdd(REDIS_ZKEY_AMOUNT_RANGE, $range_index, json_encode($member));
}
catch(Exception $e){
	$res['code'] = 'db_error';
	echo json_encode($res);
	exit;
}
$res['code'] = 'ok';
$res['data'] = $_POST;
echo json_encode($res, JSON_FORCE_OBJECT);