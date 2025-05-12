<?php
const VALID = true;
include '../../lib/config.php';
include '../../lib/mysql.php';
include '../lib/tools.php';
session_start();
$res = [];
$identity = check_identity();
if ($identity == 'both'){
	$res['flag'] = 1; // 双重身份，去 ../who.php 裁决
	echo json_encode($res);
	exit;
}
if ($identity == 'neither'){
	$res['flag'] = 2; // 无身份
	echo json_encode($res);
	exit;
}
$conn = connect();
$talker = '';
$rank = null;
if ($identity == 'is_member'){
	if (in_array($_SESSION['side'], $merch_side)){ // 已登录商家，弹到 ../merch.php
		$res['flag'] = 3; // 注册商家
		echo json_encode($res);
		exit;
	}
	$talker = $_SESSION['username']; // 管理者用户名
	$rank = 0;
}
else if ($identity == 'is_customer'){ // 消费者
	$query_mname = "SELECT `username` FROM `user` WHERE `pid` = {$_SESSION['pid']};";
	$result_mname = execute($conn, $query_mname);
	$merchant = $result_mname->fetch_row()[0]; // 消费者所属商家用户名
	$customer = $_SESSION['customer'];
	$talker = "$merchant:$customer"; // zset key 名，消费者时，消息中显示名
	$rank = -1;
}
header("cache-Control: no-store, no-cache, must-revalidate");
header("pragma: no-cache");
header("expires: 0");
$str_data = file_get_contents('php://input');
$arr_data = json_decode($str_data, true);
if ($arr_data == null || empty($arr_data)){
	$res['flag'] = 4; // 输入字符串不规范
	echo json_encode($res);
	exit;
}
if (!isset($arr_data['action']) || !in_array($arr_data['action'], ['get', 'put'])){
	$res['flag'] = 5; // 输入 action 值不合规
	echo json_encode($res);
	exit;
}
$action = $arr_data['action'];
$ip = $_SERVER['REMOTE_ADDR'];
$redis = new Redis(
	[
		'host' => REDIS_SOCK,
		'port' => -1,
		'auth' => REDIS_AUTH
	]
);
$redis->select(REDIS_DBNM_CHAT);
if ($action == 'put'){// PUT
	$type = $arr_data['type'];
	$content = $arr_data['content'];
	if ($rank == -1){ // USER PUT
		$for_store = [
			'who' => $talker,
			'rank' => $rank,
			'ip' => $ip,
			'type' => $type,
			'content' => $content,
		];
		$time14 = str_pad(str_replace('.', '', microtime(true)), 14, '0', STR_PAD_RIGHT);
		$res = [];
		if ($redis->zAdd($for_store['who'], $time14, json_encode($for_store))){
			$res = [
				'flag' => true
			];
		}
		else{
			$res = [
				'flag' => false
			];
		}
		echo json_encode($res);
		exit;
	}
	else if ($rank == 0){ // ADMIN PUT
		$talker_to = $arr_data['talker_to']; // from post.talker_to - active listItem
		$for_store = [
			'who' => $talker, // from $_SESSION
			'rank' => $rank,
			'ip' => $ip,
			'type' => $type,
			'content' => $content
		];
		$time14 = str_pad(str_replace('.', '', microtime(true)), 14, '0', STR_PAD_RIGHT);
		$res = [];
		if ($redis->zAdd($talker_to, $time14, json_encode($for_store))){ // zkey is talker_to for Admin
			$res = [
				'flag' => true
			];
		}
		else{
			$res = [
				'flag' => false
			];
		}
		echo json_encode($res);
		exit;
	}
}
else if ($action == 'get'){// GET
	if (!isset($arr_data['is_init']) || !in_array($arr_data['is_init'], [true, false])){
		$res['flag'] = 7;
		echo json_encode($res);
		exit;
	}
	// 用户刚进页面（无论是否新用户）
	if ($arr_data['is_init']){ //GET INIT
		// $messages = $redis->zRange($talker, 0, 14, ['WITHSCORES' => true, 'REV']);
		if ($rank == -1){ // USER GET INIT
			$messages = $redis->zRange($talker, 0, -1, ['WITHSCORES' => true]);
			if (empty($messages)){ // USER FIRST GET INIT
				$time14 = str_pad(str_replace('.', '', microtime(true)), 14, '0', STR_PAD_RIGHT);
				$for_store = [
					'who' => 'admin',
					'rank' => 0,
					'ip' => $ip,
					'type' => 'text',
					'content' => $chat_init_content,
				];
				$redis->zAdd($talker, $time14, json_encode($for_store));
				$console_init[$time14] = $for_store;
				$console_init[$time14]['avatar'] = 'service.jpg';
				echo json_encode($console_init);
				exit;
			}
			zres_flip_14($messages);
			foreach ($messages as $time14 => $message){
				$msg_rank = $message['rank'];
				switch ($msg_rank){
					case -1:
						$messages[$time14]['avatar'] = 'client3.png';
						break;
					case 0:
						$messages[$time14]['avatar'] = 'service.jpg';
						break;
				}
			}
			echo json_encode($messages);
			exit;
		}
		else if ($rank == 0){// ADMIN GET INIT
			$db_names = $redis->keys('*');
			if (empty($db_names)){
				$res['flag'] = 0;
				echo json_encode($res);
				exit;
			}
			// $db_names 非空
			$arr_full = [];
			foreach ($db_names as $db_name){ // 对每一个 db_name 取聊天内容
				$messages = $redis->zRange($db_name, 0, -1, ['WITHSCORES' => true]);
				zres_flip_14($messages);
				foreach ($messages as $time14 => $message){
					$msg_rank = $message['rank'];
					switch ($msg_rank){
						case -1:
							$messages[$time14]['avatar'] = 'client3.png';
							break;
						case 0:
							$messages[$time14]['avatar'] = 'service.jpg';
							break;
					}
				}
				$arr_full[$db_name] = $messages;
			}
			echo json_encode($arr_full);
			exit;
		}
	}
	else{ // GET REGULAR ($arr_data['is_init'] == false)
		if ($rank == -1){ // USER GET REGULAR
			$last_time = $arr_data['last_time'];
			if (!is_numeric($last_time) || floor($last_time) != ceil($last_time)){
				$res['flag'] = 4;
				echo json_encode($res);
				exit;
			}
			$messages = $redis->zRange($talker, "($last_time", 'inf', ['WITHSCORES' => true, 'BYSCORE']);
			if (empty($messages)){ // 消息增量为空
				$res['flag'] = 0;
				echo json_encode($res);
				exit;
			}
			zres_flip_14($messages); // 消息有增量
			ksort($messages);
			foreach ($messages as $time14 => $message){
				$msg_rank = $message['rank'];
				switch ($msg_rank){
					case -1:
						$messages[$time14]['avatar'] = 'client3.png';
						break;
					case 0:
						$messages[$time14]['avatar'] = 'service.jpg';
						break;
				}
			}
			echo json_encode($messages);
			exit;
		}
		else if ($rank == 0){ // ADMIN GET REGULAR
			$last_times = $arr_data['last_times'];
			$db_names = $redis->keys('*');
			if (empty($db_names)){
				$res['flag'] = 0;
				echo json_encode($res);
				exit;
			}
			if (count($db_names) < count($last_times)){
				$res['flag'] = 8;
				echo json_encode($res);
				exit;
			}
			$res = []; // 每一个玩家的消息增量集合
			foreach ($db_names as $db_name){ // loop names in db
				$start_score = null;
				if (!array_key_exists($db_name, $last_times)){ // 库内有新玩家，从头开始取消息
					$start_score = 0;
				}
				else{ // 库内旧玩家，从最后节点开始取
					$start_score = '('.$last_times[$db_name];
				}
				$new_messages = $redis->zRange($db_name, $start_score, 'inf', ['WITHSCORES' => true, 'BYSCORE']);
				// echo json_encode(empty($new_messages));exit;
				if (!empty($new_messages)){ // 该玩家增量不为空
					zres_flip_14($new_messages);
					foreach ($new_messages as $time14 => $message){
						$msg_rank = $message['rank'];
						switch ($msg_rank){
							case -1:
								$new_messages[$time14]['avatar'] = 'client3.png';
								break;
							case 0:
								$new_messages[$time14]['avatar'] = 'service.jpg';
								break;
						}
					}
				}
				else{ // 该用户无增量
					$new_messages = null;
				}
				$res[$db_name] = $new_messages;
			}
			echo json_encode($res);
		}
	}
}

/* 接收数据
$action 客户端发来数据标志位
	'send': 客户端主动发送数据
	'loop': 客户端定时获取数据
*/
/* 返回数据
$code
	0: 一切正常
	1: 未登录
	2: 地址更换
*/