<?php
/**
 * 文件: ajax/qr_item_layout.php
 * 功能：前端拖动图片位置时布局改变，向后端数据库同步布局数据
 * 发起：js/qr.js Line:516 onmouseup 局部变量。释放鼠标后扫描前端布局，结构化后发到这里
 * 输入数据：dataLayout - 有序集合
 * [
 *   {
 *  	 "sequence": ,
 *     "filename",
 *     "method",
 *     "text",
 *     "comment",
 *     "range",
 *   },
 *   {
 *  	 "sequence": ,
 *     "filename",
 *     "method",
 *     "text",
 *     "comment",
 *     "range",
 *   }
 * ]
 * 逻辑：删除整个 REDIS_KEY_QR_IMGS，以 dataLayout 为准，遍历后重新填充数据库
 */
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
	$res['code'] = 'permission_deny';
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
if (!is_array($_POST)){
	$res['code'] = 'input_error';
	echo json_encode($res);
	exit;
}
try{
	$redis = redis_connect();
	$redis->select(REDIS_DBNM_QPAY);
	$redis->del(REDIS_ZKEY_QR_IMGS);
	foreach ($_POST as $value){
		$redis->zAdd(REDIS_ZKEY_QR_IMGS, 0, json_encode($value));
	}
}
catch(Exception $e){
	$res['code'] = 'db_error';
	echo json_encode($res);
	exit;
}
$res['code'] = 'ok';
echo json_encode($res);
// file_put_contents('Debug', var_export($_POST, true));
// echo json_encode($_POST);
