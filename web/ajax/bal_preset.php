<?php
/*
文件: ajax/bal_preset.php
发起: js/admin.js: 633
功能: 查询会员历史订单提交金额和已支付金额，弹出时以预设格式填充
方式: AJAX
输入: GET
{
	"pid": <pid>, // 因为触发时是在会员管理界面的表格内，所以可以获取到 pid
}
返回: 
	[
		"flag" => 0,
		"today_submit_origin": <num>,
		"today_payed_origin": <num>,
		"today_submit_actual": <num>,
		"today_payed_actual": <num>,
		"tomonth_submit_origin": <num>,
		"tomonth_payed_origin": <num>,
		"tomonth_submit_actual": <num>,
		"tomonth_payed_actual": <num>,
		"d3_submit_origin": <num>,
		"d3_payed_origin": <num>,
		"d3_submit_actual": <num>,
		"d3_payed_actual": <num>,
		"d7_...",
		...
		"d30_...",
		...
		"hyear_submit_origin": <num>,
		"hyear_payed_origin": <num>,
		"hyear_submit_actual": <num>,
		"hyear_payed_actual": <num>,
		"all_submit_origin": <num>,
		"all_payed_origin": <num>,
		"all_submit_actual": <num>,
		"all_payed_actual": <num>,
	]
*/
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
$res = [];
$identity = check_identity();
// echo $_SESSION['username'].'|'.$_COOKIE['username'];exit;
if ($identity != 'is_member') {
	$res['flag'] = 2;
	$res['info'] = '';
	echo json_encode($res);
	exit;
}
if (!in_array($_SESSION['side'], $admin_side)) {
	$res['flag'] = 2;
	$res['info'] = 'side>=10';
	echo json_encode($res);
	exit;
}
if (!isset($_GET['pid'])) {
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
$pid = $_GET['pid'];
if (!is_numeric($pid)) {
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
if (floor($pid) != ceil($pid)) {
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
$conn = connect();
$sql = "SELECT SUM(`money`) AS `today_submit_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) = TO_DAYS(NOW());";
$sql .= "SELECT SUM(`money`) AS `today_payed_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) = TO_DAYS(NOW()) AND `pay_status` = 1;";
$sql .= "SELECT SUM(`actual_amount`) AS `today_submit_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) = TO_DAYS(NOW());";
$sql .= "SELECT SUM(`actual_amount`) AS `today_payed_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) = TO_DAYS(NOW()) AND `pay_status` = 1;";

$sql .= "SELECT SUM(`money`) AS `tomonth_submit_origin` FROM `order` WHERE `pid` = $pid AND DATE_FORMAT(`order_place_time`, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m');";
$sql .= "SELECT SUM(`money`) AS `tomonth_payed_origin` FROM `order` WHERE `pid` = $pid AND DATE_FORMAT(`order_place_time`, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m') AND `pay_status` = 1;";
$sql .= "SELECT SUM(`actual_amount`) AS `tomonth_submit_actual` FROM `order` WHERE `pid` = $pid AND DATE_FORMAT(`order_place_time`, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m');";
$sql .= "SELECT SUM(`actual_amount`) AS `tomonth_payed_actual` FROM `order` WHERE `pid` = $pid AND DATE_FORMAT(`order_place_time`, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m') AND `pay_status` = 1;";

$sql .= "SELECT SUM(`money`) AS `d3_submit_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 3;";
$sql .= "SELECT SUM(`money`) AS `d3_payed_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 3 AND `pay_status` = 1;";
$sql .= "SELECT SUM(`actual_amount`) AS `d3_submit_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 3;";
$sql .= "SELECT SUM(`actual_amount`) AS `d3_payed_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 3 AND `pay_status` = 1;";

$sql .= "SELECT SUM(`money`) AS `d7_submit_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 7;";
$sql .= "SELECT SUM(`money`) AS `d7_payed_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 7 AND `pay_status` = 1;";
$sql .= "SELECT SUM(`actual_amount`) AS `d7_submit_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 7;";
$sql .= "SELECT SUM(`actual_amount`) AS `d7_payed_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 7 AND `pay_status` = 1;";

$sql .= "SELECT SUM(`money`) AS `d30_submit_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 30;";
$sql .= "SELECT SUM(`money`) AS `d30_payed_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 30 AND `pay_status` = 1;";
$sql .= "SELECT SUM(`actual_amount`) AS `d30_submit_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 30;";
$sql .= "SELECT SUM(`actual_amount`) AS `d30_payed_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 30 AND `pay_status` = 1;";

$sql .= "SELECT SUM(`money`) AS `hyear_submit_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 183;";
$sql .= "SELECT SUM(`money`) AS `hyear_payed_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 183 AND `pay_status` = 1;";
$sql .= "SELECT SUM(`actual_amount`) AS `hyear_submit_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 183;";
$sql .= "SELECT SUM(`actual_amount`) AS `hyear_payed_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 183 AND `pay_status` = 1;";

$sql .= "SELECT SUM(`money`) AS `all_submit_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 366;";
$sql .= "SELECT SUM(`money`) AS `all_payed_origin` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 366 AND `pay_status` = 1;";
$sql .= "SELECT SUM(`actual_amount`) AS `all_submit_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 366;";
$sql .= "SELECT SUM(`actual_amount`) AS `all_payed_actual` FROM `order` WHERE `pid` = $pid AND TO_DAYS(`order_place_time`) > TO_DAYS(NOW()) - 366 AND `pay_status` = 1;";


$res['flag'] = 0;
if ($conn->multi_query($sql)){
	do{
		if ($result = $conn->store_result()){
			$res = array_merge($res, $result->fetch_assoc());
			$result->free(); // 释放结果集内存
		}
		$conn->more_results();
	} while ($conn->next_result());
}
else{
	$res['flag'] = 0;
	$res['error'] = $conn->error;
	echo json_encode($res);
	exit;
}
foreach ($res as $key => $val){
	if ($val === null){
		$res[$key] = '0';
	}
}

echo json_encode($res);
/*
flag:
1: pid 形式错误
2: side 身份错误
3: 
*/