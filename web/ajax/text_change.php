<?php
/*
文件: ajax/text_change.php
功能: 管理员侧修改二维码的"内部备注文本"和"外部须知文本"
类型: AJAX
发起: admin.php 中引用
	js/qr.js: 60
	js/qr.js: 171
验证: 是
输入：
	方式			参数名		值范围								含义
	GET				c					[0, 1]								0=text_out, 1=text_in
	POST			filename	字符串
	POST			method		['wxpay', 'alipay', 'huabei']		微信,支付宝
	POST			text			字符串								前端发来的要更新的字符串
备注: 此页面接收必须的 GET 参数, 同时必须的 POST 参数，两者都要有
*/
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
if (!isset($_GET['c'])){
	$res['code'] = 'arg_missing';
	echo json_encode($res);
	exit;
}
if (!in_array($_GET['c'], [0, 1])){
	$res['code'] = 'arg_error';
	echo json_encode($res);
	exit;
}
// echo json_encode($_GET);
$input = @file_get_contents('php://input');
if (empty($input)){
	$res['code'] = 'arg_missing';
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST == false){
	$res['code'] = 'arg_error';
	echo json_encode($res);
	exit;
}
if (count($_POST) != 3){
	$res['code'] = 'arg_error';
	echo json_encode($res);
	exit;
}
if (!isset($_POST['filename']) || !isset($_POST['method']) || !isset($_POST['text'])){
	$res['code'] = 'arg_missing';
	echo json_encode($res);
	exit;
}
if (!in_array($_POST['method'], $allow_pay_methods)){
	$res['code'] = 'arg_error';
	echo json_encode($res);
	exit;
}

$c = $_GET['c'];
$filename = $_POST['filename'];
$method = $_POST['method'];
$text = $_POST['text'];
// redis 取出全部
try{
	$redis = redis_connect();
	$redis->select(REDIS_DBNM_QPAY);
	$qr_org = $redis->zRange(REDIS_ZKEY_QR_IMGS, 0, -1);
}
catch(Exception $err){
	$res['code'] = 'server_error';
	echo json_encode($res);
	exit;
}

foreach ($qr_org as $key => $member){
	$member = json_decode($member, true);
	if ($filename == $member['filename'] && $method == $member['method']){
		if ($c == 0){
			$member['text'] = $text;
		}
		else{
			$member['comment'] = $text;
		}
	}
	$qr_org[$key] = $member;
}
try{
	$redis->del(REDIS_ZKEY_QR_IMGS);
	foreach ($qr_org as $member){
		$redis->zAdd(REDIS_ZKEY_QR_IMGS, 0, json_encode($member));
	}
}
catch(Exception $err){
	$res['code'] = 'server_error';
	echo json_encode($res);
	exit;
}
$res['code'] = 'ok';
$res['filename'] = $filename;
$res['method'] = $method;
$res['text'] = $text;
echo json_encode($res);
/*
id_customer
id_neither
id_both
arg_missing
arg_error

*/