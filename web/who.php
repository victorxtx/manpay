<?php
const VALID = true;
include_once 'lib/config.php';
include_once 'lib/mysql.php';
include_once 'lib/tools.php';
session_start();
$title = '';
$identity = check_identity();
if ($identity == 'is_member'){ // 商户，非玩家。去 index.php
	echo '<script type="text/javascript">location.replace("./");</script>';
	exit;
}
else if ($identity == 'is_customer'){ // 玩家，非商家，去 customer.php
	echo '<script type="text/javascript">location.replace("customer.php");</script>';
	exit;
}
else if ($identity == 'neither'){ // 无身份，去 index.php
	echo '<script type="text/javascript">location.replace("./");</script>';
	exit;
}
// else if ($identity == 'both'){
// 	// 双重身份，本页强制处理
// }
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/public.css">
	<link rel="stylesheet" type="text/css" href="css/who.css">
	<link rel="stylesheet" type="text/css" href="css/alert.css">
	<link rel="icon" href="img/rmb.svg">
	<title><?php echo $title;?></title>
</head>
<body>
<div class="main">
	<div class="notice">系统侦测到<b class="color-red">您既是商户也是玩家</b></div>
	<div class="notice">而系统只能以<b class="color-red">一种身份</b>登录</div>
	<div class="notice">请选择您将<b class="color-red">以什么身份</b>登录系统</div>
	<div class="btn-block">
		<button class="btn-who btn-merch">我是商户</button>
		<button class="btn-who btn-cust">我是玩家</button>
		<button class="btn-who btn-none">啥也不是</button>
	</div>
	<script type="text/javascript" src="js/who.js"></script>
</div>
</body>
</html>