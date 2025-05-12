<?php
if (!defined('VALID')) { //防止该文件被直接访问
?>
	<!DOCTYPE html>
	<html lang="zh-CN">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="refresh" content="15;URL=<?php echo "https://www.mps.gov.cn/"; ?>">
		<title>傻逼来了?</title>
		<link rel="stylesheet" type="text/css" href="style/remind.css">
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
/**
 * redis 快捷连接函数。（专用函数）
 * @param string $host Redis 数据库服务器主机地址，默认使用 unix_socket
 * @param int $port Redis 数据库服务器监听端口，默认 -1，不使用网络连接
 * @param int $auth Redis 数据库服务器密码
 * @param int $db Redis 数据库服务器模式使用数据库编号
 * @return Redis|false 如果成功，则是连接到 Redis 服务器的资源变量，如果失败，则为 false
 */
function redis_connect($host = REDIS_SOCK, $port = -1, $auth = REDIS_AUTH, $db = 1){
	try{
		$redis = new Redis([
			'host' => $host,
			'port' => $port,
			'auth' => $auth
		]);	
	}
	catch(Exception $e){
		return false;
	}
	$redis->select(1);
	return $redis;
}
function is_login(){
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	if (isset($_COOKIE['username']) && isset($_SESSION['username'])) {
		if ($_COOKIE['username'] == $_SESSION['username']) {
			return true;
		}
	}
	return false;
}
function is_customer(){
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	if (isset($_COOKIE['oid']) && isset($_SESSION['oid'])){
		if ($_COOKIE['oid'] == $_SESSION['oid']){
			return true;
		}
	}
	return false;
}
/**
 * @param string $rec_user 承认并允许当前用户登录。将 session 数据写入 $_SESSION
 * @return void
 */
function set_member_session($rec_user){
	global $sess_member_keys;
	// file_put_contents('debug_1.txt', var_export($rec_user, true));
	foreach ($sess_member_keys as $sess_member_key){
		// file_put_contents('debug_1.txt', $sess_member_key);
		$_SESSION[$sess_member_key] = $rec_user[$sess_member_key];
	}
}
function clear_member_session(){
	global $sess_member_keys;
	foreach ($sess_member_keys as $sess_admin_key){
		if (isset($_SESSION[$sess_admin_key])){
			unset($_SESSION[$sess_admin_key]);
		}
	}
}
function set_customer_session($rec_user){
	global $sess_customer_keys;
	foreach ($sess_customer_keys as $sess_customer_key){
		$_SESSION[$sess_customer_key] = $rec_user[$sess_customer_key];
	}
}
function clear_customer_session(){
	global $sess_customer_keys;
	foreach ($sess_customer_keys as $sess_customer_key){
		if (isset($_SESSION[$sess_customer_key])){
			unset($_SESSION[$sess_customer_key]);
		}
	}
}
/**
 * 用路径找到一个文件，用文件解包的方式判断文件类型是否为 jpg, gif 或 png 三种图像（只支持该三种，如果是其他图像，则返回 false
 * @param string $imgPath 文件路径
 * @return int|false 如果是 jpg/gif/png 其中之一，则返回其 type code。jpg:255216，gif:7173，png:13780，否则为 false
 */
function is_image($imgPath){ 
	$file  = fopen($imgPath, "rb"); 
	$bin  = fread($file, 2); // 只读2字节 

	fclose($file); 
	// 标识前两个字符按照，c格式，数组索引chars1、chars2
	$strInfo = unpack("C2chars", $bin); 
	$typeCode = intval($strInfo['chars1'].$strInfo['chars2']); 
	$fileType = ''; 

	if($typeCode == 255216 /*jpg*/ || $typeCode == 7173 /*gif*/ || $typeCode == 13780 /*png*/) { 
		return $typeCode; 
	}
	else {
		return false; 
	} 
}
/**
 * 二维码字符串图片绘制器。
 * 生成并返回不会产生歧义的大小写字母和数字组成的随机字符串，并将其绘制到 png 格式的图片输出
 * @param int $width 图片宽度，像素为单位。默认 110px
 * @param int $height 图片高度，像素为单位。默认 52px
 * @param int $font_size 绘制到图片上的字符字体大小，像素为单位。默认 24px
 * @param int $num_elements 随机字符的个数，默认 5 个
 * @param int $num_points 在图片上绘制随机干扰点的数量，默认 500 个
 * @param int $num_lines 在图片上绘制虽然干扰线段的数量，默认 10 条
 * @output 以 png 输出二维码图像
 * @return string 生成好的随机字符串
 */
function captcha($width = 110, $height = 52, $font_size = 24, $num_elements = 5, $num_points = 500, $num_lines = 10){
	header('Content-Type:image/png;charset=utf-8');
	$element = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '2', '3', '4', '5', '6', '7', '8'];
	$string = ''; //这行别乱改 
	//生成随机码，只使用 $element里的字符，去除了容易混淆的字符比如o，O，0
	for ($i = 0; $i < $num_elements; $i++) {
		$string .= $element[rand(0, count($element) - 1)];
	}
	// 创建画布
	$img = imagecreatetruecolor($width, $height);
	// 创建颜色，为画布
	$colorBg = imagecolorallocate($img, rand(0, 100), rand(0, 100), rand(0, 100));
	// 创建颜色，为文字
	$colorString = imagecolorallocate($img, rand(155, 255), rand(155, 255), rand(155, 255));
	//用调好的色用区域填充工具在(0,0)位置点一下，于是整个画布变色了
	imagefill($img, 0, 0, $colorBg);

	//如果验证码出现任何问题，请务必确认 .ttf 文件路径是否存在
	//window 不支持相对路径；只支持绝对路径。路径访问符：单正斜杠 (C:/1.ttf)，或双反斜杠 (C:\\1.ttf)
	//linux 既支持绝对路径 (/opt/nginx/html/res/1.ttf)；也支持相对路径 (res/1.ttf)
	//imagettftext($img,$font_size,rand(-5,5),rand(7,20),rand(40,50),$colorString,"/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf",$string);//把文字随机
	imagettftext($img, $font_size, rand(-3, 3), rand(3, 7), rand(32, 35), $colorString, 'res/LiberationMonoRegular.ttf', $string); //把文字随机
	//干扰线
	for ($i = 0; $i < $num_lines; $i++) {
		imageline($img, rand(0, intval($width / 2)), rand(0, $height), rand(intval($width / 2), $width), rand(0, $height), imagecolorallocatealpha($img, rand(0, 255), rand(0, 255), rand(0, 255), rand(30, 80)));
	}
	//打点循环，打num_points个点
	for ($i = 0; $i < $num_points; $i++) {
		imagesetpixel($img, rand(0, $width - 1), rand(0, $height - 1), imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255)));
	}
	imagepng($img);
	return strtoupper($string);
}
/**
 * 生成几乎永远唯一的密钥码字符串
 * sha512(返回128位) sha384(返回96位) sha256(返回64位) md5(返回32位)
 * 还有很多Hash函数......
 * @author xiaochaun (名字是不是拼错了？ —— 嫖者注)
 * @param int $type 返回格式：0大小写混合  1全大写  2全小写
 * @param string $func 启用算法。默认 'sha512'。取值范围可在当前 PHP 环境中执行 hash_algos() 函数后查看输出。
 * @return string 生成好的密钥字符串
 */
function create_secret($type = 0, $func = 'sha512'){
	$uid = md5(uniqid(rand(), true) . microtime());
	$hash = hash($func, $uid);
	$arr = str_split($hash);
	foreach ($arr as $v) {
		if ($type == 0) {
			$newArr[] = empty(rand(0, 1)) ? strtoupper($v) : $v;
		}
		if ($type == 1) {
			$newArr[] = strtoupper($v);
		}
		if ($type == 2) {
			$newArr[] = $v;
		}
	}
	return implode('', $newArr);
}
/**
 * 随机生成若干英文大写字母字符串
 * @param int 要生成几个英文大写字母
 * @return string 生成好的英文大写字母字符串
 */
function rand_cap($length){
	$ret = '';
	for ($i = 0; $i < $length; $i++) {
		$ret .= chr(rand(65, 90));
	}
	return $ret;
}
/**
 * 随机生成若干数字字符串
 * @param int $length 要生成几个数字
 * @return string 生成好的数字序列字符串
 */
function rand_num($length){
	$ret = '';
	for ($i = 0; $i < $length; $i++) {
		$ret .= chr(rand(48, 57));
	}
	return $ret;
}
/**
 * @param string $url String of URL
 * @return string A new URL includes only Scheme and Host.
 */
function get_merch_root($url){
	$pieces = parse_url($url);
	$ret = $pieces['scheme'] . '://' . $pieces['host'];
	if (isset($pieces['port'])) {
		$ret .= ':' . $pieces['port'];
	}
	$ret .= '/';
	return $ret;
}
/**
 * 返回数组的维度
 * @param array $arr 输入的变量，必须是数组
 * @return int 该数组的维度
 */
function array_dimensions($arr){
	$al = [0];
	function aL($arr, &$al, $level = 0){
		if (is_array($arr)) {
			$level++;
			$al[] = $level;
			foreach ($arr as $v){
				aL($v, $al, $level);
			}
		}
	}
	aL($arr, $al);
	return max($al);
}
/**
 * 对矩阵型数组做转置。
 * @param array &$input 矩阵型数组的引用
 * @return array | bool 转置后的数组
 */
