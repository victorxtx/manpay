<?php
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once 'lib/tools.php';
session_start();
$identity = check_identity();
switch ($identity){
	case 'both':
		echo '<script type="text/javascript">location.replace("../who.php");</script>';
		exit;
	case 'is_member':
		$side = $_SESSION['side'];
		if (in_array($side, $merch_side)){ // 商家，弹走
			echo '<script type="text/javascript">location.replace("../merch.php");</script>';
		}
		else if (in_array($side, $admin_side)){ // 管理员，进入聊天
			echo '<script type="text/javascript">location.replace("../service.php");</script>';
		}
		exit;
	case 'is_customer': // 充值者
		echo '<script type="text/javascript">location.replace("chat.php");</script>';
		exit;
	default: // 没有身份，允许使用下方 html 登录
		break;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-tial-scale=1.0">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" type="text/css" href="css/public.css">
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<title>请问，您是？</title>
</head>
<body>
<form action="." method="post">
	<div class="main">
		<div class="title">接待处登录</div>
		<div class="line-wrap acc-wrap">
			<div class="input-name">账 号</div>
			<input type="text" class="input-text input-text-acc" name="user" maxlength="16" oninput="value=value.replace(/[^\w]/ig,'')" required autocomplete="false">
			<div class="btn btn-forget" tabindex="-1">忘 记</div>
		</div>
		<div class="line-wrap pass-wrap">
			<div class="input-name">密 码</div>
			<input type="text" class="input-text input-text-pass" name="pass" maxlengh="32" oninput="value=value.replace(/[^\w]/ig,'')" required autocomplete="false">
			<input class="btn btn-enter" name="submit" value="进 入">
		</div>
	</div>
</form>
<script type="text/javascript" src="js/index.js"></script>
</body>
</html>