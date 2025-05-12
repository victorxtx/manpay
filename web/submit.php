<?php
/*
功能:
	系统核心功能，应用层路由类型
	接受商户网站指挥玩家提交过来的代支付订单
	过滤 + 验签参数，处理订单
	找到给玩家展示的二维码
	入库并跳转到二维码展示页
类型: 直接访问
发起: 所有支付发起页
验证: 是
输入: GET 或 POST 只能居其一
{
	"money": <money>,
	"name": <name>,
	"notify_url": <notify_url>,
	"out_trade_no": <out_trade_no>,
	"pid": <pid>,
	"return_url": <return_url>,
	"sitename": <sitename>,
	"type": <type>,
	"sign": <sign>,
	"sign_type": <sign_type>
}
输出: 弹窗显示支付参数的处理结果，阻塞后跳转二维码展示页
<html>
	...
</html>

<flag>
	0: 正确
	1: 错误
<content_chs>
<content_eng>
*/
const VALID = true;
include 'lib/config.php';
include 'lib/mysql.php';
include 'lib/tools.php';
$res['flag'] = 0;
$res['content_chs'] = '提交成功！';
$res['content_eng'] = 'Done!';
session_start();
$identity = check_identity();
if ($identity == 'is_member'){ // is_member 去 index.php。因为不知道 side
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("./");
	}, 500);
</script>
<?php
	exit;
}
if ($identity == 'both'){ // 双重，去 who.php 裁决
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("./who.php");		
	}, 500);
</script>
<?php
	exit;
}
// 仅玩家 或 无身份 允许发起订单
// ...
if (!empty($_POST) && !empty($_GET)){ // 攻击者
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace('https://www.bing.com/');
	}, 500);
</script>
<?php
	exit;
}
if (empty($_POST) && empty($_GET)){ // 攻击者
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace('https://www.bing.com/');
	}, 500);
</script>
<?php
	exit;
}
// 针对玩家支付请求
$_INPUT = [];
$_INPUT = empty($_POST) ? $_GET : $_POST;
if (!is_array($_INPUT)){
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace('https://www.bing.com/');
	}, 500);
</script>
<?php
	exit;
}
$pid = $_INPUT['pid'];
$type = $_INPUT['type'];
$out_trade_no = $_INPUT['out_trade_no'];
$sign_out = strtolower($_INPUT['sign']);
$name = $_INPUT['name'];
$money = $_INPUT['money'];
$sitename = $_INPUT['sitename'];
$notify_url = $_INPUT['notify_url'];
$return_url = $_INPUT['return_url'];
$res = verify_input($_INPUT);
/**
 * 核心：处理商户代引导玩家发起的支付请求。判断请求参数合法性，不合法，整个函数跳过到最后为玩家显示结果页；合法，则进入处理流程。验签：失败则返回显示结果页，成功则订单入库，根据method和金额范围轮转选择二维码，
 * @return void
 */
