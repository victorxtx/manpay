<?php
const VALID = true;
include_once 'lib/config.php';
include_once 'lib/tools.php';
include_once 'lib/mysql.php';
$identity = check_identity();
switch ($identity){
	case 'is_member': // 去主页先退出登录
?>
<script>
	alert("请先退出登录，再使用本功能！");
	setTimeout(() => {
		location.replace("./");
	}, 500);
</script>
<?php
		exit;
	case 'is_customer': // 已经发起一个订单，删掉该浏览器的 cookie.oid，刷新页面，让玩家永远可以发起新订单
		clear_customer_session();
		setcookie('oid', '', time() - 36000, $base_dir);
?>
<script>
	// alert("已经发起一个订单，请先处理完再来！");
	location.reload();
</script>
<?php
		exit;
	case 'both': // 双重身份，先裁决 who
?>
<script>
	alert("身份异常，请先确定您的身份");
	setTimeout(() => {
		location.replace("who.php");
	}, 500);
</script>
<?php
		exit;
	default: // 无身份放行
		break;
}
if (isset($_POST['submit'])){
	if (!empty($_POST) && !empty($_GET)){
		$flag = 1;
		$content_chs = '机房重地，闲人免进！';
		$content_eng = 'Sign validation failed!';
	}
	if (empty($_POST) && empty($_GET)){
		$flag = 1;
		$content_chs = '机房重地，闲人免进！';
		$content_eng = 'Sign validation failed!';
	}
	$_INPUT = [];
	$_INPUT = empty($_POST) ? $_GET : $_POST;
	$conn = connect();
	$sql_mkey = "SELECT `key` FROM `user` WHERE `pid` = {$_POST['pid']};";
	$result_mkey = execute($conn, $sql_mkey);
	$secretKey = $result_mkey->fetch_row()[0];
	unset($_POST['submit']);
	ksort($_POST);
	// exit;
	$str_to_sign = '';
	foreach ($_POST as $key => $value){
		$str_to_sign .= "$key=$value&";
	}
	$str_to_sign = substr($str_to_sign, 0, -1);
	
	$sign = md5("$str_to_sign$secretKey");
	// dump($str_to_sign);
	// dump($thisKey);
	// dump($sign);exit;
	
	$_POST['sign'] = $sign;
	$_POST['sign_type'] = 'MD5';

	$do_post = '<form id="pay-submit" action="submit.php" method="post">'.PHP_EOL;
	foreach ($_POST as $key => $value){
		$do_post .= "\t<input type=\"hidden\" name=\"$key\" value=\"$value\">.".PHP_EOL;
	}
	$do_post .= "\t<input type=\"submit\" value=\"正在跳转支付页...\">\n</form>\n";
	$do_post .= '<script>document.getElementById("pay-submit").submit();</script>';
	// file_put_contents('Debug.html', $do_post);
	echo $do_post;
	exit;
}
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
	<link rel="icon" href="img/rmb.svg">
	<title>模拟订单发起</title>
	<style>
		*{
			padding: 0;
			margin: 0;
			box-sizing: border-box;
			font-family: 'YAHEI';
		}
		.title{
			text-align: center;
			margin: auto;
			padding: 25px 0;
			font-size: 24px;
			user-select: none;
		}
		.subtitle{
			margin-bottom: 10px;
			font-size: 13px;
			user-select: none;
		}
		.main{
			width: 1080px;
			margin: auto;
		}
		form{
			margin-top: 25px;
		}
		input{
			outline: none;
			border-radius: 5px;
			border: 1px solid rgba(36, 36, 36, 1);
			width: 300px;
			font-size: 16px;
			height: 32px;
			padding: 0 8px;
			font-weight: 400;
		}
		select{
			outline: none;
			border-radius: 5px;
			border: 1px solid rgba(36,36,36,1);
			font-size: 16px;
			height: 32px;
			padding: 3px;
			cursor: pointer;
			font-weight: 400;
		}
		option{
			cursor: pointer;
		}
		.readonly{
			background-color: rgba(128,128,128,0.5);
			cursor: not-allowed;
		}
		.wrap{
			display: flex;
			justify-items: center;
			margin-bottom: 10px;
		}
		.key-name{
			display: inline-block;
			width: 120px;
			line-height: 32px;
			font-weight: bold;
			user-select: none;
		}
		.note{
			line-height: 32px;
			margin-left: 10px;
			user-select: none;
		}
		.key{
			user-select: text;
			cursor: text;
		}
		.btn-wrap{
			margin-top: 50px;
			display: flex;
			justify-content: space-around;
		}
		.btn{
			display: block;
			border: 1px solid gray;
			border-radius: 5px;
			width: 120px;
			height: 32px;
			font-size: 16px;
			font-weight: 500;
			cursor: pointer;
			transition: 0.18s;
			user-select: none;
		}
		.btn:hover{
			filter: brightness(1.1);
			transition: 0.18s;
		}
		.btn-submit{
			color: rgba(50, 50, 50, 1);
			background-color: rgba(120, 184, 50, 1);
		}
		.btn-refresh{
			color: wheat;
			background-color: rgba(50, 120, 184, 1);
		}
		.btn-refresh-2{
			margin-left: 10px;
			color: wheat;
			background-color: rgba(50, 120, 184, 1);
		}
	</style>
