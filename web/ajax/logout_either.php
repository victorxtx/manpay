<?php // 接收 who.php->who.js的路由
const VALID = true;
include_once 'lib/config.php';
include_once 'lib/mysql.php';
include_once 'lib/tools.php';
session_start();
$identity = check_identity();
// 本页面作用是当两个身份都登录时，强制登出其中一个，或都登出
if (!is_login() || !is_customer()){ // 只要有一个不存在
	echo 1; // 攻击，弹走
	exit;
}
if (!isset($_GET['who'])){
	echo 2; // 攻击，弹走
	exit;
}
if (!in_array($_GET['who'], ['0', '1', '2'])){
	echo 3; // 攻击，弹走
	exit;
}
// 同时通过存在性，则 cookie session 都存在
$who = $_GET['who'];
if ($who == 1){ // who == 1 登出 cust
	setcookie('oid', '', 1, $base_dir, '', true, true);
	clear_customer_session();
	echo 0;
}
else if ($who == 0){ // who == 0 登出 merch
	setcookie('username', '', 1, $base_dir, '', true, true);
	clear_member_session();
	echo 0;
}
else if ($who == 2){ // 全部登出
	// $redis = redis_connect();
	// $redis = new Redis(
	// 	[
	// 		'host' => REDIS_SOCK,
	// 		'port' => -1,
	// 		'auth' => REDIS_AUTH
	// 	]
	// );
	// $redis->select(REDIS_DBNM_SESS);
	// $redis->del('PHPREDIS_SESSION:'.$_COOKIE['PHPSESSID']);
	session_destroy();
	session_unset();
	foreach ($_COOKIE as $name => $value){
		setcookie($name, '', 0, $base_dir);
	}
	session_create_id();
	echo 0;
}