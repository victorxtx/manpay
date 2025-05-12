<?php
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once '../lib/tools.php';
$res['code'] = '';
session_start();
// 身份验证，必须是 neither
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
		$res['code'] = $identity;
		echo json_encode($res);
		exit;
}
// 过滤 input
$input = file_get_contents('php://input');
if (empty($input)){
	$res['code'] = 'get_the_fuck_off'; // 输入内容为空
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST === null || !is_array($_POST)){
	$res['code'] = 'get_the_fuck_off'; // 解构 $input 出错
	echo json_encode($res);
	exit;
}
if (count($_POST) != 3){
	$res['code'] = 'key_count_error'; // 键数量不为 3
	echo json_encode($res);
	exit;
}
if (!isset($_POST['user']) || !isset($_POST['pass']) || !isset($_POST['capt'])){ //三键存在性
	$res['code'] = 'key_name_mismatch';
	echo json_encode($res);
	exit;
}
$user = $_POST['user'];
$pass = $_POST['pass'];
$capt = $_POST['capt'];
// input format filter
if (empty($user) || empty($pass) || empty($capt)){ // 空值判定
	$res['code'] = 'input_not_all_filled';
	echo json_encode($res);
	exit;
}
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
for ($i = 0; $i < strlen($user); $i++){ // user char filter
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
if (strlen($capt) != 5){ // capt length error
	$res['code'] = 'capt_length_error';
}
for ($i = 0; $i < strlen($capt); $i++){ // capt char flter
	if (!in_array($pass[$i], $allow_chars)){
		$res['code'] = 'capt_invalid_char';
		echo json_encode($res);
		exit;
	}
}
// input content filter
if (strtoupper($capt) != $_SESSION['pf_login_captcha']){ //capt input wrong
	$res['code'] = 'capt_wrong';
	echo json_encode($res);
	exit;
}
$conn = connect();
$query_user = "SELECT * FROM `user` WHERE `username` = '$user';";
$result_user = execute($conn, $query_user);
if ($result_user->num_rows == 0){
	$res['code'] = 'user_inexistent';
	echo json_encode($res);
	exit;
}
else if ($result_user->num_rows > 1){
	$res['code'] = 'user_duplicates';
	echo json_encode($res);
	exit;
}
// user one and only one
$data = mysqli_fetch_assoc($result_user);
// stat
if ($data['stat'] == 1){
	$res['code'] = 'user_reserve';
}
else if ($data['stat'] == 2){
	$res['code'] = 'user_banned';
	echo json_encode($res);
	exit;
}
$passhash = $data['passhash'];
if (password_verify($pass, $passhash)){
	clear_sess_by_user_name($user);
	$ip = $_SERVER['REMOTE_ADDR'];
	$now = date('Y-m-d H:i:s', time());
	$sqlu_last = "UPDATE `user` SET `last_ip` = '$ip', `last_time` = '$now' WHERE `username` = '$user';";
	$result_uplast = execute_bool($conn, $sqlu_last);
	$data['last_time'] = $now;
	$data['last_ip'] = $ip;
	set_member_session($data);
	setcookie('username', $user, time() + 34560000, $base_dir, '', true, true);
	$res['code'] ='ok';
	$res['side'] = $data['side'];
	$res['username'] = $user;
	echo json_encode($res);
	exit;
}
else{
	$res['code'] = 'pass_wrong';
	echo json_encode($res);
	exit;
}
