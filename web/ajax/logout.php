<?php
const VALID = true;
include '../lib/config.php';
// include '../lib/mysql.php';
include '../lib/tools.php';
$res = [];
if (empty($_COOKIE)){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
if (!isset($_COOKIE['oid']) && !isset($_COOKIE['username'])){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
session_start();
$redis = new Redis(
	[
		'host' => REDIS_SOCK,
		'port' => -1,
		'auth' => REDIS_AUTH
	]
);
$redis->select(REDIS_DBNM_SESS);
$identity = check_identity();
if ($identity == 'neither'){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
else if ($identity == 'both'){
	clear_customer();
	clear_member();
}
else if ($identity == 'is_customer'){
	clear_customer();
}
else if ($identity == 'is_member'){
	clear_member();
}
$redis->Del('PHPREDIS_SESSION:'.$_COOKIE['PHPSESSID']);
session_unset();
session_destroy();
session_create_id();
$res['flag'] = 0;
echo json_encode($res);
exit;
/*
0: 成功
1: COOKIE 为空 | cookie.oid cookie.username 没有一个存在
2: 无身份
*/