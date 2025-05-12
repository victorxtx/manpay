<?php
/*
功能:
	会员在订单页向商户网站手动发送发送支付成功通知数据包
	订单必须处于已支付状态，否则通知功能前后端全封锁
	管理侧可以执行通知任何商户的已支付订单
	商户仅能通知自己的已支付订单
文件: ajax/notify_handler.php
发起:
	js/admin.js: 496
	js/merch.js: 366
验证: 是
输入: GET 必有 / POST 必空
{
	"oid": <oid>
}
过程: 读取数据库订单数据，组装 8 + 2 参数，以商户自定的 METHOD 向商户网站设置的通知页发起通知请求
输出:
{
	"flag": <flag>,
	"merch_ret": <merch_ret>,
	"time": <time>,
	"notifier": <notifier>
}
<flag>
	8: 数据库写入错误
	7: xhr.get.oid 不唯一
	6: 商户被封禁
	5: 通知回包不是 SUCCESS
	4: 通知回包不是 string（几乎不可能）
	3: 当前cookie登录者与订单所有者不匹配
	2: 未登录（仅放行 is_member）
	1: 参数形式错误
	0: 返回成功
<merch_ret> 向商户网站发起请求，得到的回应字符串，返回前端弹窗显示
	"SUCCESS": 商户网站收到通知
<time>: 通知时间，返回前端更新订单表数据
<notifier>: 执行该通知操作的人，返回前端更新订单表数据
*/
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
$res = [];
// 确认身份为 member
$identity = check_identity();
if ($identity != 'is_member'){
	$res['flag'] = 2; // 仅放行 is_member
	echo json_encode($res);
	exit;
}
// 过滤 oid
if (!isset($_GET['oid']) || !is_numeric($_GET['oid'])){
	$res['flag'] = 1; // get.oid 形式错误
	echo json_encode($res);
	exit;
}
if (floor($_GET['oid']) != ceil($_GET['oid'])){
	$res['flag'] = 1; // get.oid 形式错误
	echo json_encode($res);
	exit;
}
$oid = $_GET['oid'];
$conn = connect();
// 查操作者信息
$name_operator = $_SESSION['username'];
$sql_operator =
"SELECT
	`pid`,
	`side`,
	`stat`,
	`key`,
	`notify_method`
FROM
	`user`
WHERE
	`username` = '$name_operator';";
$result_operator = execute($conn, $sql_operator);
$data_operator = $result_operator->fetch_assoc();
$pid_operator = $data_operator['pid']; // session.username 查到的 pid
$stat_operator = $data_operator['stat'];
$key_operator = $data_operator['key'];
$side_operator = $data_operator['side'];
$mkey = $key_operator;
$notify_method_operator = $data_operator['notify_method'];
if ($stat_operator != 0){
	$res['flag'] = 6; // pid stat error
	echo json_encode($res);
	exit;
}
// 查订单拥有者信息
$sql_order = "SELECT * FROM `order` WHERE `oid` = $oid;";
$result_order = execute($conn, $sql_order);
if ($result_order->num_rows != 1){
	$res['flag'] = 7; // get.oid 不唯一
	echo json_encode($res);
	exit;
}
$data_order = $result_order->fetch_assoc();
$pid_order = $data_order['pid'];

// 如果通知人不是订单所有人（而是管理员），则查询订单所有人信息，获取商户密钥
if ($pid_operator != $pid_order){
	$sql_owner = "SELECT `key` FROM `user` WHERE `pid` = '$pid_order';";
	$result_owner = execute($conn, $sql_owner);
	$data_owner = $result_owner->fetch_assoc();
	$mkey = $data_owner['key'];
}
if ($pid_operator != $pid_order && !in_array($side_operator, $admin_side)){ // 操作者不是订单所有人，同时操作者还不是管理侧，此逻辑很绕，用德摩根定律反复拆解理解
	$res['flag'] = 3; // 
	echo json_encode($res);
	exit;
}
$pay_status = $data_order['pay_status'];
if (!$pay_status){
	$res['flag'] = 9;
	echo json_encode($res);
	exit;
}
// assemble notify
$_NOTIFY = [
	'pid' => $data_order['pid'],
	'money' => number_format($data_order['money'] / 100, 2, '.', ''),
	'name' => $data_order['name'],
	'out_trade_no' => $data_order['out_trade_no'],
	'trade_no' => $data_order['trade_no'],
	'trade_status' => 'TRADE_SUCCESS',
	'type' => $data_order['type']
];
ksort($_NOTIFY);
$str_to_sign = '';
foreach ($_NOTIFY as $key => $value){
	$str_to_sign .= "$key=$value&";
}
$str_to_sign = substr($str_to_sign, 0, -1);
$sign = md5("$str_to_sign$mkey");
$_NOTIFY['sign'] = $sign;
$_NOTIFY['sign_type'] = 'MD5';
// do notify

if ($notify_method_operator == 0){
	$str_url = $data_order['notify_url'].'?';
	$str_url .= http_build_query($_NOTIFY);
	$curl_opt = [
		CURLOPT_URL => $str_url,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FOLLOWLOCATION => 1,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_TIMEOUT => $notify_url_detect_timeout_seconds,
	];
	// $curl = curl_init($str_get);
	$curl = curl_init();
	curl_setopt_array($curl, $curl_opt);
	$merch_ret = curl_exec($curl);
	curl_close($curl);
}
else if ($notify_method_operator == 1){
	$curl_opt = [
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_POSTFIELDS => $_NOTIFY,
		CURLOPT_TIMEOUT => $notify_url_detect_timeout_seconds,
	];
	$curl = curl_init($data_order['notify_url']);
	curl_setopt_array($curl, $curl_opt);
	$merch_ret = curl_exec($curl);
	curl_close($curl);
}
// $res['data'] = $_NOTIFY;
// echo json_encode($res);
// 处理返回
if (gettype($merch_ret) != 'string'){
	$res['flag'] = 4;
	echo json_encode($res);
	exit;
}
if (strtoupper($merch_ret) != 'SUCCESS'){
	$res['flag'] = 5;
	$res['data'] = $merch_ret;
	echo json_encode($res);
	exit;
}
// 返回成功
$res['flag'] = 0;
$res['time'] = date('Y-m-d H:i:s', time());
$res['notifier'] = $pid_operator;
echo json_encode($res);

$sql_set_notify = "UPDATE `order` SET `notify_status` = 1, `notify_time` = NOW(), `notifier` = $pid_operator WHERE `oid` = $oid;";
$result_set_notify = execute($conn, $sql_set_notify);
if (!$result_set_notify){
	$res['flag'] = 8;
	echo json_encode($res);
	exit;
}
