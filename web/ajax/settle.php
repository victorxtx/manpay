<?php
/*
文件: ajax/settle.php
功能: 手动结算。平台管理员侧把收到的钱转给商家，然后手动在此减去余额
类型: AJAX
发起: js/admin.js 938
验证: 是
输入: POST
{
	pid: <pid>, // 要给哪个 pid 结算
	balance_front: <balance_front>,
	input_settle: <input_settle_value>
}
输出:
{
	"flag": <flag>
	"new_balance": <new_balance>
}
<flag>
	1: 输入参数(字符串)为空、格式、键的数量、名称不合规
	2: 身份错误。需要 is_member 且处于管理侧
	3: 参数的值形式上不合规
	4: 前端余额与后端不一致
	5: 数据库写入出错
	0: 正常，携带 "new_balance" 字段
逻辑:
	balance_front 与数据库中比对
*/

const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
$res = [];
$identity = check_identity();
if ($identity != 'is_member'){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
if (in_array($_SESSION['side'], $merch_side)){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
// 输入的外层逻辑性
$input = file_get_contents('php://input');
if (empty($input)){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if (!is_array($_POST)){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
if (count($_POST) != 3){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
if (!isset($_POST['pid']) || !isset($_POST['balance_front']) || !isset($_POST['input_settle'])){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
$pid = $_POST['pid'];
$balance_front = $_POST['balance_front'];
$input_settle = $_POST['input_settle'];
// 输入的内层合法性
if (!is_numeric($pid) || floor($pid) != ceil($pid) || $pid <= 0){
	$res['flag'] = 3;
	$res['code'] = 'pid_format_error';
	echo json_encode($res);
	exit;
}
if (!is_numeric($input_settle)){
	$res['flag'] = 3;
	$res['code'] = 'balance_front_format_error';
	echo json_encode($res);
	exit;
}
if (!is_numeric($input_settle)){
	$res['flag'] = 3;
	$res['code'] = 'input_settle_format_error';
	echo json_encode($res);
	exit;
}
// 输入的值的逻辑合法性
// 结算值不能为 0，正负都可以，甚至超过余额都可以
if ($input_settle == 0){
	$res['flag'] = 4;
	$res['code'] = 'settle_eq_zero';
	echo json_encode($res);
	exit;
}
// 查询 user
$sql =
"SELECT
	`stat`,
	`balance`
FROM
	`user`
WHERE
	`pid` = $pid;";
$mysql = connect();
$result_user = $mysql->query($sql);
$data_user = $result_user->fetch_row();
$stat = $data_user[0];
$balance = $data_user[1];
if ($balance_front * 100 != $balance){
	$res['flag'] = 4;
	$res['code'] = 'balance_not_match';
	echo json_encode($res);
	exit;
}
// $input_settle 为正是正常结算，为负是管理侧手动为商户增加余额
$settle_add = $input_settle * 100;
$new_balance = $balance - $settle_add;

$sqlu_new_balance =
"UPDATE
	`user`
SET
	`balance` = $new_balance
WHERE
	`pid` = $pid;";
$res_new_balance = $mysql->real_query($sqlu_new_balance);
if (!$res_new_balance){
	$res['flag'] = 5;
	echo json_encode($res);
	exit;
}
$res['flag'] = 0;
$res['new_balance'] = number_format($new_balance, 2, '.', '');
echo json_encode($res);
// 增加结算记录
$sql_new_settle =
"INSERT INTO
	`settle`(
		`time`,
		`before_balance`,
		`amount`,
		`after_balance`,
		`method`,
		`remark`,
		`operator`
	)
	VALUES(
		NOW(),
		$balance,
		$input_settle * 100,
		$new_balance * 100,
		'bank',
		'',
		$pid
	)";
$res_new_settle = @$mysql->real_query($sql_new_settle);
if (!$res_new_balance){
	$time = date("Y-m-d H:i:s", time());
	file_put_contents('../logs/settle.log', "[$time] 向 `settle` 表写入新记录失败。检查数据库运行状态、连接通路。", FILE_APPEND);
}