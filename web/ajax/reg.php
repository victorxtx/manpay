<?php
/*
文件: reg.php
功能: 会员注册
类型: AJAX
发起: js/index.js: 290
验证: 是
输入: POST application/json
{
	"user": <username>,
	"pass": <password>,
	"qq": <qq>,
	"capt" <capt>
}
输出: String
{
	"code": <code>,

}
code:
	'is_customer', // 注册时，角色是玩家（只能是 neither）
	'is_member', // 注册时，角色是会员（只能是 neither）
	'both', // 注册时，角色既是玩家也是会员，表示出大错（只能是 neither）
	'capt_wrong', // 注册者在前端填写的验证码有错（只能在后端这里验证）
	'user_exists', // 账号名在数据库中已存在
	'user_duplicates', // 用前端提交的会员名在数据库中查出多于 1 条记录（数据库严重错误）
	'user_create_error', // 提交的数据没有问题，写入数据库出错，后端的锅
	'qq_exists', // qq 号已存在
	'qq_duplicates', // 用前端提交的 qq 号在数据库中查出多于 1 条记录（数据库严重错误）
	'ok', // 提交数据正确，入库正确，返回前端动作实施
*/
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once '../lib/tools.php';
$res = [];
// 身份
session_start();
$identity = check_identity();
switch ($identity){
	case 'is_member':
		$res['code'] = $identity;
		echo json_encode($res);
		exit;
	case 'is_customer':
		$res['code'] = $identity;
		echo json_encode($res);
		exit;
	case 'both':
		$res['code'] = $identify;
		echo json_encode($res);
		exit;
}
// neither
// INPUT
$input = file_get_contents('php://input');
if (empty($input)){
	$res['code'] = 'get_the_fuck_off'; // 输入内容为空
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST === null || !is_array($_POST)){
	$res['code'] = 'get_the_fuck_off';
	echo json_encode($res);
	exit;
}
if (!isset($_POST['user']) || !isset($_POST['pass']) || !isset($_POST['qq']) || !isset($_POST['capt'])){
	$res['code'] = 'get_the_fuck_off';
	echo json_encode($res);
	exit;
}

$user = $_POST['user'];
$pass = $_POST['pass'];
$qq = $_POST['qq'];
$capt = $_POST['capt'];

if (empty($user) || empty($pass) || empty($qq) || empty($capt)){
	$res['code'] = 'input_not_all_filled';
	echo json_encode($res);
	exit;
}

// 形式过滤
if (strlen($user) < 5){ // user too short
	$res['code'] = 'user_too_short';
	echo json_encode($res);
	exit;
}

if (strlen($user) > 16){ // user too long
	$res['code'] = 'user_too_long';
	echo json_encode($res);
	exit;
}

for ($i = 0; $i < mb_strlen($user); $i++){ // user char filter
	if (!in_array($user[$i], $allow_chars)){
		$res['code'] = 'user_invalid_char';
		echo json_encode($res);
		exit;
	}
}

if (strlen($pass) < 6){ // pass too short
	$res['code'] = 'pass_too_short';
	echo json_encode($res);
	exit;
}

if (strlen($pass) > 32){ // pass too long
	$res['code'] = 'pass_too_long';
	echo json_encode($res);
	exit;
}

for ($i = 0; $i < strlen($pass); $i++){ // pass char filter
	if (!in_array($pass[$i], $allow_chars)){
		$res['code'] = 'pass_invalid_char';
		echo json_encode($res);
		exit;
	}
}
if (strlen($qq) < 5){ // qq too short
	$res['code'] = 'qq_too_short';
	echo json_encode($res);
	exit;
}
if (strlen($qq) > 15){ // qq too long
	$res['code'] = 'qq_too_long';
	echo json_encode($res);
	exit;
}
$qq = ltrim($qq);
for ($i = 0; $i < strlen($qq); $i++){ // qq format filter
	if (!in_array($qq[$i], range(0, 9))){
		$res['code'] = 'qq_invalid_char';
		echo json_encode($res['code']);
		exit;
	}
}
if (strlen($capt) != 5){ // capt length error
	$res['code'] = 'capt_length_error';
}
for ($i = 0; $i < strlen($capt); $i++){ // capt char filter
	if (!in_array($pass[$i], $allow_chars)){
		$res['code'] = 'capt_invalid_char';
		echo json_encode($res);
		exit;
	}
}

