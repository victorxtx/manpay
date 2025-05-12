<?php
// 本页面：
// 接收从 submit.php 页面跳来的支付请求
// 读取在 submit.php 执行 setcookie oid 的 oid
//  
const VALID = true;
include 'lib/config.php';
include 'lib/mysql.php';
include 'lib/tools.php';
session_start();
$identity = check_identity();
if ($identity == 'both'){
	echo '<script type="text/javascript">location.replace("who.php");</script>';
	exit;
}
if ($identity == 'neither'){
	echo '<script type="text/javascript">location.replace("./");</script>';
	exit;
}
if ($identity == 'is_member'){
	echo '<script type="text/javascript">location.replace("./");</script>';
	exit;
}

// 获取 oid 对应的订单数据
$oid = $_SESSION['oid'];
$conn = connect();
$sql_order =
"SELECT
	`pid`,
	`trade_no`,
	`out_trade_no`,
	`qr_file`,
	`name`,
	`type`,
	`money`,
	`actual_amount`,
	`sitename`,
	`return_url`,
	`order_place_time`
FROM
	`order`
WHERE
	`oid` = $oid;";
$result_order = $conn->query($sql_order);
$data_order = $result_order->fetch_assoc();

$pid = $data_order['pid'];
$trade_no = $data_order['trade_no'];
$out_trade_no = $data_order['out_trade_no'];
$qr_file = $data_order['qr_file'];
$name = $data_order['name'];
$type = $data_order['type'];
$money = $data_order['money'];
$actual_amount = $data_order['actual_amount'];
$sitename = $data_order['sitename'];
$return_url = $data_order['return_url'];
$order_place_time = $data_order['order_place_time'];
// 读文件
$qr_file_content = file_get_contents("img/qr-files/$qr_file");
$b64 = base64_encode($qr_file_content);
$img_type_int = exif_imagetype("img/qr-files/$qr_file");
switch ($img_type_int){
case IMAGETYPE_JPEG:
	$img_type = 'image/jpeg';
	break;
case IMAGETYPE_GIF:
	$img_type = 'image/gif';
	break;
case IMAGETYPE_BMP:
	$img_type = 'image/bmp';
	break;
case IMAGETYPE_PNG:
	$img_type = 'image/png';
	break;
default:
	break;
}
$type_show = '';
$money_show = number_format($money / 100, 2, '.', '');
$actual_amount_show = number_format($actual_amount / 100, 2, '.', '');
$redis = redis_connect();
$redis->select(REDIS_DBNM_QPAY);
$qr_org = $redis->zRange(REDIS_ZKEY_QR_IMGS, 0, -1);
$arr_file = explode('/',$qr_file);
$method = $arr_file[0];
$filename = $arr_file[1];
foreach ($qr_org as $member){
	$member = json_decode($member, true);
	if ($member['method'] == $method && $member['filename'] == $filename){
		$text = $member['text'];
		break;
	}
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>请扫码支付</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/qrshow.css">
	<link rel="icon" href="img/rmb.svg">
	<script src="js/qrshow.js" defer></script>
</head>
<body>
	<div class="main">
		<div class="line-wrap flex">
			<div class="flex-label">支付金额</div>
			<div>
				<span class="yen yan-origin">¥</span><span class="money money-origin"><?php echo $money_show;?></span>
			</div>
			
		</div>
		<div class="line-wrap">恭喜您触发了<b style="color: rgba(255,255,255,0.75);">折扣奖励</b>！</div>
		<div class="line-wrap">本次您仅需支付：</div>
		<div class="line-wrap center">
			<span class="yen yen-final">¥</span><span class="money money-final"><?php echo $actual_amount_show;?>
		</div>
		<div class="line-wrap center">
			<div class="qr-img" style="background-image: url(<?php echo "data:$img_type;base64,$b64";?>)"></div>
		</div>
		<div class="line-wrap center hint">请在 <span class="count-down">05:00</span> 内完成支付</div>
		<div class="line-wrap center hint">请使用<?php echo $type_show;?>支付</div>
		<div class="line-wrap vertical">
			<div class="notice-name">支付须知：</div>
			<div class="notice-text"><?php echo $text;?></div>
		</div>
	</div>
	<?php
	var_dump($qr_file);
	?>
</body>
</html>