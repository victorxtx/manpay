<?php
/*
文件: ajax/pay_handler.php
功能: 管理侧手动设置订单"已支付"状态
类型: AJAX
发起: js/admin.js: 424
验证: 是
输入: GET
{
	"oid": <oid> // oid=order id
}
oid:
	要设为"已支付"状态的订单号
输出:
{
	"flag": <flag>
}
<flag>:
	0: 支付状态设置成功
		将同时返回额外两个字段
		"time": YYYY-mm-dd HH:ii:ss
		"payer": <pid>
	1: get.oid 格式错误
	2: 未登录，无身份
	3: pid 身份无权限操作当前订单
	4: 订单已经支付，却还发来执行支付要求
	5: 设置支付状态sql执行错误
	6: 登录者账号状态异常
	7: 订单表中 oid 不存在或超过一个
*/
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
$res = [];
$identity = check_identity();
if ($identity != 'is_member'){
	$res['flag'] = 2; // 仅放行 is_member
	echo json_encode($res);
	exit;
}
if (in_array($_SESSION['side'], $merch_side)){ // 不是普通会员
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
if (!isset($_GET['oid']) || !is_numeric($_GET['oid'])){
	$res['flag'] = 1; // get.oid 形式错误
	echo json_encode($res);
	exit;
}
$oid = $_GET['oid'];
if (floor($oid) != ceil($oid)){
	$res['flag'] = 1; // get.oid 形式错误
	echo json_encode($res);
	exit;
}
$mysql = connect();
// 查操作者信息（session user
$username = $_SESSION['username'];
$sql_operator =
"SELECT
	`pid`,
	`side`,
	`stat`,
	`notify_method`
FROM
	`user`
WHERE
	`username` = '$username';";
$result_operator = execute($mysql, $sql_operator);
$data_operator = $result_operator->fetch_assoc();
$pid_operator = $data_operator['pid']; // session.username 查到的 pid
$username_operator = $_SESSION['username'];
$side_operator = $data_operator['side'];
$stat_operator = $data_operator['stat'];
$notify_method_operator = $data_operator['notify_method'];
if ($stat_operator != 0){
	$res['flag'] = 6; // pid stat error
	echo json_encode($res);
	exit;
}
// 用 oid 查订单
$sql_order =
"SELECT
	*
FROM
	`order`
WHERE
	`oid` = $oid;";
$result_order = $mysql->query($sql_order);
if ($result_order->num_rows != 1){
	$res['flag'] = 7; // get.oid 不唯一
	echo json_encode($res);
	exit;
}
// 当前要支付订单的全部数据在此
$data_order = $result_order->fetch_assoc();
$pid_order = $data_order['pid']; // 从 get.oid 查到的订单拥有者 pid
if (!in_array($side_operator, $admin_side)){ // 这是支付操作，操作者必须是管理侧
	$res['flag'] = 3;
	echo json_encode($res);
	exit;
}
$return_url = $data_order['return_url'];
$pay_status = $data_order['pay_status'];
$commission = $data_order['commission_fee_rate_actual'];
if ($pay_status == 1){
	$res['flag'] = 4;
	echo json_encode($res);
	exit;
}
// $customer = $data_order['customer'];
// 用 pid 查出的 pid_order 再查商户的 secret_key
$sql_merch =
"SELECT
	`username`,
	`key`,
	`commission_fee_rate`
FROM
	`user`
WHERE
	`pid` = $pid_order;";
$result_merch = $mysql->query($sql_merch);
$data_merch = $result_merch->fetch_row();
$merch_name = $data_merch[0];
$mkey = $data_merch[1];
$commission = $data_merch[2];
// 聊天界面 - Start
// $redis = new Redis(
// 	[
// 		'host' => REDIS_SOCK,
// 		'port' => -1,
// 		'auth' => REDIS_AUTH
// 	]
// );
// $redis->select(REDIS_DBNM_CHAT);
// $time = microtime(true);
// $time = str_pad(str_replace('.', '', $time), 14, '0', STR_PAD_RIGHT);
// $args = [
// 	'pid' => $pid_order,
// 	'money' => number_format($data_order['money'] / 100, 2, '.', ''),
// 	'name' => $data_order['name'],
// 	'out_trade_no' => $data_order['out_trade_no'],
// 	'trade_no' => $data_order['trade_no'],
// 	'trade_status' => 'TRADE_SUCCESS',
// 	'type' => $data_order['type']
// ];
// $str_to_sign = '';
// foreach ($args as $key => $value){
// 	$str_to_sign .= "$key=$value&";
// }
// $str_to_sign = substr($str_to_sign, 0, -1);
// $sign = md5("$str_to_sign$mkey");
// $args['sign'] = $sign;
// $args['sign_type'] = 'MD5';
// $str_args = http_build_query($args);

// $message_to_redis = [
// 	'who' => '系统通知',
// 	'rank' => 0,
// 	'ip' => '127.0.0.1',
// 	'type' => 'text',
// 	'content' => "【系统通知】亲爱的玩家，您的订单已支付。您可以点击这个链接 <a href=\"$return_url?$str_args\">返回商城</a> 查看支付结果。感谢使用本系统。"
// ];
// $redis->zAdd("$merch_name:$customer", $time, json_encode($message_to_redis));
// 聊天界面 - End
// 一切正常，写入支付状态
$sqlu_set_pay =
"UPDATE
	`order`
SET
	`pay_status` = 1,
	`pay_time` = NOW(),
	`payer` = $pid_operator
WHERE
	`oid` = $oid;";
$bool_set_pay = $mysql->real_query($sqlu_set_pay);
// 增加余额
$delta_amount = $data_order['money'] * (1 - $commission);
$sqlu_add_balance =
"UPDATE
	`user`
SET
	`balance` = `balance` + $delta_amount
WHERE
	`pid` = $pid_order;";
$bool_add_balance = $mysql->real_query($sqlu_add_balance);
if ($bool_set_pay && $bool_add_balance){
	$res['flag'] = 0;
	$res['time'] = date('Y-m-d H:i:s', time());
	$res['payer'] = $pid_operator;
	echo json_encode($res);
}
else{
	$res['flag'] = 5;
	echo json_encode($res);
	exit;
}