<?php
function get_sessions_from_redis($redis){
	$result = [];
	foreach ($redis->keys('PHPREDIS_SESSION*') as $key_index => $red_key_name){
		$str_value_all = $redis->get($red_key_name);
		$count_semicolon = substr_count($str_value_all, ';');
		if ($count_semicolon < 1){
			$redis->del($red_key_name);
			$result[$red_key_name] = [];
		}
		else if ($count_semicolon == 1){
			$str_value = substr($str_value_all, 0, -1);
			$arr_kv = explode('|', $str_value);
			$fin_val = trim(explode(':', $arr_kv[1], 3)[2], "\"");
			$temp[$arr_kv[0]] = $fin_val;
			$result[$red_key_name] = $temp;
			unset($temp);
		}
		else if ($count_semicolon > 1){
			$sess_kvs = explode(';', $str_value_all);
			foreach ($sess_kvs as $v_index => $single_kv){
				if ($single_kv != ''){
					$arr_kv = explode('|', $single_kv);
					$temp[$arr_kv[0]] = trim(explode(':', $arr_kv[1], 3)[2],"\"");
				}
			}
			$result[$red_key_name] = $temp;
			unset($temp);
		}
	}
	return $result;
	/*
	返回形式：
	array = array(
		"PHPREDIS_SESSION:hpd5ter7c6qg8qpfo8j10erqh8pvul6bjaosmaaffh36keoodpc78s4ca0q8d7ei" => array(
			'captcha' => 'cX4yv',
			'user' => 'abc'
		),
		"PHPREDIS_SESSION:7k5k1qfr9hlvi7th2ark0c8jab0br90d12h8fme32u2l576qs8uoa6ok1317ctdm" => array(
			'captcha' => 'kksjJ',
			'user' => 'cde'
		)
	);
	*/
}
function zres_flip_14(&$arr_org){
	foreach ($arr_org as $key => $value){
		$value = substr($value, 0, 14);
		$res[$value] = json_decode($key, true);
	}
	$arr_org = $res;
	return $arr_org;
}
function zres_flip($input){
	$res = [];
	foreach ($input as $name => $zset){
		foreach ($zset as $content => $time){
			$res[$name][$time] = $content;
		}
	}
	return $res;
}
function zres_vd($arr){
	foreach ($arr as $name => $zset){
		echo '&apos;'.$name.'&apos; => array (<br>';
		foreach ($zset as $key => $value){
			echo '<div style="margin-left:20px">&apos;'.$key.'&apos; => &apos;'.$value.'&apos;</div>';
		}
		echo '),<br>';
	}
}
function img2base64($file){
	$fp = fopen($file, 'rb', 0);
	$img_stream = fread($fp, filesize($file));
	fclose($fp);
	$str_b64 = chunk_split(base64_encode($img_stream));
	return $str_b64;
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
		var_dump($var);
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
/**
 * 根据登录身份的页面路由
 * @return string 'is_member' | 'is_customer' | 'neither' | 'both'
 */
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
	if (isset($_SESSION['customer'])){
		unset($_SESSION['customer']);
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
 * 根据登录身份的页面路由
 * 用 cookie 和 session 里的 username 字段值是否相等，判定是否是用户 is_member
 * 用 cookie 和 session 里的 oid 字段值是否相等，判定是否是玩家 is_customer
 * @return string 身份名 'is_member' | 'is_customer' | 'neither' | 'both'
 */
function check_identity(){
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	// username 双边存在性
	if (isset($_COOKIE['username'])){
		if (isset($_SESSION['username'])){
			if (isset($_COOKIE['oid'])){ // username 都存在，cookie.oid 存在
				if (isset($_SESSION['oid'])){ // username 都存在，oid 都存在
					if ($_COOKIE['username'] == $_SESSION['username']){
						if ($_COOKIE['oid'] == $_SESSION['oid']){ // username 都存在且匹配，oid 都存在且匹配
							return 'both';
						}
						else{ // username 都存在且匹配，oid 都存在但不匹配
							clear_customer();
							return 'is_member';
						}
					}
					else{
						if ($_COOKIE['oid'] == $_SESSION['oid']){ // username 都存在但不匹配，oid 都存在且匹配
							clear_member();
							return 'is_customer';
						}
						else{ // username 都存在不匹配，oid 都存在但不匹配
							clear_customer();
							clear_member();
							return 'neither';
						}
					}
				}
				else{ // username 都存在，cookie.oid 存在，session.oid 不存在
					clear_customer();
					if ($_COOKIE['username'] == $_SESSION['username']){ // username 都存在且匹配，oid 不存在
						return 'is_member';
					}
					else{ // username 都存在但不匹配，oid 不存在
						clear_member();
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
						return 'neither';
					}
				}
				else{ // cookie.username 存在，session.username 不存在，cookie.oid 存在，session.oid 不存在
					clear_member();
					clear_customer();
					return 'neither';
				}
			}
			else{ // cookie.username 存在，session.username 不存在，cookie.oid 不存在
				if (isset($_SESSION['oid'])){ // cookie.username 存在，session.username 不存在，cookie.oid 不存在，session.oid 存在
					clear_member();
					clear_customer();
					return 'neither';
				}
				else{ // cookie.username 存在，session.username 不存在，oid 都不存在
					clear_member();
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
						return 'neither';
					}
				}
				else{ // cookie.username 不存在，session.username 存在，cookie.oid 存在，session.oid 不存在
					clear_customer();
					return 'neither';
				}
			}
			else{ // cookie.username 不存在，session.username 存在，cookie.oid 不存在
				if (isset($_SESSION['oid'])){ // cookie.username 不存在，session.username 存在，cookie.oid 不存在，session.oid 存在
					clear_customer();
					return 'neither';
				}
				else{
					return 'neither';
				}
			}
		}
		else{ // username 都不存在
			if (isset($_COOKIE['oid'])){ // username 都不存在，cookie.oid 存在
				if (isset($_SESSION['oid'])){ // username 都不存在，oid 都存在，
					if ($_COOKIE['oid'] == $_SESSION['oid']){ // username 都不存在，oid 都存在且匹配
						return 'is_customer';
					}
					else{ // username 都不存在，oid 都存在但不匹配，
						clear_customer();
						return 'neither';
					}
				}
				else{ // username 都不存在，cookie.oid 存在，session.oid 不存在
					clear_customer();
					return 'neither';
				}
			}
			else{ // username 都不存在，cookie.oid 不存在
				if (isset($_SESSION['oid'])){ // username 都不存在，cookie.oid 不存在，session.oid 存在
					clear_customer();
					return 'neither';
				}
				else{ // username 都不存在，oid 都不存在
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