<?php
/*
功能: 管理侧为商户修改提现费率
文件: ajax/change_fee.php
发起: js/admin.js: 824
验证: 是
输入: POST
{
	"pid": <pid>
}
输出:
{
	"flag": <flag>
}
<flag>
	0: 成功
	1: 身份错误（需管理侧）
	2: 整体输入格式错误
	3: 输入参数形式不合规
	4: 输入参数值形式不合规
	5: 输入参数值不存在于数据库
	6: 输入 pid 在数据库中存在多条记录（主键冲突）
	7: 新费率和旧费率一致
	8: 新费率越界，只能属于 (0, 100)
	9: 向数据库写入新 fee_rate 失败
*/
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
$res = [];
// 确认身份为 member
$identity = check_identity();
if ($identity != 'is_member' || !in_array($_SESSION['side'], $admin_side)){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
$input = file_get_contents('php://input');
if (empty($input) || $input == null){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST == null || empty($_POST)){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
if (count($_POST) != 2 || !isset($_POST['pid']) || !isset($_POST['new_fee_rate'])){
	$res['flag'] = 3;
	echo json_encode($res);
	exit;
}
$pid = $_POST['pid'];
if (!is_numeric($pid) || floor($pid) != ceil($pid) || $pid <= 0){
	$res['flag'] = 3;
	echo json_encode($res);
	exit;
}
$sql_old_fee_rate =
"SELECT
	`commission_fee_rate`
FROM
	`user`
WHERE
	`pid` = $pid;";
$mysql = connect();
$result_old_fee_rate = $mysql->query($sql_old_fee_rate);
if ($result_old_fee_rate->num_rows == 0){ // 无此 pid 
	$res['flag`'] = 5;
	echo json_encode($res);
	exit;
}
if ($result_old_fee_rate->num_rows > 1){ // 主键冲突
	$res['flag`'] = 6;
	echo json_encode($res);
	exit;
}
$old_fee_rate = $result_old_fee_rate->fetch_row()[0];
$new_fee_rate = $_POST['new_fee_rate'];
if ($old_fee_rate == $new_fee_rate){
	$res['flag'] = 7;
	echo json_encode($res);
	exit;
}
if ($new_fee_rate >= 1 || $new_fee_rate <= 0){ // 新费率越界
	$res['flag'] = 8;
	echo json_encode($res);
	exit;
}
// 执行 UPDATE commission_fee_rate
$sqlu_fee_rate =
"UPDATE
	`user`
SET
	`commission_fee_rate` = $new_fee_rate
WHERE
	`pid` = $pid";
$resultu_fee_rate = $mysql->real_query($sqlu_fee_rate);
if (!$resultu_fee_rate){
	$res['flag`'] = 9;
	echo json_encode($res);
	exit;
}
$res = [
	'flag' => 0,
	'new_fee_rate' => $new_fee_rate
];
echo json_encode($res);