</head>
<body>
<div class="title">支付订单生成模拟（商户、管理不可用）</div>
<div class="main">
	<div class="subtitle"><b>*</b> 此页面用于模拟最终用户在商户技术员的引导下向本平台网关发起支付请求时的订单生成（致管理员）</div>
	<div class="subtitle"><b>*</b> 页面中的 8 个参数为总共 10 个参数中的数据部分（还有两个为 sign 和 sign_type），具体开发文档请到主页登录后查看（致商户程序员）</div>
	<div class="subtitle"><b>*</b> 通知和跳转地址请仔细填写，本页面默认示例是测试回调地址，不会验签，并永远输出 SUCCESS，仅做测试。</div>
	<form method="post" action="">
		<?php
		$conn = connect();
		$sql_pid_range = "SELECT DISTINCT `pid` FROM `user` WHERE `side` >= 10;";
		$result_pid_range = execute($conn, $sql_pid_range);
		$data_pid_range = $result_pid_range->fetch_all();
		$pids = [];
		foreach($data_pid_range as $key => $value){
			array_push($pids, $value[0]);
		}
		?>
		<!-- <label class="wrap"> Deprecated
			<label class="key-name" for="pid">玩家标志:</label>
			<input id="customer" type="text" name="customer" value="<?php echo nickname(1);?>">
			<label class="note">聊天交流时显示的客户名 (key: '<b class="key">customer</b>')</label>
		</label> -->
		<label class="wrap">
			<label class="key-name" for="pid">商户 ID:</label>
			<select name="pid" class="sel-pid">
			<?php
			if (!empty($pids)){
				$random_pid = $pids[array_rand($pids)];
				foreach ($pids as $value){
				?>
				<option value="<?php echo $value;?>"<?php if($value == $random_pid)echo ' selected';?>>商户号 pid = <?php echo $value;?></option>
				<?php
				}
			}
			?>
			</select>
			<label class="note">数据库中已存在全部商户 <b>PID</b> (key: '<b class="key">pid</b>')</label>
		</label>
		<label class="wrap">
			<label class="key-name" for="type">支付类型:</label>
			<?php
			$methods = ['wxpay', 'alipay', 'huabei'];
			$rand_key = array_rand($methods, 1);
			$rand_method = $methods[$rand_key];
			?>
			<select name="type">
				<option value="wxpay"<?php if($rand_method == 'wxpay')echo ' selected';?>>微信支付 (wxpay)</option>
				<option value="alipay"<?php if($rand_method == 'alipay')echo ' selected';?>>支付宝 (alipay)</option>
				<option value="huabei"<?php if($rand_method == 'huabei')echo ' selected';?>>花呗 (huabei)</option>
			</select>
			<label class="note">(key: '<b class="key">type</b>')</label>
		</label>
		<?php
		$otn = date('YmdHis', time()).rand_num(6);
		?>
		<label class="wrap">
			<label class="key-name" for="out_trade_no">商户订单号:</label>
			<input class="readonly" id="out_trade_no" type="text" name="out_trade_no" value="<?php echo $otn;?>" readonly>
			<button class="btn btn-refresh-2">刷 新</button>
			<label class="note">提交时会验证是否同商户重复 (key: '<b class="key">out_trade_no</b>')</label>
		</label>
		<label class="wrap">
			<label class="key-name" for="notify_url">通知地址:</label>
			<input id="notify_url" type="text" name="notify_url" value="https://www.vickystudio.cn:100/pf/notify.php" style="min-width:420px;width:fit-content">
			<label class="note">异步回调地址，必须<b>公网可达</b> (key: '<b class="key">notify_url</b>')</label>
		</label>
		<label class="wrap">
			<label class="key-name" for="return_url">跳转地址:</label>
			<input id="return_url" type="text" name="return_url" value="https://www.vickystudio.cn:100/pf/return.php" style="min-width:420px;width:fit-content">
			<label class="note">同步跳转地址，必须<b>公网可达</b> (key: '<b class="key">return_url</b>')</label>
		</label>
		<label class="wrap">
			<label class="key-name" for="name">商品名:</label>
			<input class="readonly" id="name" type="text" name="name" value="元直充" maxlength="20" readonly>
			<label class="note">最多 <b>20</b> 个汉字，或 <b>64</b> 字节 (key: '<b>name</b>')</label>
		</label>
		<label class="wrap">
			<label class="key-name" for="money">金额(元):</label>
			<input id="money" type="text" name="money" value="200" maxlength="5">
			<label class="note">最多<b>两位小数</b>，不能用中文 (key: '<b class="key">money</b>')</label>
		</label>
		<label class="wrap">
			<label class="key-name" for="sitename">站点名:</label>
			<input id="sitename" type="text" name="sitename" value="黑山老妖魔兽圈钱服" maxlength="20">
			<label class="note">最多 <b>20</b> 个汉字，或 <b>64</b> 字节 (key: '<b class="key">sitename</b>')</label>
		</label>
		<div class="btn-wrap">
			<input class="btn btn-submit" id="submit" type="submit" name="submit" value="模拟提交">
			<button class="btn btn-refresh">全部刷新</button>
		</div>
	</form>