function matrix_transposition(&$input){
	if (!is_array($input)){
		return false;
	}
	// if (array_dimensions($input) != 2){
	// 	return false;
	// }
	if (!isset($input[0])) {
		return false;
	}
	$cols = array_keys($input[0]);
	$res = [];
	foreach ($cols as $col){
		$res[$col] = array_column($input, $col);
	}
	return $input = $res;
}
/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo = true, $label = null, $strict = true){
	$label = ($label === null) ? '' : rtrim($label) . ' ';
	if (!$strict){
		if (ini_get('html_errors')){
			$output = print_r($var, true);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		}
		else{
			$output = $label . print_r($var, true);
		}
	}
	else{
		ob_start();
		$output = ob_get_clean();
		if (!extension_loaded('xdebug')){
			$output = preg_replace("/\]\=\>\n(\s+)/m", '] => ', $output);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		}
	}
	if ($echo){
		echo $output;
		return null;
	}
	else
		return $output;
}
function dump_web($var, $depth = 0, $refChain = [], $isRoot = true) {
	static $colors = [
		'string' => '#c41a16',
		'int' => 'rgb(78,154,6)',
		'float' => 'rgb(245,121,0)',
		'bool' => '#aa0d91',
		'null' => '#808080',
		'key' => '#BBB',
		'object' => '#0000aa',
		'array' => '#1c5f00',
	];
	if ($isRoot) {
		echo '<pre style="background:rgba(0,0,0,0.618);padding:10px;border:1px solid #ccc;border-radius:5px;font-family:monospace;font-size:14px;line-height:1.4em;">';
	}
	$indent = str_repeat(' ', $depth);
	$type = gettype($var);

	switch ($type) {
	case 'boolean':
		echo ' bool <span style="color:'.$colors['bool'].'">'.($var ? 'true' : 'false').'</span>'."\n";
		break;
	case 'integer':
		echo ' int <span style="color:' . $colors['int'].'">' . $var . '</span>' . "\n";
		break;
	case 'double':
		echo ' float <span style="color:'.$colors['float'].'">' . $var . '</span>' . "\n";
		break;
	case 'string':
		$len = strlen($var);
		$escaped = htmlspecialchars($var, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		echo ' string ('.$len.') <span style="color:'.$colors['string'].'">"'.$escaped.'"</span>'."\n";
		break;
	case 'NULL':
		echo ' <span style="color:' . $colors['null'] . '">NULL</span>' . "\n";
		break;
	case 'array':
		$count = count($var);
		echo $indent . '<span style="color:' . $colors['array'] . '">array(' . $count . ") {</span>\n";
		foreach ($var as $key => $value) {
			$k = is_string($key) ? '\'' . htmlspecialchars($key) . '\'' : $key;
			echo $indent . '  <span style="color:' . $colors['key'] . '">' . $k . '</span> <span style="color:rgb(136, 138, 133)">=></span>';
			dump_web($value, $depth + 2, $refChain, false);
		}
		echo $indent . "}\n";
		break;
	case 'object':
		$class = get_class($var);
		$hash = spl_object_hash($var);
		if (in_array($hash, $refChain)) {
			echo $indent . '<span style="color:' . $colors['object'] . '">object(' . $class . ') *RECURSION*</span>' . "\n";
			break;
		}
		$refChain[] = $hash;
		$props = (array) $var;
		echo $indent . '<span style="color:' . $colors['object'] . '">object(' . $class . ") {</span>\n";
		foreach ($props as $key => $value) {
			$visibleKey = preg_replace('/^\x00.*\x00/', '', $key);
			echo $indent . '  [<span style="color:' . $colors['key'] . '">' . $visibleKey . '</span>] <span style="color:rgb(136, 138, 133)">=></span> ';
			dump_web($value, $depth + 2, $refChain, false);
		}
		echo $indent . "}\n";
		break;

	case 'resource':
	case 'resource (closed)':
		echo $indent . '<span style="color:gray">' . $type . '</span>' . "\n";
		break;

	default:
		echo $indent . '<span>' . htmlspecialchars(print_r($var, true)) . '</span>' . "\n";
		break;
	}

	if ($depth === 0 && $isRoot) {
		echo '</pre>';
	}
}
/**
 * 数组据字段列名对应的显示列名（专用函数）
 * @param string $arg 数据库列名
 * @return string 显示列名
 */
function col_name($arg){
	switch($arg){
		case 'oid':
			return 'ID';
		case 'pid':
			return '会员ID';
		case 'trade_no':
			return '平台<span class="text-red">订单号</span>';
		case 'out_trade_no':
			return '商户<span class="text-red">订单号</span>';
		case 'name':
			return '商品名';
		case 'money':
			return '订单金额';
		case 'commission':
			return '费率';
		case 'random_discount_rate':
			return '随减率';
		case 'actual_amount':
			return '实际金额';
		case 'sitename':
			return '商户网站名';
		case 'order_place_time':
			return '创建时间';
		case 'pay_status':
			return '支付状态';
		case 'pay_time':
			return '支付时间';
		case 'payer':
			return '支付人';
		case 'notify_status':
			return '通知状态';
		case 'notify_time':
			return '最后通知时间';
		case 'notifier':
			return '通知人';
		case 'username':
			return '商户名';
		case 'nickname':
			return '昵称';
		case 'side':
			return '身份';
		case 'qq':
			return 'QQ 号';
		case 'reg_time':
			return '注册时间';
		case 'reg_ip':
			return '注册地IP';
		case 'last_time':
			return '上次登录时间';
		case 'last_ip':
			return '上次登录IP';
		case 'stat':
			return '状态';
		case 'balance':
			return '余额(实付剩余)';
		case 'level':
			return '会员等级';
		case 'key':
			return '商户私钥';
		case 'notify_method':
			return '通知方式';
		case 'qr_file':
			return '收款码';
		case 'commission_fee_rate':
			return '提现费率';
		case 'sid':
			return '结算ID';
		case 'time':
			return '结算时间';
		case 'before_balance':
			return '结算前';
		case 'amount':
			return '结算额';
		case 'after_balance':
			return '结算后';
		case 'operator':
			return '操作者';
		default:
			return '';
	}
}
/**
 * 通过数字判断该会员的身份名称。
 * @param int $side 身份的代号。目前只有 0:管理员 1:副管理员 2:书记员 3:副书记员 5:客服 10:商户。否则返回未定义。
 * @return string 身份名|未定义
 */
function side_name($side){
	switch ($side){
		case '0':
			return '管理员';
		case '1':
			return '客服';
		case '2':
			return '？';
		case '3':
			return '？';
		case '5':
			return '？';
		case '10':
			return '商户';
		default:
			return '未定义';
	}
}
/**
 * 会员状态指示器字符串模板
 * @param int $stat 会员状态代号，只能是 0 或 1。否则返回 html 标签模板“未知”
 * @return string <span> 标签字符串模板 0:正常 或 1:封禁。
 */
function stat_name($stat){
	switch ($stat){
		case 0:
			return '<span style="color:rgba(50,174,90,1);font-weight:500;font-size:inherit">正常</span>';
		case 1:
			return '<span style="color:rgba(204,30,80,1);font-weight:500;font-size:inherit">封禁</span>';
		default:
			return '<span style="color:rgba(204,30,80,1);font-weight:500;font-size:inherit">未知</span>';
	}
}
/**
 * 支付成功通知名
 * @param int 支付通知方式代码，只能是 0 或 1
 * @return string 支付通知方式字符串，0:'GET' 1:'POST'
 */
function notify_name($notify_method){
	switch ($notify_method){
		case 0:
			return 'GET';
		case 1:
			return 'POST';
	}
}
/**
 * 全面过滤支付请求参数（共 10 个）。包括参数形式合法性、逻辑合法性以及存在性。但不计算 sign 值匹配性（不验签）
 * @param mixed $_INPUT 支付请求输入的关联数组
 * @return array 关联数组，包含三个 key。'flag'=>(内部返回 0:成功 1:出错), 'content_chs'=>(外部显示中文结果), 'content_eng'=>(外部显示英文结果)
 */
function verify_input($_INPUT){
	$ret = [];
	$ret['flag'] = 0;
	$ret['content_chs'] = '成功！';
	$ret['content_eng'] = 'Done!';
	$arg_names = ['money', 'name', 'notify_url', 'out_trade_no', 'pid', 'return_url', 'sign', 'sign_type', 'sitename', 'type'];
	foreach ($_INPUT as $key => $value){
		if (!in_array($key, $arg_names)){
			$ret['flag'] = 1;
			$ret['content_chs'] = '不应该存在的参数名！';
			$ret['content_eng'] = 'An unexpected argument name!';
			return $ret;
		}
	}
	$conn = connect();
	$pid = $_INPUT['pid'];
	$money = $_INPUT['money'];
	$name = $_INPUT['name'];
	$notify_url = $_INPUT['notify_url'];
	$out_trade_no = $_INPUT['out_trade_no'];
	$return_url = $_INPUT['return_url'];
	$sitename = $_INPUT['sitename'];
	$type = $_INPUT['type'];
	$sign = strtolower($_INPUT['sign']);
	$sign_type = $_INPUT['sign_type'];
	// pid
	if (!is_numeric($pid) || floor($pid) != ceil($pid) || $pid <= 0){
		$ret['flag'] = 1;
		$ret['content_chs'] = 'pid 格式错误！';
		$ret['content_eng'] = 'pid format error!';
		return $ret;
	}
	$query_num_pid = "SELECT COUNT(*) FROM `user` WHERE `pid` = $pid";
	$result_num_pid = execute($conn, $query_num_pid);
	$data_num_pid = $result_num_pid->fetch_row()[0];
	if ($data_num_pid != 1){
		$ret['flag'] = 1;
		$ret['content_chs'] = '不存在的 pid！';
		$ret['content_eng'] = 'Nonexistent pid!';
		return $ret;
	}
	// money
	if (!is_numeric($money)){
		$ret['flag'] = 1;
		$ret['content_chs'] = '金额不是数字！';
		$ret['content_eng'] = 'Money is not a number!';
		return $ret;
	}
	$money *= 100;
	if (floor($money) != ceil($money) || $money <= 0){
		$ret['flag'] = 1;
		$ret['content_chs'] = '金额值错误！';
		$ret['content_eng'] = 'Money value error!';
		return $ret;
	}
	global $money_min;
	global $money_max;
	if ($money < $money_min || $money > $money_max){
		$ret['flag'] = 1;
		$ret['content_chs'] = '金额越界！';
		$ret['content_eng'] = 'Money exceeds the limit!';
		return $ret;
	}
	// name
	if ($name == ''){
		$ret['flag'] = 1;
		$ret['content_chs'] = '商品名为空！';
		$ret['content_eng'] = 'Product name shouldn\'t be empty!';
		return $ret;
	}
	$name_length = mb_strlen($name);
	if ($name_length > 20){
		$ret['flag'] = 1;
		$ret['content_chs'] = '金额越界！';
		$ret['content_eng'] = 'Money exceeds the limit!';
		return $ret;
	}
	// notify_url
	if (!filter_var($notify_url, FILTER_VALIDATE_URL)){
		$ret['flag'] = 1;
		$ret['content_chs'] = '通知地址格式错误！';
		$ret['content_eng'] = 'Notify url format error1!';
		return $ret;
	}
	global $notify_url_detect_timeout_seconds;
	if (!url_test($notify_url, $notify_url_detect_timeout_seconds)){
		$ret['flag'] = 1;
		$ret['content_chs'] = '通知地址探测失败！';
		$ret['content_eng'] = 'Notify URL unreachable!';
		return $ret;
	}
	// out_trade_no
	if ($out_trade_no == ''){
		$ret['flag'] = 1;
		$ret['content_chs'] = '商户订单号不能为空！';
		$ret['content_eng'] = 'Out trade number mustn\'t be empty!';
		return $ret;
	}
	$otn_length = strlen($out_trade_no);
	global $allow_chars;
	for ($i = 0; $i < $otn_length ; $i++){
		if (!in_array($out_trade_no[$i], $allow_chars)){
			$ret['flag'] = 1;
			$ret['content_chs'] = '商户订单号有意外字符！';
			$ret['content_eng'] = 'Unexpected char in out trade number!';
			return $ret;
		}
	}
	global $maxlen_out_trade_no;
	if ($otn_length > $maxlen_out_trade_no){
		$ret['flag`'] = 1;
		$ret['content_chs'] = '商户订单号过长！';
		$ret['content_eng'] = 'Out trade number too long!';
		return $ret;
	}
	$sql_otn_dup = "SELECT COUNT(*) FROM `order` WHERE `out_trade_no` = '$out_trade_no';";
	$result_otn_dup = execute($conn, $sql_otn_dup);
	$num_same_otns = $result_otn_dup->fetch_row()[0];
	if ($num_same_otns > 0){
		$ret['flag'] = 1;
		$ret['content_chs'] = '商户订单号已存在！';
		$ret['content_eng'] = 'The merchant\'s out trade number duplicates!';
	}
	// return_url
	if (!filter_var($return_url, FILTER_VALIDATE_URL)){
		$ret['flag'] = 1;
		$ret['content_chs'] = '通知地址格式错误！';
		$ret['content_eng'] = 'Return url format error1!';
		return $ret;
	}
	if (!url_test($return_url, $notify_url_detect_timeout_seconds)){
		$ret['flag'] = 1;
		$ret['content_chs'] = '返回地址探测失败！';
		$ret['content_eng'] = 'Return URL unreachable!';
		return $ret;
	}
	// sitename
	if ($sitename == ''){
		$ret['flag'] = 1;
		$ret['content_chs'] = '商品站点名不能为空！';
		$ret['content_eng'] = 'Sitename shouldn\'t be empty!';
		return $ret;
	}
	$sitename_length = mb_strlen($sitename);
	if ($sitename_length > 20){
		$ret['flag'] = 1;
		$ret['content_chs'] = '商品站点名太长(最多20个字)！';
		$ret['content_eng'] = 'Sitename too long! (Max: 20 words)';
		return $ret;
	}
	// type
	global $allow_pay_methods;
	if (!in_array($type, $allow_pay_methods)){
		$ret['flag'] = 1;
		$ret['content_chs'] = '不支持的支付方式！';
		$ret['content_eng'] = 'Unsupported pay method!';
		return $ret;
	}
	// sign
	global $hex_chars;
	$sign_length = strlen($sign);
	if ($sign_length != 32){
		$ret['flag'] = 1;
		$ret['content_chs'] = '签名值长度错误！';
		$ret['content_eng'] = 'Sign value length error!';
		return $ret;
	}
	for ($i = 0; $i < $sign_length; $i++){
		if (!in_array($sign[$i], $hex_chars)){
			$ret['flag'] = 1;
			$ret['content_chs'] = '签名值存在意外字符！';
			$ret['content_eng'] = 'Unexpected char in sign value!';
			return $ret;
		}
	}
	// sign_type
	global $allow_sign_types;
	if (!in_array($sign_type, $allow_sign_types)){
		$ret['flag'] = 1;
		$ret['content_chs'] = '不支持的签名类型！';
		$ret['content_eng'] = 'Unsupported sign type!';
		return $ret;
	}
	return $ret;
}
// function format_money($money_in_penny){

// }
/**
 * 用 curl 探测输入的 URL 地址是否可达。默认 2 秒没收到返回则判定不可达
 * @param string $url 要探测的 URL 地址
 * @param int $timeout_sec 以秒为单位的超时时间
 * @return int 执行 curl_getinfo 后的 CURLINFO_HTTP_CODE 代码。范围是 100 ~ 599 的标准 HTTP 状态码
 */
function url_test($url, $timeout_sec = 2){
	$curl = curl_init();
	$arr_opt = [
		CURLOPT_FOLLOWLOCATION => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_HEADER => 1,
		CURLOPT_TIMEOUT => $timeout_sec,
		CURLOPT_URL => $url,
	];
	curl_setopt_array($curl, $arr_opt);
	curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	return $code;
}
/**
 * 本支付平台专用验签函数。与其他平台参数结构不完全一致，谨慎搬运。
 * @param mixed $_INPUT 包含全部请求参数的关联数组。请在调用 verify_input() 函数获取返回值 $res['flag'] == 0 之后调用此函数
 * @param mixed $mkey 商户的私有密钥，存储于 `user`.`key`
 * @return bool true: 参数列表形式正确，且参数值签名运算将结果与 $_INPUT['sign'] 一致。false: 参数列表形式错误或签名值运算结果与 $_INPUT['sign'] 不一致
 */
function verify_sign($_INPUT, $mkey){
	if (empty($_INPUT)){
		return false;
	}
	if ($mkey == '' || $mkey == null){
		return false;
	}
	$arg_names = ['pid', 'money', 'name', 'notify_url', 'out_trade_no', 'return_url', 'sign', 'sign_type', 'sitename', 'type'];
	ksort($_INPUT);
	$str_sign = '';
	foreach ($_INPUT as $key => $value){
		if (!in_array($key, $arg_names)){
			return false;
		}
		if ($key != 'sign' && $key != 'sign_type'){
			$str_sign .= "$key=$value&";
		}
	}
	$str_sign = substr($str_sign, 0, -1);
	$sign = md5("$str_sign$mkey");
	if ($sign === $_INPUT['sign']){
		return true;
	}
	else{
		return false;
	}
}
/** 清除作为一个消费者的浏览器的所有 cookie 和所有 session。（不清理会员的 session 和 cookie）
 * 消费者的 session 字段有
 * oid=订单ID, pid=所属会员ID, customer=消费者名字, out_trade_no=消费者发起的当前订单的外部订单号, money=当前订单金额, order_place_time=当前订单发起时间
 */
function clear_customer(){
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	global $base_dir;
	if (isset($_COOKIE['oid'])){
		setcookie('oid', '', 0, $base_dir, '', true, true);
	}
	if (isset($_SESSION['oid'])){
		unset($_SESSION['oid']);
	}
	if (isset($_SESSION['pid'])){
		unset($_SESSION['pid']);
	}
	if (isset($_SESSION['out_trade_no'])){
		unset($_SESSION['out_trade_no']);
	}
	if (isset($_SESSION['money'])){
		unset($_SESSION['money']);
	}
	if (isset($_SESSION['order_place_time'])){
		unset($_SESSION['order_place_time']);
	}
}
/**
 * 
 */
function clear_member(){
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	global $base_dir;
	if (isset($_COOKIE['username'])){
		setcookie('username', '', 0, $base_dir, '', true, true);
	}
	if (isset($_SESSION['username'])){
		unset($_SESSION['username']);
	}
	if (isset($_SESSION['nickname'])){
		unset($_SESSION['nickname']);
	}
	if (isset($_SESSION['side'])){
		unset($_SESSION['side']);
	}
	if (isset($_SESSION['qq'])){
		unset($_SESSION['qq']);
	}
	if (isset($_SESSION['reg_time'])){
		unset($_SESSION['reg_time']);
	}
	if (isset($_SESSION['reg_ip'])){
		unset($_SESSION['reg_ip']);
	}
	if (isset($_SESSION['last_time'])){
		unset($_SESSION['last_time']);
	}
	if (isset($_SESSION['last_ip'])){
		unset($_SESSION['last_ip']);
	}
	if (isset($_SESSION['stat'])){
		unset($_SESSION['stat']);
	}
	if (isset($_SESSION['balance'])){
		unset($_SESSION['balance']);
	}
	if (isset($_SESSION['level'])){
		unset($_SESSION['level']);
	}
	if (isset($_SESSION['key'])){
		unset($_SESSION['key']);
	}
	if (isset($_SESSION['notify_method'])){
		unset($_SESSION['notify_method']);
	}
	if (isset($_SESSION['search_filter'])){
		unset($_SESSION['search_filter']);
	}
}
/**
 * 通过读取并比对 cookie 和 session 中的 username 和 oid 字段，确定当前浏览器使用者的身份。最终确定下来的身份只能是 “会员”，“消费者”，“两种都不是”，“两种都是” 其中之一。
 * @return string 身份名 'is_member' | 'is_customer' | 'neither' | 'both'
 */
function check_identity(){
	if (session_status() === PHP_SESSION_NONE) { // SESSION 未开启
		session_start();
	}
	// username 双边存在性
	if (isset($_COOKIE['username'])){ // cookie.username 存在
		if (isset($_SESSION['username'])){ // cookie.username 存在，session.username 存在
			if (isset($_COOKIE['oid'])){ // cookie.username 存在，session.username 存在，cookie.oid 存在
				if (isset($_SESSION['oid'])){ // cookie.username 存在，session.username 存在，cookie.oid 存在，session.oid 存在
					if ($_COOKIE['username'] == $_SESSION['username']){
						if ($_COOKIE['oid'] == $_SESSION['oid']){ // username 都存在且匹配，oid 都存在且匹配
							return 'both';
						}
						else{ // username 都存在且匹配，oid 都存在但不匹配
							clear_customer();
							return 'is_member';
						}
					}
					else{ // $_COOKIE['username'] != $_SESSION['username']
						if ($_COOKIE['oid'] == $_SESSION['oid']){ // username 都存在但不匹配，oid 都存在且匹配
							clear_member();
							return 'is_customer';
						}
						else{ // username 都存在不匹配，oid 都存在但不匹配
							clear_customer();
							clear_member();
							// file_put_contents('debug.txt', 'f1'.PHP_EOL, FILE_APPEND);
							return 'neither';
						}
					}
				}
				else{ // cookie.username 存在，session.username 存在，cookie.oid 存在，session.oid 不存在
					clear_customer();
					if ($_COOKIE['username'] == $_SESSION['username']){ // username 都存在且匹配，oid 不存在
						return 'is_member';
					}
					else{ // username 都存在但不匹配，oid 不存在
						clear_member();
						// file_put_contents('debug.txt', 'f2'.PHP_EOL, FILE_APPEND);
						return 'neither';
					}
				}
			}
			else{ // username 都存在，cookie.oid 不存在
				clear_customer();
				if ($_COOKIE['username'] == $_SESSION['username']){ // username 都存在且匹配，cookie.oid 不存在
					return 'is_member';
				}
				else{ // username 都存在但不匹配，cookie.oid 不存在
					clear_member();
					// file_put_contents('debug.txt', 'f3'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
			}
		}
		else{ // cookie.username 存在，session.username 不存在
			if (isset($_COOKIE['oid'])){ // cookie.username 存在，session.username 不存在，cookie.oid 存在
				if (isset($_SESSION['oid'])){ // cookie.username 存在，session.username 不存在，cookie.oid 存在，session.oid 存在
					if ($_COOKIE['oid'] == $_SESSION['oid']){ // cookie.username 存在，session.username 不存在，oid 都存在且匹配
						clear_member();
						return 'is_customer';
					}
					else{ // cookie.username 存在，session.username 不存在，oid 都存在但不匹配
						clear_member();
						clear_customer();
						// file_put_contents('debug.txt', 'f4'.PHP_EOL, FILE_APPEND);
						return 'neither';
					}
				}
				else{ // cookie.username 存在，session.username 不存在，cookie.oid 存在，session.oid 不存在
					clear_member();
					clear_customer();
					// file_put_contents('debug.txt', 'f5'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
			}
			else{ // cookie.username 存在，session.username 不存在，cookie.oid 不存在
				if (isset($_SESSION['oid'])){ // cookie.username 存在，session.username 不存在，cookie.oid 不存在，session.oid 存在
					clear_member();
					clear_customer();
					// file_put_contents('debug.txt', 'f6'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
				else{ // cookie.username 存在，session.username 不存在，oid 都不存在
					clear_member();
					// file_put_contents('debug.txt', 'f7'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
			}
		}
	}
	else{ // cookie.username 不存在
		if (isset($_SESSION['username'])){ // cookie.username 不存在，session.username 存在
			clear_member();
			if (isset($_COOKIE['oid'])){ // cookie.username 不存在，session.username 存在，cookie.oid 存在
				if (isset($_SESSION['oid'])){ // cookie.username 不存在，session.username 存在，oid 都存在
					if ($_COOKIE['oid'] == $_SESSION['oid']){ // cookie.username 不存在，session.username 存在，oid 都存在且匹配
						return 'is_customer';
					}
					else{ // cookie.username 不存在，session.username 存在，oid 都存在但不匹配
						clear_customer();
						// file_put_contents('debug.txt', 'f8'.PHP_EOL, FILE_APPEND);
						return 'neither';
					}
				}
				else{ // cookie.username 不存在，session.username 存在，cookie.oid 存在，session.oid 不存在
					clear_customer();
					// file_put_contents('debug.txt', 'f9'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
			}
			else{ // cookie.username 不存在，session.username 存在，cookie.oid 不存在
				if (isset($_SESSION['oid'])){ // cookie.username 不存在，session.username 存在，cookie.oid 不存在，session.oid 存在
					clear_customer();
					// file_put_contents('debug.txt', 'f10'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
				else{
					// file_put_contents('debug.txt', 'f11'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
			}
		}
		else{ // username 都不存在
			if (isset($_COOKIE['oid'])){ // username 都不存在，cookie.oid 存在
				// file_put_contents('debug.txt', $_SESSION['oid'].PHP_EOL, FILE_APPEND);
				if (isset($_SESSION['oid'])){ // username 都不存在，oid 都存在，
					if ($_COOKIE['oid'] == $_SESSION['oid']){ // username 都不存在，oid 都存在且匹配
						return 'is_customer';
					}
					else{ // username 都不存在，oid 都存在但不匹配，
						clear_customer();
						// file_put_contents('debug.txt', 'f12'.PHP_EOL, FILE_APPEND);
						return 'neither';
					}
				}
				else{ // username 都不存在，cookie.oid 存在，session.oid 不存在
					clear_customer();
					// file_put_contents('debug.txt', 'f13'.PHP_EOL, FILE_APPEND);
					// file_put_contents('debug.txt', 'session.oid='.$_SESSION['oid'].'cookie.oid='.$_COOKIE['oid'].PHP_EOL, FILE_APPEND);
					return 'neither';
				}
			}
			else{ // username 都不存在，cookie.oid 不存在
				if (isset($_SESSION['oid'])){ // username 都不存在，cookie.oid 不存在，session.oid 存在
					clear_customer();
					// file_put_contents('debug.txt', 'f14'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
				else{ // username 都不存在，oid 都不存在
					// file_put_contents('debug.txt', 'f15'.PHP_EOL, FILE_APPEND);
					return 'neither';
				}
			}
		}
	}
}
/**
 * 登录时，遍历所有 session，删除其中 username 值为 $username 的 session
 * @param mixed $username 登录时传入的请求登录用户名
 * @return void
 */
function clear_sess_by_user_name($username){
	$redis = new Redis([
		'host' => REDIS_SOCK,
		'port' => -1,
		'auth' => REDIS_AUTH
	]);
	$sess_keys = $redis->keys('*');
	foreach ($sess_keys as $value){ // 索引数组，$value 形式 PHPREDIS_SESSION:*<SESS_NAME>
		$content = $redis->get($value);
		$start = strpos($content, 'username|');
		if ($start !== false){
			$start = strpos($content, 'username|');
			$str1 = substr($content, $start);
			$quote_start_pos = strpos($str1, '"');
			$str2 = substr($str1, $quote_start_pos + 1);
			$quote_stop_pos = strpos($str2, '"');
			$sess_user_name = substr($str2, 0, $quote_stop_pos);
			if ($username == $sess_user_name){
				$redis->del($value);
			}
		}
	}
}
/**
 * 把 64 位的长用户私钥留头留尾，用省略号替代中部显示。用于在会员管理列表的表格中合理显示密钥值
 * @param string $key 输入的完整 64 位用户私钥。
 * @param int $pre_length key 的开始部分保留几位
 * @param int $suf_length key 的结尾部分保留几位
 * @return string 中间为省略号的缩略 key 字符串
 */
function short_key(string $key, int $pre_length = 3, int $suf_length = 3): string{
	$key_out = '';
	$key_out .= substr($key, 0, $pre_length).'...'.substr($key, -$suf_length);
	return $key_out;
}
/**
 * 把 0 和 1 转为为“未支付”和“已支付”
 * @param bool $status 0 或 1
 * @return string 已支付、未支付，输入错误返回 false
 */
function pay_status($status){
	if (!in_array($status, [0, 1])){
		return false;
	}
	switch ($status){
		case 0:
			return '<span style="color:rgba(255,50,50,1)">未支付</span>';
		case 1:
			return '<span style="color:rgba(50,184,50,1)">已支付</span>';
	}
}
/**
 * 将 zRange 取出来的数组 key 和 value 交换位置，构成 $score => $member 的效果。其中 $score 是存储在 redis 中的 14 位 unix 时间戳转为整数后的值。因为 zRange 取出来的数组 value($score) 是 float 类型，而 php 数组只能用 string 和 int 两种类型做 key，所以对 $score 调用了 substr 转为 string
 * @param array $arr_org 调用 zRange(WITHSCORES) 取出的原始数组
 * @return array 交换元素 key 和 value 并整理后的结果数组（关联数组）
 */
function zres_flip_14(&$arr_org){
	foreach ($arr_org as $key => $value){
		$value = substr($value, 0, 14);
		$res[$value] = json_decode($key, true);
	}
	$arr_org = $res;
	return $arr_org;
}
/**
 * 将 zRange 取出来的数组 key 和 value 交换位置，构成 $score => $member 的效果。其中 $score 是存储在 redis 中的分数。因为 zRange 取出来的数组 value($score) 是 float 类型，而 php 数组只能用 string 和 int 两种类型做 key，所以对 $score 调用了 substr 转为 string，会丢失小数部分。
 * @param array $arr_org 调用 zRange(WITHSCORES) 取出的原始数组
 * @return array 交换元素 key 和 value 并整理后的结果数组（关联数组）
 */
function zres_flip($input){
	$ret = [];
	foreach ($input as $member => $score){
		$score = (int)$score;
		$ret[$score] = $member;
	}
	return $ret;
}
/** 读取项目目录 img/qr-files/ 下的 wxpay, alipay, huabei 三个目录内的所有图片文件。
 * 以这些文件名去对应 redis 中的
 * 
 */
function qr_sync($redis){
	// 扫描微信文件夹，关联数组（因为需要得到 method）
	$fileinfo_wx = [];
	$filenames_wx = scandir('img/qr-files/wxpay');
	foreach ($filenames_wx as $filename_wx){
		if ($filename_wx != '..' && $filename_wx != '.' && $filename_wx != 'Thumbs.db'){
			if (is_image("img/qr-files/wxpay/$filename_wx")){
				$fileinfo_wx[] = [
					'filename' => $filename_wx,
					'method' => 'wxpay'
				];
			}
		}
	}
	// 扫描支付宝文件夹，关联数组（因为需要得到 method）
	$fileinfo_ali = [];
	$filenames_ali = scandir('img/qr-files/alipay');
	foreach ($filenames_ali as $filename_ali){
		if ($filename_ali != '..' && $filename_ali != '.' && $filename_ali != 'Thumbs.db'){
			if (is_image("img/qr-files/alipay/$filename_ali")){
				$fileinfo_ali[] = [
					'filename' => $filename_ali,
					'method' => 'alipay'
				];
			}
		}
	}
	$fileinfo_huabei = [];
	$filenames_huabei = scandir('img/qr-files/huabei');
	foreach ($filenames_huabei as $filename_huabei){
		if ($filename_huabei != '..' && $filename_huabei != '.' && $filename_huabei != 'Thumbs.db'){
			if (is_image("img/qr-files/huabei/$filename_huabei")){
				$fileinfo_huabei[] = [
					'filename' => $filename_huabei,
					'method' => 'huabei'
				];
			}
		}
	}
	// dump_web($fileinfo_ali);
	// dump_web($fileinfo_wx);
	// dump_web($fileinfo_huabei);
	// 合并两个文件夹的图片（空数组执行 merge 返回数组，不会报错）
	$dir_qrs = array_merge($fileinfo_wx, $fileinfo_ali, $fileinfo_huabei);
	// 连接 redis
	// $redis->Select(REDIS_DBNM_QPAY);
	if (empty($dir_qrs)){ // 文件目录为空
		// 清空 qr_imgs key，结束数据操作。
		$redis->Del(REDIS_ZKEY_QR_IMGS);
		return [];
	}
	// 目录非空，获取 redis 内所有元素
	// 如果成员的 score 值相同，返回的结果会根据成员值（member）的字典序进行排序
	$redis_qrs = $redis->zRange(REDIS_ZKEY_QR_IMGS, 0, -1);
	// 如果 redis 为空，但 dir 非空，则用 dir 完整填充 redis，顺便装入本页面 $redis_qrs 备用
	if (empty($redis_qrs)){
		foreach ($dir_qrs as $dir_qr){
			$temp_add = [
				"sequence" => 0,
				"filename" => $dir_qr['filename'],
				"method" => $dir_qr['method'],
				"text" => "",
				"comment" => "",
				"range" => 0
			];
			$redis_qrs[] = json_encode($temp_add);
			$redis->zAdd(REDIS_ZKEY_QR_IMGS, 0, json_encode($temp_add));
		}
		return $redis_qrs;
	}
	
	// 第一次同步遍历：先 redis，后 dir，用于从 redis 中删除 redis 中有，但 dir 中无的元素
	foreach ($redis_qrs as $index => $redis_qr_str){
		$redis_qr = json_decode($redis_qr_str, true);
		$exists = false;
		foreach ($dir_qrs as $dir_qr){
			if ($dir_qr['filename'] == $redis_qr['filename'] && $dir_qr['method'] == $redis_qr['method']){
				$exists = true;
				break;
			}
		}
		if (!$exists){
			unset($redis_qrs[$index]);
			$redis->zRem(REDIS_ZKEY_QR_IMGS, $redis_qr_str);
		}
	}
	// 第二次整理：先 dir，后 redis(已修整过一次)
	// 用于 1、修整 redis 库和内存的 method 值；2、把 dir 中新出现的 method/filename 添加到 redis range:0
	// 最后把数据装入内存，供本页面调用
	// dump_web($dir_qrs);
	// dump_web(json_decode($redis_qrs[0], true));
	// dump_web($dir_qrs);
	// dump_web($redis_qrs);
	// dump_web($dir_qrs);exit;
	foreach ($dir_qrs as $dir_qr){
		$exists = false;
		foreach ($redis_qrs as $i_redis => $redis_qr_str){
			$redis_qr = json_decode($redis_qr_str, true);
			// dump_web($redis_qr);
			// filename 相同, method 相同，内层遍历：下一个 (redis - continue)
			// echo "\$dir_qr['filename'] => {$dir_qr['filename']}<br>\$dir_qr['method'] => {$dir_qr['method']}<br>";
			// echo "\$redis_qr['filename'] => {$redis_qr['filename']}<br>\$redis_qr['method'] => {$redis_qr['method']}<br><br>";
			if ($dir_qr['filename'] == $redis_qr['filename'] && $dir_qr['method'] == $redis_qr['method']){
				// 当前 dir 中的 qr 在 redis_qrs 中存在，就结束 redis_qrs 遍历，同时结束外层本次循环
				$exists = true;
				break;
			}
		}
		if (!$exists){
			$qr_add = [
				"sequence" => 0,
				"filename" => $dir_qr['filename'],
				"method" => $dir_qr['method'],
				"text" => "",
				"comment" => "",
				"range" => 0	
			];
			// dump_web(json_encode($qr_add));
			$redis_qrs[] = json_encode($qr_add);
			$redis->zAdd(REDIS_ZKEY_QR_IMGS, 0, json_encode($qr_add));
		}
		
	}
	
	// 第三次遍历（暂时不需要。如果其他地方的布局更改无法自持，再实现下方代码）
	// 读出全部数据
	// $qr_org = $redis->zRange(REDIS_ZKEY_QR_IMGS, 0, -1);
	// foreach ($qr_org as $key => $value){

	// }
	// dump_web($redis_qrs);
	return $redis_qrs;
}
function img_method($method){
	switch ($method){
	case 'alipay':
		return '<svg class="alipay" viewBox="0 0 1024 1024" width="44" height="44"><path d="M1023.795 853.64v6.348a163.807 163.807 0 0 1-163.807 163.807h-696.18A163.807 163.807 0 0 1 0 859.988v-696.18A163.807 163.807 0 0 1 163.807 0h696.181a163.807 163.807 0 0 1 163.807 163.807V853.64z" fill="#009FE9" p-id="7028"></path><path d="M844.836 648.267c-40.952-14.333-95.623-34.809-156.846-57.128a949.058 949.058 0 0 0 90.094-222.573H573.325V307.14h245.711v-43.41l-245.71 2.458V143.33H472.173c-18.223 0-21.704 20.476-21.704 20.476v102.38H204.759v40.952h245.71v61.427H245.712v40.952h409.518a805.522 805.522 0 0 1-64.909 148.246c-128.384-42.795-266.186-77.604-354.233-55.08a213.564 213.564 0 0 0-112.003 63.27c-95.418 116.917-26.21 294.034 175.274 294.034 119.989 0 236.087-67.366 325.771-177.73 134.322 65.932 398.666 176.297 398.666 176.297V701.3s-32.352-4.095-178.96-53.033z m-563.702 144.97c-158.893 0-204.759-124.699-126.336-194.112a191.86 191.86 0 0 1 90.913-46.276c93.575-10.238 189.811 35.629 293.624 86.614-74.941 94.598-166.674 153.774-258.2 153.774z" fill="#FFFFFF" p-id="7029"></path></svg>';
	case 'wxpay':
		return '<svg class="wxpay" viewBox="0 0 1024 1024" width="44" height="44"><path d="M186.197333 0h651.605334C961.962667 0 1024 62.08 1024 186.197333v651.605334C1024 961.962667 961.962667 1024 837.802667 1024H186.197333C62.037333 1024 0 961.962667 0 837.802667V186.197333C0 62.037333 62.08 0 186.197333 0z" fill="#09BB07" p-id="6036"></path><path d="M404.096 596.266667a22.613333 22.613333 0 0 1-10.581333 2.432 23.253333 23.253333 0 0 1-20.48-12.074667l-1.706667-3.157333-64.597333-138.24c-0.810667-1.578667-0.810667-3.157333-0.810667-4.778667a11.093333 11.093333 0 0 1 11.52-11.264 13.226667 13.226667 0 0 1 7.338667 2.432l76.074666 53.034667a40.021333 40.021333 0 0 0 19.626667 5.546666 32.810667 32.810667 0 0 0 12.245333-2.389333l356.906667-155.776c-63.872-73.898667-169.386667-122.154667-288.938667-122.154667-194.730667 0-353.536 129.322667-353.536 289.109334 0 86.741333 47.488 165.461333 121.941334 218.453333 5.674667 4.053333 9.813333 11.264 9.813333 18.474667a21.930667 21.930667 0 0 1-1.706667 7.253333c-5.674667 21.632-15.573333 57.045333-15.573333 58.581333a28.458667 28.458667 0 0 0-1.706667 8.832 11.093333 11.093333 0 0 0 11.52 11.264 9.386667 9.386667 0 0 0 6.570667-2.389333l76.885333-44.16a39.893333 39.893333 0 0 1 18.816-5.589333c3.242667 0 7.381333 0.768 10.624 1.621333 37.546667 10.709333 76.373333 16.128 115.413334 16.085333 194.773333 0 353.536-129.322667 353.536-289.109333 0-48.213333-14.72-93.952-40.106667-134.186667l-406.613333 230.528-2.474667 1.621334z" fill="#FFFFFF" p-id="6037"></path></svg>';
	case 'huabei':
		return '<svg class="huabei" viewBox="0 0 1024 1024" width="44" height="44"><path d="M128 0h768C981.333333 0 1024 42.666667 1024 128v768c0 85.333333-42.666667 128-128 128H128C42.666667 1024 0 981.333333 0 896V128C0 42.666667 42.666667 0 128 0z" fill="#30B4FF" p-id="2724"></path><path d="M577.923879 83.642182c11.264-29.758061 63.550061-23.288242 120.707879 3.211636 154.422303 72.502303 261.368242 231.873939 261.368242 416.116364 0 252.617697-201.076364 457.029818-448 457.029818-247.683879 0-448-204.350061-448-457.076364 0-40.199758 3.196121-80.461576 15.297939-117.4496971 33.792-105.472 78.010182-164.988121 132.670061-144.911515 57.157818 20.945455 57.157818 69.197576 24.963879 118.287515-22.528 33.854061-82.881939 148.092121-32.19394 261.56994 17.733818 39.408485 78.848 129.536 135.928243 146.432 247.683879 73.216 260.608-99.017697 242.889697-185.095758-17.671758-86.078061-142.336-154.484364-199.431758-188.276363-56.32-33.792-52.286061-84.48-28.16-104.572122 24.126061-20.169697 126.277818 23.272727 196.297697 52.286061 70.718061 29.758061 189.750303 20.914424 193.008485-55.559758 3.971879-76.412121-77.234424-115.075879-106.992485-128.698181-29.758061-13.699879-70.795636-42.697697-60.353939-73.293576z m-0.775758 82.121697c40.96 16.135758 62.727758 54.721939 48.252121 86.140121-14.460121 31.356121-59.516121 44.218182-100.538181 28.16-40.96-16.135758-62.712242-54.721939-48.252122-86.140121 14.460121-31.356121 59.516121-44.218182 100.476122-28.16h0.06206z" fill="#FFFFFF" p-id="2725"></path></svg>';
	}
}
function echo_doc(){
	ob_start();
	global $api_submit;
?>
<div class="tab-title">业务 <span class="tab-title-sub">文档 / 说明</span></div>
<div class="doc-title">支付请求概览</div>
<div class="doc-text">URL地址: <span class="doc-text-key text-red"><?php echo $api_submit;?></span></div>
<div class="doc-text">注意事项:</div>
<div class="doc-text-sub"><b class="text-red">务必在前端</b>引导玩家向网关发起支付请求，这样玩家所见页面才能被引导向支付网关做<b class="text-red">跳转</b></div>
<div class="doc-text-sub"><b class="text-red">暂勿在后端</b>使用 curl 等工具向支付网关发起支付请求。</div>
<div class="doc-text-sub">* 就算后端发起成功，因为玩家浏览器无法跟随跳转，所以新生成订单会直接作废。</div>
<div class="doc-title">发起支付请求流程</div>
<div class="doc-text">发起支付请求允许使用 <b class="text-red">POST</b> 和 <b class="text-red">GET</b> 两种方式</div>
<div class="doc-text"><b class="text-red">POST</b> 方式说明</div>
<div class="doc-text-sub">在前端按要求组装出一个包含指定 INPUT 参数系列的 FORM 表单，输出到页面，引导用户跳转。</div>
<div class="doc-text"><b class="text-red">GET</b> 方式</div>
<div class="doc-text-sub">在网关地址字符串后拼接上 ?，再接上一系列参数，把最终的字符串写入玩家浏览器地址栏即可。</div>
<div class="doc-text">* 平台会自动识别 GET 方式或 POST 方式，两者只能居其一。不可以在同一个支付请求中载荷两种方式的数据。</div>
<div class="doc-title">参数说明</div>
<div class="doc-text">支付接口要求以下 <span style="background-color: rgba(0,255,0,0.08);border-radius:5px;">8 个数据参数</span>和 <span style="background-color: rgba(255,0,0,0.08);border-radius:5px;">2 个内置参数</span>，共计 10 个参数。（下表的参数顺序是已经 ksort 之后的顺序）</div>
<div class="arg-grid">
	<div class="arg-row arg-row-head" style="border-top-left-radius:10px;border-top-right-radius:10px;background-color:rgba(255,255,255,0.1);">
		<div class="arg-cell arg-cell-head">字段名</div>
		<div class="arg-cell arg-cell-head">变量名</div>
		<div class="arg-cell arg-cell-head">类型</div>
		<div class="arg-cell arg-cell-head">长度</div>
		<div class="arg-cell arg-cell-head">键值对范例</div>
		<div class="arg-cell arg-cell-head">描述</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">金额</div>
		<div class="arg-cell arg-cell-data">money</div>
		<div class="arg-cell arg-cell-data">number</div>
		<div class="arg-cell arg-cell-data">200 ~ 1000</div>
		<div class="arg-cell arg-cell-data">money=500</div>
		<div class="arg-cell arg-cell-data">元为单位，整数或最多两位小数</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">商品名</div>
		<div class="arg-cell arg-cell-data">name</div>
		<div class="arg-cell arg-cell-data">string</div>
		<div class="arg-cell arg-cell-data">最大 64 字节，中文占 3 个字节</div>
		<div class="arg-cell arg-cell-data">name=500 元直充</div>
		<div class="arg-cell arg-cell-data">建议设置为当前订单所购商品的品名，慎用特殊字符</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">通知地址</div>
		<div class="arg-cell arg-cell-data">notify_url</div>
		<div class="arg-cell arg-cell-data">string</div>
		<div class="arg-cell arg-cell-data">最大 255 字节</div>
		<div class="arg-cell arg-cell-data">notify_url=https://www.merchant-site.com/notify.php</div>
		<div class="arg-cell arg-cell-data">成功支付后，平台向商户网站发送支付成功的异步通知（不可带参数）</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">商户订单号</div>
		<div class="arg-cell arg-cell-data">out_trade_no</div>
		<div class="arg-cell arg-cell-data">string</div>
		<div class="arg-cell arg-cell-data">最大 32 字节</div>
		<div class="arg-cell arg-cell-data">out_trade_no=PID2_20240916074315930514</div>
		<div class="arg-cell arg-cell-data">仅支持字母、数字和下划线。请遵守订单号唯一的规范，不要提交重复订单号</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">商户ID</div>
		<div class="arg-cell arg-cell-data">pid</div>
		<div class="arg-cell arg-cell-data">unsigned int</div>
		<div class="arg-cell arg-cell-data">从 2 开始的正整数</div>
		<div class="arg-cell arg-cell-data">pid=5</div>
		<div class="arg-cell arg-cell-data">请商户在本站注册用户后，进入后台获取自己的 PID，自然整数，没有补零。</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">跳转地址</div>
		<div class="arg-cell arg-cell-data">return_url</div>
		<div class="arg-cell arg-cell-data">string</div>
		<div class="arg-cell arg-cell-data">最大 255 字节</div>
		<div class="arg-cell arg-cell-data">return_url=https://www.merchant-site.com/return.php</div>
		<div class="arg-cell arg-cell-data">自动支付开启时，当玩家在平台成功支付，被引导到商户网站的页面（不可带参数）</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">站点名</div>
		<div class="arg-cell arg-cell-data">sitename</div>
		<div class="arg-cell arg-cell-data">string</div>
		<div class="arg-cell arg-cell-data">最多 64 字节，中文占 3 个字节</div>
		<div class="arg-cell arg-cell-data">sitename=良心传奇打金服</div>
		<div class="arg-cell arg-cell-data">商户站点名称，提示玩家充值时，是向哪个网站充值</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">支付类型</div>
		<div class="arg-cell arg-cell-data">type</div>
		<div class="arg-cell arg-cell-data">string enum</div>
		<div class="arg-cell arg-cell-data">根据枚举值限定</div>
		<div class="arg-cell arg-cell-data">type=wxpay</div>
		<div class="arg-cell arg-cell-data">支付宝(借记): alipay，支付宝(贷记): huabei，微信支付: wxpay</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">签名字符串</div>
		<div class="arg-cell arg-cell-data">sign</div>
		<div class="arg-cell arg-cell-data">string</div>
		<div class="arg-cell arg-cell-data">固定 32 字节</div>
		<div class="arg-cell arg-cell-data">sign=202cb962ac59075b964b07152d234b70</div>
		<div class="arg-cell arg-cell-data">数据参数升序排序展开为字符串并接上 KEY，再执行 md5 后的结果</div>
	</div>
	<div class="arg-row arg-row-data">
		<div class="arg-cell arg-cell-data">签名类型</div>
		<div class="arg-cell arg-cell-data">sign_type</div>
		<div class="arg-cell arg-cell-data">string</div>
		<div class="arg-cell arg-cell-data">固定 3 字符</div>
		<div class="arg-cell arg-cell-data">sign_type=MD5</div>
		<div class="arg-cell arg-cell-data">执行签名算法时使用的加密函数，固定为大写 MD5。</div>
	</div>
</div>
<div class="doc-title">参数组装详细步骤与算法（以 POST 方式、php 语法为例讲解）</div>
<div class="doc-text"><b>一</b>、把上表中的<span style="background-color: rgba(50,255,120,0.2);border-radius:5px;padding:0 5px;margin-left:2px;">绿色背景的参数</span>（数据部分）表示为<b class="text-red">关联数组</b>（其他语言可能表述为 <b class="text-red">哈希表</b>、<b class="text-red">table</b>、<b class="text-red">字典</b>）形式：</div>
<div class="doc-text-sub"><img src="img/data-args.jpg"></div>
<div class="doc-text"><b>二</b>、上图数组执行 <b class="text-red">ksort</b> 升序排列</div>
<div class="doc-text-sub"><b class="text-red">ksort</b> 即根据数组的 key 做<b class="text-red">升序</b>排序，排列后的形式也在下图中：</div>
<div class="doc-text-sub">排列完成后，数组将变成如下顺序（下例中，每个元素 key 的顺序已按字母表升序从低到高排列）money 值的个位是元，可以是 100.00 格式，也可以直接就是 100。</div>
<div class="doc-text-sub"><img src="img/sorted-data-args.jpg"></div>
<div class="doc-text"><b>三</b>、遍历上图数组，展开键值对 "<b>key</b>=<b>value</b>" 形式，参数之间用 <b>&</b> 分割。</div>
<div class="doc-text-sub">具体算法：准备字符串 <b>$str_data</b>，遍历数据参数数组 <b>$data_args</b>，把每一个元素展开为 <b>$key</b>=<b>$value</b> 形式，拼接上参数分隔符 '<b>&</b>'，调用 <b>substr</b> 函数删掉最后一个多余的 '<b>&</b>'</div>
<div class="doc-text-sub"><img src="img/foreach.png"></div>
<div class="doc-text-sub">得到数据参数字符串 <b>$str_data</b> 形式如下：</div>
<div class="doc-text-sub"><span class="text-red">customer</span>=<span class="text-green">player1</span>&<span class="text-red">pid</span>=<span class="text-green">5</span>&<span class="text-red">money</span>=<span class="text-green">500</span>&<span class="text-red">name</span>=<span class="text-green">500 元直充</span>&<span class="text-red">notify_url</span>=<span class="text-green">https://www.merchant-site.com/notify.php</span>&<span class="text-red">out_trade_no</span>=<span class="text-green">PID2_20240916074315930514</span>&<span class="text-red">return_url</span>=<span class="text-green">https://www.merchant-site.com/return.php</span>&<span class="text-red">sitename</span>=<span class="text-green">良心传奇打金服</span>&<span class="text-red">type</span>=<span class="text-green">wxpay</span></div>
<div class="doc-text-sub">** 注意：签名时，<b class="text-red">请不要对 $str_args 数组执行 http_build_query 函数。</b>保持参数字符串全部为原始值，先不要对参数字符串做 url 加密。</div>
<div class="doc-text"><b>四</b>、把上述<b>原始</b>数据参数字符串与商户的密钥 <b>KEY</b> 做字符串拼接，拼接后，对整个字符串执行 <b>md5</b> 函数</div>
<div class="doc-text-sub">* 商户密钥请登录自己账号进入商户后台查看</div>
<div class="doc-text-sub"><img src="img/md5.png"></div>
<div class="doc-text-sub"><b>$sign</b> 是整个数据参数字符串的加密值，在此例中的值为 <b class="text-blue">a28e60ebdda9f380e6fb73d1de90e76d</b></div>
<div class="doc-text"><b>五</b>、把最后两个参数 <b class="text-red">sign</b>=<b class="text-green">a28e60ebdda9f380e6fb73d1de90e76d</b> 和 <b class="text-red">sign_type</b>=<b class="text-green">MD5</b> 两个键值对按顺序加入 <b>$data_args</b> 数组的最后两个位置，最终的请求参数数组看起来如下：</div>
<div class="doc-text-sub"><img src="img/final.jpg"></div>
<div class="doc-text"><b>六</b>、把最终的参数列表组装成 <b>form</b> 标签，向网关提交：</div>
<div class="doc-text-sub"><img src="img/submit.jpg"></div>
<div class="doc-text-sub">** 提交的方式无限制，但只能是 <b>GET</b> 或 <b>POST</b> 其中之一。不能同时存在两种数据</div>
<div class="doc-text-sub">** 组装完成后的 <b>form</b> 形式如下。</div>
<div class="doc-text-sub"><img src="img/html.jpg"></div>
<div class="doc-text"><b>七</b>、“平台”在支付完成后，会向商户网站 (<b>notify_url</b>)发起通知，并在“适当的时候”引导玩家跳转回自己所属的商户网站 (<b>return_url</b>)。</div>
<div class="doc-text-sub">通知 (<b>NOTIFY</b>) 方式，商户可以在后台自行设置 <b>POST</b> 或 <b>GET</b>。</div>
<div class="doc-text-sub">跳转 (<b>RETURN</b>) 目前只支持 <b>GET</b>（后期会根据需要再加入 <b>POST</b> 支持）。</div>
<div class="doc-text-sub">平台向商户发起的回调通知格式如下:</div>
<div class="doc-text-sub"><img src="img/notify.jpg"></div>
<div class="doc-text-sub">商户在 <b>notify_url</b> 页面成功收到通知，并验签无误后，请在发起订单时自定义的 <b>notify_url</b> 页面输出字符串 '<b>SUCCESS</b>'，平台即可完成通知。</div>
<div class="doc-text"><b>八</b>、“查询”、“设置”等接口将会在后续逐步开放。</div>
<?php
	$html_doc = ob_get_contents();
	ob_end_clean();
	return $html_doc;
}
function is_strict_indexed_array(array $arr): bool {
	return array_keys($arr) === range(0, count($arr) - 1);
}
/**
 * @param int $type 1生成昵称，2生成姓名
 * //汉语 - 给用户自动生成昵称
 */
function nickname($type = 1){
	/**
	 * 随机昵称 形容词
	 */
	$nicheng_tou = [
		'迷你的',
		'鲜艳的',
		'飞快的',
		'真实的',
		'清新的',
		'幸福的',
		'可耐的',
		'快乐的',
		'冷静的',
		'醉熏的',
		'潇洒的',
		'糊涂的',
		'积极的',
		'冷酷的',
		'深情的',
		'粗暴的',
		'温柔的',
		'可爱的',
		'愉快的',
		'义气的',
		'认真的',
		'威武的',
		'帅气的',
		'传统的',
		'潇洒的',
		'漂亮的',
		'自然的',
		'专一的',
		'听话的',
		'昏睡的',
		'狂野的',
		'等待的',
		'搞怪的',
		'幽默的',
		'魁梧的',
		'活泼的',
		'开心的',
		'高兴的',
		'超帅的',
		'留胡子的',
		'坦率的',
		'直率的',
		'轻松的',
		'痴情的',
		'完美的',
		'精明的',
		'无聊的',
		'有魅力的',
		'丰富的',
		'繁荣的',
		'饱满的',
		'炙热的',
		'暴躁的',
		'碧蓝的',
		'俊逸的',
		'英勇的',
		'健忘的',
		'故意的',
		'无心的',
		'土豪的',
		'朴实的',
		'兴奋的',
		'幸福的',
		'淡定的',
		'不安的',
		'阔达的',
		'孤独的',
		'独特的',
		'疯狂的',
		'时尚的',
		'落后的',
		'风趣的',
		'忧伤的',
		'大胆的',
		'爱笑的',
		'矮小的',
		'健康的',
		'合适的',
		'玩命的',
		'沉默的',
		'斯文的',
		'香蕉',
		'苹果',
		'鲤鱼',
		'鳗鱼',
		'任性的',
		'细心的',
		'粗心的',
		'大意的',
		'甜甜的',
		'酷酷的',
		'健壮的',
		'英俊的',
		'霸气的',
		'阳光的',
		'默默的',
		'大力的',
		'孝顺的',
		'忧虑的',
		'着急的',
		'紧张的',
		'善良的',
		'凶狠的',
		'害怕的',
		'重要的',
		'危机的',
		'欢喜的',
		'欣慰的',
		'满意的',
		'跳跃的',
		'诚心的',
		'称心的',
		'如意的',
		'怡然的',
		'娇气的',
		'无奈的',
		'无语的',
		'激动的',
		'愤怒的',
		'美好的',
		'感动的',
		'激情的',
		'激昂的',
		'震动的',
		'虚拟的',
		'超级的',
		'寒冷的',
		'精明的',
		'明理的',
		'犹豫的',
		'忧郁的',
		'寂寞的',
		'奋斗的',
		'勤奋的',
		'现代的',
		'过时的',
		'稳重的',
		'热情的',
		'含蓄的',
		'开放的',
		'无辜的',
		'多情的',
		'纯真的',
		'拉长的',
		'热心的',
		'从容的',
		'体贴的',
		'风中的',
		'曾经的',
		'追寻的',
		'儒雅的',
		'优雅的',
		'开朗的',
		'外向的',
		'内向的',
		'清爽的',
		'文艺的',
		'长情的',
		'平常的',
		'单身的',
		'伶俐的',
		'高大的',
		'懦弱的',
		'柔弱的',
		'爱笑的',
		'乐观的',
		'耍酷的',
		'酷炫的',
		'神勇的',
		'年轻的',
		'唠叨的',
		'瘦瘦的',
		'无情的',
		'包容的',
		'顺心的',
		'畅快的',
		'舒适的',
		'靓丽的',
		'负责的',
		'背后的',
		'简单的',
		'谦让的',
		'彩色的',
		'缥缈的',
		'欢呼的',
		'生动的',
		'复杂的',
		'慈祥的',
		'仁爱的',
		'魔幻的',
		'虚幻的',
		'淡然的',
		'受伤的',
		'雪白的',
		'高高的',
		'糟糕的',
		'顺利的',
		'闪闪的',
		'羞涩的',
		'缓慢的',
		'迅速的',
		'优秀的',
		'聪明的',
		'含糊的',
		'俏皮的',
		'淡淡的',
		'坚强的',
		'平淡的',
		'欣喜的',
		'能干的',
		'灵巧的',
		'友好的',
		'机智的',
		'机灵的',
		'正直的',
		'谨慎的',
		'俭朴的',
		'殷勤的',
		'虚心的',
		'辛勤的',
		'自觉的',
		'无私的',
		'无限的',
		'踏实的',
		'老实的',
		'现实的',
		'可靠的',
		'务实的',
		'拼搏的',
		'个性的',
		'粗犷的',
		'活力的',
		'成就的',
		'勤劳的',
		'单纯的',
		'落寞的',
		'朴素的',
		'悲凉的',
		'忧心的',
		'洁净的',
		'清秀的',
		'自由的',
		'小巧的',
		'单薄的',
		'贪玩的',
		'刻苦的',
		'干净的',
		'壮观的',
		'和谐的',
		'文静的',
		'调皮的',
		'害羞的',
		'安详的',
		'自信的',
		'端庄的',
		'坚定的',
		'美满的',
		'舒心的',
		'温暖的',
		'专注的',
		'勤恳的',
		'美丽的',
		'腼腆的',
		'优美的',
		'甜美的',
		'甜蜜的',
		'整齐的',
		'动人的',
		'典雅的',
		'尊敬的',
		'舒服的',
		'妩媚的',
		'秀丽的',
		'喜悦的',
		'甜美的',
		'彪壮的',
		'强健的',
		'大方的',
		'俊秀的',
		'聪慧的',
		'迷人的',
		'陶醉的',
		'悦耳的',
		'动听的',
		'明亮的',
		'结实的',
		'魁梧的',
		'标致的',
		'清脆的',
		'敏感的',
		'光亮的',
		'大气的',
		'老迟到的',
		'知性的',
		'冷傲的',
		'呆萌的',
		'野性的',
		'隐形的',
		'笑点低的',
		'微笑的',
		'笨笨的',
		'难过的',
		'沉静的',
		'火星上的',
		'失眠的',
		'安静的',
		'纯情的',
		'要减肥的',
		'迷路的',
		'烂漫的',
		'哭泣的',
		'贤惠的',
		'苗条的',
		'温婉的',
		'发嗲的',
		'会撒娇的',
		'贪玩的',
		'执着的',
		'眯眯眼的',
		'花痴的',
		'想人陪的',
		'眼睛大的',
		'高贵的',
		'傲娇的',
		'心灵美的',
		'爱撒娇的',
		'细腻的',
		'天真的',
		'怕黑的',
		'感性的',
		'飘逸的',
		'怕孤独的',
		'忐忑的',
		'高挑的',
		'傻傻的',
		'冷艳的',
		'爱听歌的',
		'还单身的',
		'怕孤单的',
		'懵懂的'
	];
	$nicheng_wei = [
		'嚓茶',
		'皮皮虾',
		'皮卡丘',
		'马里奥',
		'小霸王',
		'凉面',
		'便当',
		'毛豆',
		'花生',
		'可乐',
		'灯泡',
		'哈密瓜',
		'野狼',
		'背包',
		'眼神',
		'缘分',
		'雪碧',
		'人生',
		'牛排',
		'蚂蚁',
		'飞鸟',
		'灰狼',
		'斑马',
		'汉堡',
		'悟空',
		'巨人',
		'绿茶',
		'自行车',
		'保温杯',
		'大碗',
		'墨镜',
		'魔镜',
		'煎饼',
		'月饼',
		'月亮',
		'星星',
		'芝麻',
		'啤酒',
		'玫瑰',
		'大叔',
		'小伙',
		'哈密瓜，数据线',
		'太阳',
		'树叶',
		'芹菜',
		'黄蜂',
		'蜜粉',
		'蜜蜂',
		'信封',
		'西装',
		'外套',
		'裙子',
		'大象',
		'猫咪',
		'母鸡',
		'路灯',
		'蓝天',
		'白云',
		'星月',
		'彩虹',
		'微笑',
		'摩托',
		'板栗',
		'高山',
		'大地',
		'大树',
		'电灯胆',
		'砖头',
		'楼房',
		'水池',
		'鸡翅',
		'蜻蜓',
		'红牛',
		'咖啡',
		'机器猫',
		'枕头',
		'大船',
		'诺言',
		'钢笔',
		'刺猬',
		'天空',
		'飞机',
		'大炮',
		'冬天',
		'洋葱',
		'春天',
		'夏天',
		'秋天',
		'冬日',
		'航空',
		'毛衣',
		'豌豆',
		'黑米',
		'玉米',
		'眼睛',
		'老鼠',
		'白羊',
		'帅哥',
		'美女',
		'季节',
		'鲜花',
		'服饰',
		'裙子',
		'白开水',
		'秀发',
		'大山',
		'火车',
		'汽车',
		'歌曲',
		'舞蹈',
		'老师',
		'导师',
		'方盒',
		'大米',
		'麦片',
		'水杯',
		'水壶',
		'手套',
		'鞋子',
		'自行车',
		'鼠标',
		'手机',
		'电脑',
		'书本',
		'奇迹',
		'身影',
		'香烟',
		'夕阳',
		'台灯',
		'宝贝',
		'未来',
		'皮带',
		'钥匙',
		'心锁',
		'故事',
		'花瓣',
		'滑板',
		'画笔',
		'画板',
		'学姐',
		'店员',
		'电源',
		'饼干',
		'宝马',
		'过客',
		'大白',
		'时光',
		'石头',
		'钻石',
		'河马',
		'犀牛',
		'西牛',
		'绿草',
		'抽屉',
		'柜子',
		'往事',
		'寒风',
		'路人',
		'橘子',
		'耳机',
		'鸵鸟',
		'朋友',
		'苗条',
		'铅笔',
		'钢笔',
		'硬币',
		'热狗',
		'大侠',
		'御姐',
		'萝莉',
		'毛巾',
		'期待',
		'盼望',
		'白昼',
		'黑夜',
		'大门',
		'黑裤',
		'钢铁侠',
		'哑铃',
		'板凳',
		'枫叶',
		'荷花',
		'乌龟',
		'仙人掌',
		'衬衫',
		'大神',
		'草丛',
		'早晨',
		'心情',
		'茉莉',
		'流沙',
		'蜗牛',
		'战斗机',
		'冥王星',
		'猎豹',
		'棒球',
		'篮球',
		'乐曲',
		'电话',
		'网络',
		'世界',
		'中心',
		'鱼',
		'鸡',
		'狗',
		'老虎',
		'鸭子',
		'雨',
		'羽毛',
		'翅膀',
		'外套',
		'火',
		'丝袜',
		'书包',
		'钢笔',
		'冷风',
		'八宝粥',
		'烤鸡',
		'大雁',
		'音响',
		'招牌',
		'胡萝卜',
		'冰棍',
		'帽子',
		'菠萝',
		'蛋挞',
		'香水',
		'泥猴桃',
		'吐司',
		'溪流',
		'黄豆',
		'樱桃',
		'小鸽子',
		'小蝴蝶',
		'爆米花',
		'花卷',
		'小鸭子',
		'小海豚',
		'日记本',
		'小熊猫',
		'小懒猪',
		'小懒虫',
		'荔枝',
		'镜子',
		'曲奇',
		'金针菇',
		'小松鼠',
		'小虾米',
		'酒窝',
		'紫菜',
		'金鱼',
		'柚子',
		'果汁',
		'百褶裙',
		'项链',
		'帆布鞋',
		'火龙果',
		'奇异果',
		'煎蛋',
		'唇彩',
		'小土豆',
		'高跟鞋',
		'戒指',
		'雪糕',
		'睫毛',
		'铃铛',
		'手链',
		'香氛',
		'红酒',
		'月光',
		'酸奶',
		'银耳汤',
		'咖啡豆',
		'小蜜蜂',
		'小蚂蚁',
		'蜡烛',
		'棉花糖',
		'向日葵',
		'水蜜桃',
		'小蝴蝶',
		'小刺猬',
		'小丸子',
		'指甲油',
		'康乃馨',
		'糖豆',
		'薯片',
		'口红',
		'超短裙',
		'乌冬面',
		'冰淇淋',
		'棒棒糖',
		'长颈鹿',
		'豆芽',
		'发箍',
		'发卡',
		'发夹',
		'发带',
		'铃铛',
		'小馒头',
		'小笼包',
		'小甜瓜',
		'冬瓜',
		'香菇',
		'小兔子',
		'含羞草',
		'短靴',
		'睫毛膏',
		'小蘑菇',
		'跳跳糖',
		'小白菜',
		'草莓',
		'柠檬',
		'月饼',
		'百合',
		'纸鹤',
		'小天鹅',
		'云朵',
		'芒果',
		'面包',
		'海燕',
		'小猫咪',
		'龙猫',
		'唇膏',
		'鞋垫',
		'羊',
		'黑猫',
		'白猫',
		'万宝路',
		'金毛',
		'山水',
		'音响',
		'纸飞机',
		'烧鹅'
	];
	/**
	 * 百家姓
	 */
	$arrXing = [
		'赵',
		'钱',
		'孙',
		'李',
		'周',
		'吴',
		'郑',
		'王',
		'冯',
		'陈',
		'褚',
		'卫',
		'蒋',
		'沈',
		'韩',
		'杨',
		'朱',
		'秦',
		'尤',
		'许',
		'何',
		'吕',
		'施',
		'张',
		'孔',
		'曹',
		'严',
		'华',
		'金',
		'魏',
		'陶',
		'姜',
		'戚',
		'谢',
		'邹',
		'喻',
		'柏',
		'水',
		'窦',
		'章',
		'云',
		'苏',
		'潘',
		'葛',
		'奚',
		'范',
		'彭',
		'郎',
		'鲁',
		'韦',
		'昌',
		'马',
		'苗',
		'凤',
		'花',
		'方',
		'任',
		'袁',
		'柳',
		'鲍',
		'史',
		'唐',
		'费',
		'薛',
		'雷',
		'贺',
		'倪',
		'汤',
		'滕',
		'殷',
		'罗',
		'毕',
		'郝',
		'安',
		'常',
		'傅',
		'卞',
		'齐',
		'元',
		'顾',
		'孟',
		'平',
		'黄',
		'穆',
		'萧',
		'尹',
		'姚',
		'邵',
		'湛',
		'汪',
		'祁',
		'毛',
		'狄',
		'米',
		'伏',
		'成',
		'戴',
		'谈',
		'宋',
		'茅',
		'庞',
		'熊',
		'纪',
		'舒',
		'屈',
		'项',
		'祝',
		'董',
		'梁',
		'杜',
		'阮',
		'蓝',
		'闵',
		'季',
		'贾',
		'路',
		'娄',
		'江',
		'童',
		'颜',
		'郭',
		'梅',
		'盛',
		'林',
		'钟',
		'徐',
		'邱',
		'骆',
		'高',
		'夏',
		'蔡',
		'田',
		'樊',
		'胡',
		'凌',
		'霍',
		'虞',
		'万',
		'支',
		'柯',
		'管',
		'卢',
		'莫',
		'柯',
		'房',
		'裘',
		'缪',
		'解',
		'应',
		'宗',
		'丁',
		'宣',
		'邓',
		'单',
		'杭',
		'洪',
		'包',
		'诸',
		'左',
		'石',
		'崔',
		'吉',
		'龚',
		'程',
		'嵇',
		'邢',
		'裴',
		'陆',
		'荣',
		'翁',
		'荀',
		'于',
		'惠',
		'甄',
		'曲',
		'封',
		'储',
		'仲',
		'伊',
		'宁',
		'仇',
		'甘',
		'武',
		'符',
		'刘',
		'景',
		'詹',
		'龙',
		'叶',
		'幸',
		'司',
		'黎',
		'溥',
		'印',
		'怀',
		'蒲',
		'邰',
		'从',
		'索',
		'赖',
		'卓',
		'屠',
		'池',
		'乔',
		'胥',
		'闻',
		'莘',
		'党',
		'翟',
		'谭',
		'贡',
		'劳',
		'逄',
		'姬',
		'申',
		'扶',
		'堵',
		'冉',
		'宰',
		'雍',
		'桑',
		'寿',
		'通',
		'燕',
		'浦',
		'尚',
		'农',
		'温',
		'别',
		'庄',
		'晏',
		'柴',
		'瞿',
		'阎',
		'连',
		'习',
		'容',
		'向',
		'古',
		'易',
		'廖',
		'庾',
		'终',
		'步',
		'都',
		'耿',
		'满',
		'弘',
		'匡',
		'国',
		'文',
		'寇',
		'广',
		'禄',
		'阙',
		'东',
		'欧',
		'利',
		'师',
		'巩',
		'聂',
		'关',
		'荆',
		'司马',
		'上官',
		'欧阳',
		'夏侯',
		'诸葛',
		'闻人',
		'东方',
		'赫连',
		'皇甫',
		'尉迟',
		'公羊',
		'澹台',
		'公冶',
		'宗政',
		'濮阳',
		'淳于',
		'单于',
		'太叔',
		'申屠',
		'公孙',
		'仲孙',
		'轩辕',
		'令狐',
		'徐离',
		'宇文',
		'长孙',
		'慕容',
		'司徒',
		'司空',
		'皮'
	];
	/**
	 * 名
	 */
	$arrMing = [
		'伟',
		'刚',
		'勇',
		'毅',
		'俊',
		'峰',
		'强',
		'军',
		'平',
		'保',
		'东',
		'文',
		'辉',
		'力',
		'明',
		'永',
		'健',
		'世',
		'广',
		'志',
		'义',
		'兴',
		'良',
		'海',
		'山',
		'仁',
		'波',
		'宁',
		'贵',
		'福',
		'生',
		'龙',
		'元',
		'全',
		'国',
		'胜',
		'学',
		'祥',
		'才',
		'发',
		'武',
		'新',
		'利',
		'清',
		'飞',
		'彬',
		'富',
		'顺',
		'信',
		'子',
		'杰',
		'涛',
		'昌',
		'成',
		'康',
		'星',
		'光',
		'天',
		'达',
		'安',
		'岩',
		'中',
		'茂',
		'进',
		'林',
		'有',
		'坚',
		'和',
		'彪',
		'博',
		'诚',
		'先',
		'敬',
		'震',
		'振',
		'壮',
		'会',
		'思',
		'群',
		'豪',
		'心',
		'邦',
		'承',
		'乐',
		'绍',
		'功',
		'松',
		'善',
		'厚',
		'庆',
		'磊',
		'民',
		'友',
		'裕',
		'河',
		'哲',
		'江',
		'超',
		'浩',
		'亮',
		'政',
		'谦',
		'亨',
		'奇',
		'固',
		'之',
		'轮',
		'翰',
		'朗',
		'伯',
		'宏',
		'言',
		'若',
		'鸣',
		'朋',
		'斌',
		'梁',
		'栋',
		'维',
		'启',
		'克',
		'伦',
		'翔',
		'旭',
		'鹏',
		'泽',
		'晨',
		'辰',
		'士',
		'以',
		'建',
		'家',
		'致',
		'树',
		'炎',
		'德',
		'行',
		'时',
		'泰',
		'盛',
		'雄',
		'琛',
		'钧',
		'冠',
		'策',
		'腾',
		'楠',
		'榕',
		'风',
		'航',
		'弘',
		'秀',
		'娟',
		'英',
		'华',
		'慧',
		'巧',
		'美',
		'娜',
		'静',
		'淑',
		'惠',
		'珠',
		'翠',
		'雅',
		'芝',
		'玉',
		'萍',
		'红',
		'娥',
		'玲',
		'芬',
		'芳',
		'燕',
		'彩',
		'春',
		'菊',
		'兰',
		'凤',
		'洁',
		'梅',
		'琳',
		'素',
		'云',
		'莲',
		'真',
		'环',
		'雪',
		'荣',
		'爱',
		'妹',
		'霞',
		'香',
		'月',
		'莺',
		'媛',
		'艳',
		'瑞',
		'凡',
		'佳',
		'嘉',
		'琼',
		'勤',
		'珍',
		'贞',
		'莉',
		'桂',
		'娣',
		'叶',
		'璧',
		'璐',
		'娅',
		'琦',
		'晶',
		'妍',
		'茜',
		'秋',
		'珊',
		'莎',
		'锦',
		'黛',
		'青',
		'倩',
		'婷',
		'姣',
		'婉',
		'娴',
		'瑾',
		'颖',
		'露',
		'瑶',
		'怡',
		'婵',
		'雁',
		'蓓',
		'纨',
		'仪',
		'荷',
		'丹',
		'蓉',
		'眉',
		'君',
		'琴',
		'蕊',
		'薇',
		'菁',
		'梦',
		'岚',
		'苑',
		'婕',
		'馨',
		'瑗',
		'琰',
		'韵',
		'融',
		'园',
		'艺',
		'咏',
		'卿',
		'聪',
		'澜',
		'纯',
		'毓',
		'悦',
		'昭',
		'冰',
		'爽',
		'琬',
		'茗',
		'羽',
		'希',
		'欣',
		'飘',
		'育',
		'滢',
		'馥',
		'筠',
		'柔',
		'竹',
		'霭',
		'凝',
		'晓',
		'欢',
		'霄',
		'枫',
		'芸',
		'菲',
		'寒',
		'伊',
		'亚',
		'宜',
		'可',
		'姬',
		'舒',
		'影',
		'荔',
		'枝',
		'丽',
		'阳',
		'妮',
		'宝',
		'贝',
		'初',
		'程',
		'梵',
		'罡',
		'恒',
		'鸿',
		'桦',
		'骅',
		'剑',
		'娇',
		'纪',
		'宽',
		'苛',
		'灵',
		'玛',
		'媚',
		'琪',
		'晴',
		'容',
		'睿',
		'烁',
		'堂',
		'唯',
		'威',
		'韦',
		'雯',
		'苇',
		'萱',
		'阅',
		'彦',
		'宇',
		'雨',
		'洋',
		'忠',
		'宗',
		'曼',
		'紫',
		'逸',
		'贤',
		'蝶',
		'菡',
		'绿',
		'蓝',
		'儿',
		'翠',
		'烟'
	];
	switch ($type) {
		case 1:
			$tou_num = rand(0, count($nicheng_tou) - 1);
			$wei_num = rand(0, count($nicheng_wei) - 1);
			$nicheng = $nicheng_tou[$tou_num] . $nicheng_wei[$wei_num];
		case 2:
			$nicheng = $arrXing[mt_rand(0, count($arrXing) - 1)];
			for ($i = 1; $i <= 3; $i++) {
				$nicheng .= (mt_rand(0, 1) ? $arrMing[mt_rand(0, count($arrMing) - 1)] : $arrMing[mt_rand(0, count($arrMing) - 1)]);
			}
	}
	return $nicheng;
}