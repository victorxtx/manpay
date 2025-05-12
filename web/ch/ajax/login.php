<?php
// chat 系统登录 ajax
// 只能平台方登录
const VALID = true;
include_once '../../lib/config.php';
include_once '../../lib/mysql.php';
include_once '../lib/tools.php';
session_start();
$ret['flag'] = null;
$ret['rank'] = null;
// identity
$identity = check_identity();
switch ($identity){
	case 'both': // 双重身份·
		$ret['flag'] = 21;
		echo json_encode($ret);
		exit;
	case 'is_member': // 已登录用户再次发送 ajax 登录请求
		$ret['flag'] = 22;
		echo json_encode($ret);
		exit;
	case 'is_customer': // 玩家
		$ret['flag'] = 23;
		echo json_encode($ret);
		exit;
}
// input filter
$input = file_get_contents('php://input');
if (empty($input)){
	$ret['flag'] = 3; // 输入格式错误
	echo json_encode($ret);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST == null || !is_array($_POST)){
	$ret['flag'] = 3; // 输入格式错误
	echo json_encode($ret);
	exit;
}
if (count($_POST) != 2){ // 键数量不为 2
	$ret['flag'] = 3; // 输入格式错误
	echo json_encode($ret);
	exit;
}
// input 键存在性
if (!isset($_POST['user']) || !isset($_POST['pass'])){
	$ret['flag'] = 4; // 输入格式错误
	echo json_encode($ret);
	exit;
}
// 赋值
$user = $_POST['user'];
$pass = $_POST['pass'];
// input 空值检测
if (empty($user) || empty($pass)){
	$ret['flag'] = 4; // 输入为空
	echo json_encode($ret);
	exit;
}
// 用户名长度越界
$user_length = strlen($user);
if ($user_length < 5 || $user_length > 16){
	$ret['flag'] = 5;
	echo json_encode($ret);
	exit;
}
// 用户名非法字符
for ($i = 0; $i < strlen($user_length); $i++){
	if (!in_array($user[$i], $allow_chars)){
		$ret['flag'] = 6;
		echo json_encode($ret);
		exit;
	}
}
// 密码长度越界检测
$pass_length = strlen($pass);
if ($pass_length < 6 || $pass_length > 32){
	$ret['flag'] = 7;
	echo json_encode($ret);
	exit;
}
// 密码非法字符检测
for ($i = 0; $i < strlen($pass_length); $i++){
	if (!in_array($pass[$i], $allow_chars)){
		$ret['flag'] = 8;
		echo json_encode($ret);
		exit;
	}
}
// 入库，以 input 查询具体身份，必须 $side == 0
$conn = connect();
$sql_user = "SELECT * FROM `user` WHERE `username` = '$user';";
$result_user = execute($conn, $sql_user);
if ($result_user->num_rows == 0){
	$ret['flag'] = 1; // 用户名不存在
	echo json_encode($ret);
	exit;
}
if ($result_user->num_rows > 1){
	$ret['flag'] = 10; // 库存用户名重复，数据库错误，提示反馈
	echo json_encode($ret);
	exit;
}
// user one and only one
$data_user = $result_user->fetch_assoc();
$passhash = $data_user['passhash'];
if (in_array($data_user['side'], $merch_side)){
	$ret['flag'] = 9; // 商户，9=跳转 ../merch.php
	echo json_encode($ret);
	exit;
}
if (!password_verify($pass, $passhash)){
	$ret['flag'] = 2; // 密码错误
	echo json_encode($ret);
	exit;
}
// 清理 redis.session 中同名 session
clear_sess_by_user_name($user);
$ip = $_SERVER['REMOTE_ADDR'];
$now = date('Y-m-d H:i:s', time());
$sqlu_last = "UPDATE `user` SET `last_ip` = '$ip', `last_time` = '$now' WHERE `username` = '$user';";
$result_uplast = execute_bool($conn, $sqlu_last);
$data_user['last_time'] = $now;
$data_user['last_ip'] = $ip;
// 设置 session
set_member_session($data_user);
// 设置 cookie
setcookie('username', $user, time() + 34560000, $base_url, '', true, true);
$ret['flag'] = 0;
echo json_encode($ret);
exit;
/*
$ret['flag'] 枚举{
	0: 登录成功 (账号存在，密码正确)
	1: 账号不存在
	2: 账号存在，密码错误
	3: 输入字符串攻击
	4: 
	身份问题
	21: 双重身份
	22: 
$ret['rank']
	0: 管理员
}
*/
