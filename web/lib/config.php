<?php
if (!defined('VALID')){ // 防御直接攻击
?>
<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="refresh" content="15;URL=<?php echo "https://www.mps.gov.cn/";?>">
		<title>傻逼来了?</title>
	</head>
	<body>
		<div class="notice">
			<div style="margin:20px auto;text-align:center;">
				<h1 style="font-size:40px">滚，你妈生你的时候逼眼儿被我的鸡巴堵死了，你是从屁眼儿拉出来的！</h1>
				<h1 style="font-size:40px">拉出来的时候全身都是屎！味道现在还没洗干净！</h1>
			</div>
		</div>
	</body>
</html>
<?php
exit();
}
/*
说明：
	带 (*) 的：必填
	带 (#) 的：如果能看懂，可以自己修改
	带 (!) 的：千万别动
	带 (?) 的：我也不记得有没有效果了
	带 (x) 的：已废弃或未实现
*/
// MySQL
const MYSQL_HOST = ''; // (*) MySQL 主机地址，一般是本机 '127.0.0.1'
const MYSQL_USER = ''; // (*) MySQL 的登录用户名，不知道一般是什么，我直接用的 'root'
const MYSQL_PASS = ''; // (*) 登录用户名对应的密码
const MYSQL_NAME = 'platform'; // (#) 项目根目录 mysql.sql 为建库建表脚本
const MYSQL_PORT = 3306; // (#) MySQL 端口号
const MYSQL_SOCK = '/dev/shm/mysqld.sock'; // (#) MySQL 的 unixsocket 访问地址，懂的应该好好配置这个值，不懂的话不动也可以用
// Redis
const REDIS_HOST = '127.0.0.1'; // (#) REDIS 的主机地址
const REDIS_AUTH = ''; // (*) REDIS 的登录密码（不要加用户名）
const REDIS_PORT = -1; // (!) 警告：请在 Redis 配置文件中配置 unixsocket "/dev/shm/redis.sock"，因为本系统默认使用 unixsocket 与 Redis 通信，不支持 6379 端口（开发失误）。你可以在 lib/tools.php 中修改 redis_connect 函数支持 6379。
const REDIS_SOCK = '/dev/shm/redis.sock'; // (!) 默认配置，千万别动
// 请在 redis 中留出 1 ~ 4 号数据库，如果你的 PHP 的 session_handler 是 redis，那么 0 号也需要留
// 其中 1 号是必须留，2 ~ 4 号可以不留了，因为这是另一个子系统的库，而这个子系统已经被叫停
const REDIS_DBNM_SESS = 0; // (#) php session
const REDIS_DBNM_QPAY = 1; // (!) qr img organizer
const REDIS_DBNM_CHAT = 2; // (#) chat message contents
const REDIS_DBNM_CNSL = 3; // (x) chat console manager(Deprecated)
const REDIS_DBMN_PGDF = 4; // (x) page attack defender
const REDIS_ZKEY_QR_IMGS = 'qr_imgs'; // (!) Redis 1 号库里的 key 名。是所有手动收款二维码的组织数据。
const REDIS_ZKEY_AMOUNT_RANGE = 'amount_range'; // (!) Redis 1 号库里的 key 名。是针对不同金额的收款范围定义。（这里没说清楚，自己用一下就明白了）
$notify_url_detect_timeout_seconds = 10; // 支付成功通知商户网站超时时间。你的平台向商户的平台发支付成功的通知时，商户网站超过这个秒数，就视为通知失败。墙内外的通知最好设置在 5 以上。
$front_host = 'https://your-site.com'; // (*) 你的网站从外部可以访问的带协议、带主机、带端口的 URL 地址。注意：结尾不能带 '/'。不能留空
$base_dir = '/'; // (*) 你的网站在你的根地址下所处的目录。若在网站根目录，就填写 '/'，若在 manpay 目录下就写 '/manpay'。左边带 /，右边不带 /
$allow_chars = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '_']; // 商户注册时允许使用的字符
$hex_chars = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f']; // 别管这个，我也忘了
$allow_pay_methods = [ // (#) 允许的支付方式列表，这里不能加东西，只能删一些东西，因为没有做额外的逻辑。
	'alipay',
	'wxpay',
	'huabei',
];
$allow_sign_types = [ // (!) api v1 版本。事实上一些支付平台已经升级为 2.0 了。
	'MD5'
];
$order_cols = ['oid', 'pid', 'trade_no', 'out_trade_no', 'qr_file', 'name', 'money', 'random_discount_rate', 'actual_amount', 'sitename', 'order_place_time', 'pay_status', 'pay_time', 'payer', 'notify_status', 'notify_time', 'notifier']; // (!) 系统内部制表数据，不要更改
$sess_member_keys = ['username', 'nickname', 'side', 'qq', 'reg_time', 'reg_ip', 'last_time', 'last_ip', 'stat', 'balance', 'level', 'key', 'notify_method', 'search_filter']; // (!) 别动，有一些字段会被内部使用
$sess_customer_keys = ['oid', 'pid', 'out_trade_no', 'money', 'order_place_time']; // (!) 内部使用字段
$money_min = 20000; // (#) 最小允许充值金额，分为单位。100:一元钱。这里并非金额控制的唯一位置，还有地方控制，我也忘了是哪儿。
$money_max = 100000; // (#)一个订单允许的最大充值金额，以“分”啊为单位。990000=￥9900元。这里并非金额控制的唯一位置，还有地方控制，我也忘了是哪儿。
$rate = 0.05; // (x) 已废弃。平台抽成费率，0.05 就是 5%。平台抽成费率在数据库中针对每个商户可以修改为不同值。商户注册默认是 5%，可以在管理侧后台“商户 / 信息”里修改“提现费率”
/*
$random_discount_rate_range_*
	提交订单时的随机折扣率取值范围，单位 ‱ （万分之），即 0.30% ~ 0.35%
	比如玩家提交 100 元，实际实付为 99.65 ~ 99.70 元
	若玩家成功支付折扣后金额，商户将看到 100 x 0.95 = 95 元余额到帐。
	管理侧将看到提交金额 100 元和折扣后的金额 99.6x 元，以及该订单随机折扣率的取值
*/
$random_discount_rate_range_alipay = [30, 35]; // (#) 针对支付宝借记（余额、银行卡等）的随机折扣值，单位 ‱
$random_discount_rate_range_wxpay = [10, 15]; // (#) 针对微信借记的随机折扣值，单位 ‱
$random_discount_rate_range_huabei = [10, 15]; // (#) 针对支付宝贷记（花呗）的随机折扣额，单位 ‱
$maxlen_out_trade_no = 32; // (#!) 外部订单（商户提交支付请求时带的订单号）的最大长度（这个最好别往上改，其他地方有限制）
$charge_method = 1; // (!) 1=手动充值, 0=自动充值，本系统就是手动代支付，所以必须是 1。
$order_chat_duration = 2; // (?) 玩家发起订单的留存时间，单位小时，最多两位小数。这个配置没有做逻辑，没有效果。
if ($base_dir == '/'){ // (!) 不要修改
	$api_submit = "$front_host/submit.php"; // (!) 不要修改
	$api_order = "$front_host/query"; // (!) 不要修改
}
else if ($base_dir == ''){ // (!) 不要修改
	echo '$base_dir 不能为空，请参照说明填写';
	exit;
}
else{
	$api_submit = "$front_host$base_dir/submit.php"; // (!) 不要修改
	$api_order = "$front_host$base_dir/query"; // (!) 不要修改
}


$orders_per_page = 20; // (#) 订单每页显示条数，好像只有管理侧有效，商户侧我忘了，好像也有效。
$customer_stay_sec = 300; // (#) 收款码展示时间秒数，玩家在发起订单后页面会显示 300 秒倒计时，计时结束会跳转到其他页面。但玩家若按 F5 刷新会重置该时间。所以只是个看的花瓶，没有实质限制效果，也没有必要限制。
$admin_side = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]; // (!) 不要修改。简单的权限定义，实际使用的管理侧就两个角色，0 和 1
$merch_side = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19]; // (!) 不要修改。简单的权限定义，