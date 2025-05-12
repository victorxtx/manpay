<?php
const VALID = true;
include_once 'lib/config.php';
include_once 'lib/mysql.php';
include_once 'lib/tools.php';

if (empty(REDIS_HOST) || empty(REDIS_AUTH) || empty(REDIS_DBNM_QPAY)){
	echo '请先配置 lib/config.php 的 REDIS_* 项';
	exit;
}
if (empty(MYSQL_HOST) || empty(MYSQL_USER) || empty(MYSQL_PASS) || empty(REDIS_DBNM_QPAY)){
	echo '请先配置 lib/config.php 的 MYSQL_* 项';
	exit;
}

$redis = new Redis([
	'host' => REDIS_SOCK,
	'port' => -1,
	'auth' => REDIS_AUTH,
]);
$redis->select(REDIS_DBNM_QPAY);
// 初始化 REDIS amount_range
$redis->select(REDIS_DBNM_QPAY);
if (!$redis->exists(REDIS_ZKEY_AMOUNT_RANGE)){
	$redis->zAdd(REDIS_ZKEY_AMOUNT_RANGE, 1, '{"min":1,"max":100}');
	$redis->zAdd(REDIS_ZKEY_AMOUNT_RANGE, 2, '{"min":101,"max":1000}');
	$redis->zAdd(REDIS_ZKEY_AMOUNT_RANGE, 3, '{"min":1000,"max":10000}');
}

$time = time();
// exit;
// $old_time = $redis->lRange($_SERVER['REMOTE_ADDR'], 0, -1);
// $redis->rPush($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_TIME']);
// $redis->lTrim($_SERVER['REMOTE_ADDR'], -10, -1);
// dump($old_time);
// exit;
session_start();
$identity = check_identity();

if ($identity == 'is_customer'){
	echo '<script type="text/javascript">location.replace("customer.php");</script>';
	exit;
}
if ($identity == 'both'){
	echo '<script type="text/javascript">location.replace("who.php");</script>';
	exit;
}
// index.php 允许仅成员、无身份 'is_member' | 'neither'
$login = is_login();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<link rel="stylesheet" type="text/css" href="css/public.css">
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<link rel="stylesheet" type="text/css" href="css/alert.css">
	<link rel="icon" href="img/rmb.svg">
	<title>戏台子</title>
</head>
<body>
	<?php
	if (!$login){
	?>
	<div class="login-popup">
		<h2 class="login-title">登 录</h2>
		<div class="input-wrap">
			<label class="input-label">账号名</label>
			<input class="login-input login-input-user" type="text" maxlength="20" placeholder="字母、数字、下划线，5~16位" oninput="value=value.replace(/[\W]/g,'')" onkeydown="if(event.keyCode==13){loginSubmitDo()}" required>
		</div>
		<div class="input-wrap">
			<label class="input-label">密 码</label>
			<input class="login-input login-input-pass" type="text" maxlength="32" placeholder="字母、数字、下划线，6~32位" oninput="value=value.replace(/[\W]/g,'')" onkeydown="if(event.keyCode==13){loginSubmitDo()}" required>
		</div>
		<div class="input-wrap">
			<label class="input-label">验证码</label>
			<input class="login-input login-input-capt" type="text" maxlength="5" placeholder="5位" oninput="value=value.replace(/[\W]/g,'')" onkeydown="if(event.keyCode==13){loginSubmitDo()}" required>
		</div>
		<div class="input-wrap">
			<div class="captcha-show captcha-show-login"></div>
		</div>
		<div class="input-wrap">
			<div class="login-submit">提 交</div>
		</div>
	</div>
	<div class="reg-popup">
		<h2 class="reg-title">注 册</h2>
		<div class="input-wrap">
			<label class="input-label">账号名</label>
			<input class="reg-input reg-input-user" type="text" maxlength="20" placeholder="字母、数字、下划线，5~16位" oninput="value=value.replace(/[\W]/g,'')" onkeydown="if(event.keyCode==13){regSubmitDo()}" required>
		</div>
		<div class="input-wrap">
			<label class="input-label">密 码</label>
			<input class="reg-input reg-input-pass" type="text" maxlength="32" placeholder="字母、数字、下划线，6~32位" oninput="value=value.replace(/[\W]/g, '')" onkeydown="if(event.keyCode==13){regSubmitDo()}" required>
		</div>
		<div class="input-wrap">
			<label class="input-label">QQ 号</label>
			<input class="reg-input reg-input-qq" type="text" maxlength="15" placeholder="5~15位QQ号" onkeydown="if(event.keyCode==13){regSubmitDo()}" required>
		</div>
		<div class="input-wrap">
			<label class="input-label">验证码</label>
			<input class="reg-input reg-input-capt" type="text" maxlength="5" placeholder="5位，大小写随意" oninput="value=value.replace(/[\W]/g,'')" onkeydown="if(event.keyCode==13){regSubmitDo()}" required>
		</div>
		<div class="input-wrap">
			<div class="captcha-show captcha-show-reg"></div>
		</div>
		<div class="input-wrap">
			<div class="reg-submit">提 交</div>
		</div>
	</div>
	<?php
	}
	?>
	<div class="header">
		<div class="logo">戏台子</div>
		<?php
		if ($login){ // logged
		?>
		<div class="btn-auth btn-welcome">
			<span class="greeting">欢迎回来</span>
			<br>
			<span class="call-name"><?php echo $_SESSION['username'];?></span>
		</div>
		<div class="btn-logout">退 出</div>
		<div class="btn-auth btn-admin">后台管理</div>
		<?php
		}
		else{ // not logged
		?>
		<div class="btn-auth btn-login">登 录</div>
		<div class="btn-auth btn-reg">注 册</div>
		<?php
		}
		?>
	</div>
	<?php
	// dump($_SERVER);
	// dump(check_identity());
	?>
	<script type="text/javascript" src="js/index.js"></script>
</body>
</html>