function order_process(){
	global $_INPUT, $pid, $money, $type, $out_trade_no, $name, $random_discount_rate_range_alipay, $random_discount_rate_range_wxpay, $random_discount_rate_range_huabei, $res, $notify_url, $return_url, $sitename, $customer_stay_sec, $base_dir;
	$return_url = $_INPUT['return_url'];;
	if ($res['flag'] == 0){ // 参数过滤通过
		$mysql = connect();
		$sql_user =
		"SELECT
			`key`,
			`commission_fee_rate`
		FROM
			`user`
		WHERE
			`pid` = $pid;";
		$result_user = $mysql->query($sql_user);
		$data_user = $result_user->fetch_row();
		$merch_mkey = $data_user[0];
		$commission_fee_rate = $data_user[1];
		if (!verify_sign($_INPUT, $merch_mkey)){ // 签名运算失败
			$res['flag'] = 1;
			$res['content_chs'] = '签名验证失败';
			$res['content_eng'] = 'Sign validation failed!';
		}
		else{ // 签名验证成功，准备订单数据入库
			$dt = new DateTime();
			$order_place_time = $dt->format('Y-m-d H:i:s');
			$microtime = $dt->format('YmdHisu');
			$order_oid = str_pad(mysqli_fetch_assoc(mysqli_query($mysql, 'SHOW TABLE STATUS WHERE `Name` = "order";'))['Auto_increment'], 6, 0, STR_PAD_LEFT);
			$rand_suffix = rand_num(6);
			$trade_no = "$microtime$order_oid$rand_suffix";
			$money *= 100;
			$rate_range = null;
			switch($type){
			case 'alipay':
				$rate_range = $random_discount_rate_range_alipay;
				break;
			case 'wxpay':
				$rate_range = $random_discount_rate_range_wxpay;
				break;
			case 'huabei':
				$rate_range = $random_discount_rate_range_huabei;
				break;
			}
			$random_discount_rate = rand($rate_range[0], $rate_range[1]) / 10000; // (0, 1.0000) 表示优惠的比例
			$actual_amount = $money * (1 - $random_discount_rate);
			$out_trade_no = mysqli_real_escape_string($mysql, $out_trade_no);
			$name = mysqli_real_escape_string($mysql, $name);
			$notify_url = mysqli_real_escape_string($mysql, $notify_url);
			$return_url = mysqli_real_escape_string($mysql, $return_url);
			$sitename = mysqli_real_escape_string($mysql, $sitename);
			// 决定向用户展示哪一张二维码，这里使用的 needle 是玩家提交金额，非实付金额
			$redis = redis_connect();
			$qr_range = $redis->zRange(REDIS_ZKEY_AMOUNT_RANGE, 0, -1, ['WITHSCORES' => true]);
			$qr_range = zres_flip($qr_range);
			// 用金额找到应该去哪一个 range 拿二维码
			foreach ($qr_range as $key => $value){
				$value = json_decode($value, true);
				$min = $value['min'];
				$max = $value['max'];
				$money_yen = $money / 100;
				if ($money_yen >= $min && $money_yen <= $max){
					$range = $key;
					break;
				}
			}
			// 如果提交金额在所有范围之外
			if ($range === null){
				$res['flag'] = 1;
				$res['content_chs'] = '平台未设置此金额的收款码...';
				$res['content_eng'] = 'No qr-pay matching that range.';
				return;
			}

			$qr_org = $redis->zRange(REDIS_ZKEY_QR_IMGS, 0, -1);
			$qr_img = []; // 保存选中的二维码 ['method' => 'wxpay', 'filename' => 'wx01.png']
			// redis qr_imgs 原始数据取出来 json_decode
			// 找到数组中 seq 的最大值和最小值，下一次遍历时轮换 seq
			$qr_found = false; // 
			$seqs_org = []; // 轮转因子
			foreach ($qr_org as $key => $value){
				$value = json_decode($value, true);
				$qr_org[$key] = $value; // value 反序列化写入临时数组
				// 在 $range 中筛出 $type 的元素
				// var_dump($value);
				if ($value['range'] == $range && $value['method'] == $type){
					// 把 $range 中 $type 的所有二维码 [文件名 => 顺序] 存进一个数组 $seqs_org，作为轮转原始数据
					// ['wx01.png' => 2, 'wx03.png' => 3]
					// key 是文件名，value 是 sequence
					$seqs_org[$value['filename']] = $value['sequence'];
					// 原始数组默认会按 sequence 顺序升序排列（zRange 默认特性）
					// 所以可以把 $range 中 $type 的第一个二维码路径和文件名存起来（存入 MySQL 订单表 qr_file 字段）
					if (!$qr_found){
						$qr_img['filename'] = $value['filename'];
						$qr_img['method'] = $value['method'];
						// 存完第一个，标志置位
						$qr_found = true;
						continue;
					}
				}
			}
			// 如果匹配到的 range 中没有提交 type 中的 method
			if (empty($qr_img)){
				$type_show = '';
				switch($type){
				case 'alipay':
					$type_show = '支付宝';
					break;
				case 'wxpay':
					$type_show = '微信支付';
					break;
				case 'huabei':
					$type_show = '花呗';
				}
				$res['flag'] = 1;
				$res['content_chs'] = "暂未提供{$type_show}收款码！";
				$res['content_eng'] = "No $type qr provided yet!";
				return;
			}
			// var_dump($qr_img);exit;
			// var_dump($qr_org);exit;
			// var_dump($qr_img);exit;
			// 处理轮转因子：保持 key(filename) 不变，value(sequence) 向后移动
			$seqs = [];// ['wx02.png' => 2, 'wx01.png' => 4]
			$count_method_range = count($seqs_org);
			if ($count_method_range > 1){
				$keys = array_keys($seqs_org);
				foreach ($keys as $i => $key){
					if ($i === 0){
						$seqs[$key] = $seqs_org[$keys[count($keys) - 1]];
					}
					else{
						$seqs[$key] = $seqs_org[$keys[$i - 1]];
					}
				}
			}
			// 执行轮转：根据轮转因子，对原始数据
			// 上面已经把 $value 数组化
			foreach ($qr_org as $key => $value){
				// var_dump($value['range']);
				// var_dump($value['method']);
				if ($value['range'] == $range && $value['method']){
					if (key_exists($value['filename'], $seqs)){
						$value['sequence'] = $seqs[$value['filename']];
					}
					$qr_org[$key] = $value; // 写回当前数组元素，其他非当前 range 元素不动
				}
			}
			// dump($qr_img);exit;
			// 删除 redis 中整个 qr_imgs
			$redis->del(REDIS_ZKEY_QR_IMGS);
			// 更新 redis：全 key 遍历写回
			foreach ($qr_org as $value){
				$redis->zAdd(REDIS_ZKEY_QR_IMGS, 0, json_encode($value));
			}
			$qr_file = $qr_img['method'].'/'.$qr_img['filename'];
			$sql_new_order =
			"INSERT INTO
				`order`(
					`pid`,
					`trade_no`,
					`out_trade_no`,
					`name`,
					`type`,
					`money`,
					`random_discount_rate`,
					`commission_fee_rate_actual`,
					`actual_amount`,
					`notify_url`,
					`return_url`,
					`sitename`,
					`order_place_time`,
					`qr_file`
				)
				VALUES(
					$pid,
					'$trade_no',
					'$out_trade_no',
					'$name',
					'$type',
					$money,
					$random_discount_rate,
					$commission_fee_rate,
					$actual_amount,
					'$notify_url',
					'$return_url',
					'$sitename',
					'$order_place_time',
					'$qr_file'
				);";
		
			$result_new_order = execute($mysql, $sql_new_order);
			if (!$result_new_order){ // 入库执行失败，报错
				$res['flag'] = 1;
				$res['content_chs'] = '数据库出错！';
				$res['content_eng'] = 'Database error!';
			}
			else{ // 入库执行成功，设置身份，进入缓冲页，确定后转到 ch/chat.php
				$sql_oid = "SELECT `oid` FROM `order` WHERE `trade_no` = '$trade_no';";
				$result_oid = execute($mysql, $sql_oid);
				$oid = $result_oid->fetch_row()[0];
				$_SESSION['oid'] = $oid;
				$_SESSION['pid'] = $pid;
				$_SESSION['out_trade_no'] = $out_trade_no;
				$_SESSION['money'] = $money;
				$_SESSION['order_place_time'] = $order_place_time;
				setcookie('oid', $oid, time() + $customer_stay_sec, $base_dir, '', true, false);
			}
		}
	}
}
order_process();
$title = $res['flag'] == 1 ? '出错啦~！' : '成功！';
// 1=手; 0=自
$submit_next = $charge_method ? 'qrshow.php' : $_INPUT['return_url'];
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
	<link rel="stylesheet" type="text/css" href="css/prompt.css">
	<link rel="icon" href="img/rmb.svg">
	<title><?php echo $title;?></title>
</head>
<body>
	<div class="prompt">
		<div class="title"><?php echo $title;?></div>
		<div class="content-wrap">
			<div class="content content-chs"><?php echo $res['content_chs'];?></div>
			<div class="content content-eng"><?php echo $res['content_eng'];?></div>
		</div>
		<div class="btn-wrap">
			<div class="btn btn-ok">确 定</div>
		</div>
	</div>
	<script type="text/javascript">
		document.querySelector('.btn-ok').addEventListener('click', function(){
		<?php
		if ($res['flag'] == 1){
		?>
		location.replace('./');
		<?php
		}
		else{
		?>
		location.replace('<?php echo $submit_next;?>');
		<?php
		}
		?>
		});
	</script>
</body>
</html>