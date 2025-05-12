<?php
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include 'lib/tools.php';
session_start();
$identity = check_identity();
if ($identity == 'both'){ // 双重身份，跳 ../who.php 裁决
	echo '<script type="text/javascript">location.replace("../who.php");</script>';
	exit;
}
if ($identity == 'neither'){ // 无身份（需先发起订单，或已有订单，有订单即为 'is_customer'）
	echo '<script type="text/javascript">location.replace("../");</script>';
	exit;
}
if ($identity == 'is_member'){ // 
	$alert_text = '';
	$url = '';
	if (in_array($_SESSION['side'], $admin_side)){
		$alert_text = "管理员请移步平台聊天页，3 秒后将自动跳转 ...";
		$url = 'service.php';
	}
	else if (in_array($_SESSION['side'], $merch_side)){ // side >= 10 为注册商户
		$alert_text = "商户暂不能使用聊天系统，3 秒后将跳转商户后台 ...";
		$url = '../merch.php';
	}
?>
<script type="text/javascript">
	setTimeout(function(){
		location.replace('<?php echo $url;?>');
	}, 3000)
	alert('<?php echo $alert_text;?>');
</script>
<?php
	exit;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" type="text/css" href="css/public.css">
	<link rel="stylesheet" type="text/css" href="css/chat.css">
	<link rel="stylesheet" type="text/css" href="css/alert.css">
	<title>充值咨询</title>
</head>
<body>
	<div class="chat-wrap">
		<div class="chat-show"></div>
		<div class="input-wrap">
			<div contenteditable="plaintext-only" type="text" class="chat-input" placeholder="粘贴图片直接发送"></div>
		</div>
		<div class="button-wrap">
			<button class="btn-common btn-send" tabindex="-1">发送 (Ctrl+Enter)</button>
			<button class="btn-common btn-img-wrap"><input class="btn-img" type="file" accept="image/gif,image/jpg,image/jpeg,image/png">选择图片</button>
			<button class="btn-common btn-exit" tabindex="-1">结束对话</button>
		</div>
	</div>
	<script type="text/javascript" src="js/chat.js"></script>
</body>
</html>