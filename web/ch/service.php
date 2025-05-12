<?php
include 'lib/config.php';
include 'lib/tools.php';
session_start();
$identity = check_identity();
switch ($identity){
	case 'both':
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("../who.php");
	}, 500);
</script>
<?php
		exit;
	case 'neither':
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("../");
	})
</script>
<?php
		exit;
	case 'is_customer':
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("../customer.php");	
	}, 500);
</script>
<?php
		exit;
}
$side = $_SESSION['side'];

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
	<link rel="stylesheet" type="text/css" href="css/service.css">
	<title>客服接待</title>
</head>
<body>
	<div class="full-wrap">
		<div class="list-wrap">
			<div class="list-head">
				<input type="radio" name="radio-sort" id="radio-sort-new" class="radio-sort-input radio-sort-new" checked>
				<label class="radio-sort" for="radio-sort-new">最新</label>
				<input type="radio" name="radio-sort" id="radio-sort-alpha" class="radio-sort-input radio-sort-alpha">
				<label class="radio-sort" for="radio-sort-alpha">名字</label>
				<input type="radio" name="radio-sort" id="radio-sort-stop" class="radio-sort-input radio-sort-stop">
				<label class="radio-sort" for="radio-sort-stop">停止排序</label>
			</div>
			<div class="list-item-wrap"></div>
		</div>
		<div class="chat-wrap">
			<div class="chat-head">接收中...</div>
			<div class="input-wrap">
				<div contenteditable="plaintext-only" type="text" class="chat-input" placeholder="粘贴图片直接发送"></div>
			</div>
			<div class="button-wrap">
				<button class="btn-common btn-exit">返回后台</button>
				<?php
				// if ($_SERVER['REMOTE_ADDR'] == '10.0.0.1'){
				// 	echo '<button class="btn-common btn-test">测试功能</button>';
				// }
				
				?>
				<button class="btn-common btn-send">发送 (Ctrl+Enter)</button>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="js/service.js"></script>
</body>
</html>