</div>
<script type="text/javascript">
	var selPid = document.querySelector('.sel-pid');
	var outTradeNo = document.querySelector('#out_trade_no');
	var goodName = document.querySelector('#name');
	var money = document.querySelector('#money');
	var btnRefresh = document.querySelector('.btn-refresh');
	var btnRefresh2 = document.querySelector('.btn-refresh-2');
	money.addEventListener('input', function(){
		if (this.value == ''){
			this.value = '0';
			goodName.value = '0 元直充';
			return;
		}
		if (this.value.length > 1 && this.value[0] == '0'){
			this.value = this.value.substring(1);
			goodName.value = this.value + ' 元直充';
			return;
		}
		goodName.value = this.value + ' 元直充';
	});
	window.onload = function(){
		let selInd = selPid.selectedIndex;
		
		let selectedPid = selPid.options[selInd].value;
		outTradeNo.value = 'PID' + selectedPid + '_' + outTradeNo.value;

		let randomAmount = selPid.options[selInd].value * 100 + Math.floor(Math.random() * (100 - 0 - 1)) + 0 + 1;
		console.log(selPid.options[selInd].value * 100)
		console.log(Math.floor(Math.random() * (100 - 0 - 1)) + 0 + 1)
		goodName.value = randomAmount + ' 元直充';
		money.value = randomAmount;
	}
	selPid.addEventListener('change', function(){
		let selInd = selPid.selectedIndex;
		let selectedPid = selPid.options[selInd].value;
		outTradeNo.value = outTradeNo.value.substring(5);
		outTradeNo.value = 'PID' + selectedPid + '_' + outTradeNo.value;
		goodName.value = selectedPid + goodName.value.substring(1);
		money.value = selectedPid + money.value.substring(1);
	});
	
	btnRefresh.addEventListener('click', refreshWhole);
	btnRefresh2.addEventListener('click', refreshWhole);
	function refreshWhole(){
		history.go(0)
	}
</script>
</body>
</html>