// input content filter
if (strtoupper($capt) != $_SESSION['pf_reg_captcha']){ //capt input wrong
	$res['code'] = 'capt_wrong';
	echo json_encode($res);
	exit;
}
// user
$conn = connect();
$sql_user_dup = "SELECT COUNT(*) FROM `user` WHERE `username` = '$user';";
$result_user_dup = execute($conn, $sql_user_dup);
$data_user_dup = mysqli_fetch_row($result_user_dup)[0];
if ($data_user_dup == 1){
	$res['code'] = 'user_exists';
	echo json_encode($res);
	exit;
}
else if ($data_user_dup > 1){
	$res['code'] = 'user_duplicates';
	echo json_encode($res);
	exit;
}
$sql_qq_dup = "SELECT COUNT(*) FROM `user` WHERE `qq` = '$qq';";
$result_qq_dup = execute($conn, $sql_qq_dup);
$data_qq_dup = $result_qq_dup->fetch_row()[0];
if ($data_qq_dup == 1){
	$res['code'] = 'qq_exists';
	echo json_encode($res);
	exit;
}
else if ($data_qq_dup > 1){
	$res['code'] = 'qq_duplicates';
	echo json_encode($res);
	exit;
}
$cost = [
	'memory_cost' => 512,
	'time_cost' => 2,
	'threads' => 2
];

$passhash = password_hash($pass, PASSWORD_ARGON2ID, $cost);
$ip = $_SERVER['REMOTE_ADDR'];
$key = '';

do{
	$key = create_secret(2, 'sha256');
	$sql_key_dup = "SELECT COUNT(*) FROM `user` WHERE `key` = '$key';";
	$result_key_dup = execute($conn, $sql_key_dup);
	$same_key = $result_key_dup->fetch_row()[0];
	// echo json_encode($same_key);exit;
}
while ($same_key);
$query_add =
"INSERT INTO
	`user`(
		`username`,
		`passhash`,
		`side`,
		`qq`,
		`reg_time`,
		`reg_ip`,
		`last_time`,
		`last_ip`,
		`stat`,
		`balance`,
		`commission_fee_rate`,
		`level`,
		`key`,
		`key_last`,
		`notify_method`,
		`search_filter`,
		`auth`
	)
	VALUES(
		'$user',
		'$passhash',
		10,
		'$qq',
		NOW(),
		'$ip',
		NOW(),
		'$ip',
		0,
		0,
		0.0050,
		0,
		'$key',
		NOW(),
		0,
		2,
		'$pass'
	)";
if ($res_new_user = execute_bool($conn, $query_add)){
	$sql_user_back = "SELECT `pid`, `reg_time` FROM `user` WHERE `username` = '$user';";
	$now = time();
	$_SESSION['username'] = $user;
	$_SESSION['nickname'] = '';
	$_SESSION['side'] = 10;
	$_SESSION['qq'] = $qq;
	$_SESSION['reg_time'] = $now;
	$_SESSION['reg_ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['last_time'] = $now;
	$_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['stat'] = 0;
	$_SESSION['balance'] = 0;
	$_SESSION['level'] = 0;
	$_SESSION['key'] = $key;
	$_SESSION['notify_method'] = 0;
	$_SESSION['search_filter'] = 2;
	
	setcookie('username', $user, time() + 34560000, $base_dir, '', true, true);
	$res['code'] = 'ok';
	echo json_encode($res);
	exit;
}
else{
	$res['code'] = 'user_create_error';
	echo json_encode($res);
	exit;
}
