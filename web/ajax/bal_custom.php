<?php
/*
文件: ajax/bal_custom.php
发起: js/admin.js: 724
功能: 管理员用自定义日期，查询会员的玩家的提交的订单金额和已支付订单金额
输入: POST application/json
{
	"timeStart": , // 要查询的起始时间
	"timeEnd"; , // 要查询的结束时间
	"username" , // 要查询的用户名（因为查询动作是在弹出窗口实施，而弹出窗口没填会员 pid，就凑合直接发 username 了）
}
返回: 
{
	"flag": 0,
	"custom_submit_origin": <num>,
	"custom_payed_origin": <num>
	"custom_submit_actual": <num>
	"custom_payed_actual": <num>
}
flag:
	1: 输入 key 不合法
	2: 身份错误，只能是管理侧
	3: 输入 value 不合法
	4: 数据库查询阶段出错
	0: 一切正常，后 2 字段将生效

*/
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
$res = [];
// id filter
$identity = check_identity();
if ($identity != 'is_member'){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
if (!in_array($_SESSION['side'], $admin_side)){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
// input form filter
$input = file_get_contents('php://input');
if (empty($input)){
	$res['flag'] = 1; // 输入格式不合法
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST === null || !is_array($_POST)){
	$res['flag'] = 1; // 输入格式不合法
	echo json_encode($res);
	exit;
}
if (count($_POST) != 3){
	$res['flag'] = 1; // 输入格式不合法
	echo json_encode($res);
	exit;
}
if (!isset($_POST['timeStart']) || !isset($_POST['timeEnd']) || !isset($_POST['username'])){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
// input content filter
$start = strtotime($_POST['timeStart']);
$end = strtotime($_POST['timeEnd']);
if ($start === false || $end === false){
	$res['flag'] = 3;
	echo json_encode($res);
	exit;
}
$username = $_POST['username'];
$username_length = strlen($username);
for ($i = 0; $i < $username_length; $i++){
	if (!in_array($username[$i], $allow_chars)){
		$res['flag'] = 3;
		echo json_encode($res);
		exit;
	}
}
$res['flag'] = 0;
$start = date('Y-m-d H:i:s', $start);
$end = date('Y-m-d H:i:s', $end);
$conn = connect();
$username = mysqli_real_escape_string($conn, $username);
$sql =
"SELECT
	SUM(`money`) AS `custom_submit_origin`
FROM
	`order`
WHERE
	`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username')
	AND
	`order_place_time` >= '$start'
	AND
	`order_place_time` <= '$end';
SELECT
	SUM(`money`) AS `custom_payed_origin`
FROM
	`order`
WHERE
	`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username')
	AND
	`order_place_time` >= '$start'
	AND
	`order_place_time` <= '$end'
	AND
	`pay_status` = 1;
SELECT
	SUM(`actual_amount`) AS `custom_submit_actual`
FROM
	`order`
WHERE
	`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username')
	AND
	`order_place_time` >= '$start'
	AND
	`order_place_time` <= '$end';
SELECT
	SUM(`actual_amount`) AS `custom_payed_actual`
FROM
	`order`
WHERE
	`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username')
	AND
	`order_place_time` >= '$start'
	AND
	`order_place_time` <= '$end'
	AND
	`pay_status` = 1;

	";
if ($conn->multi_query($sql)){
	do{
		if ($result = $conn->store_result()){
			$res = array_merge($res, $result->fetch_assoc());
			$result->free();
		}
		$conn->more_results();
	} while($conn->next_result());
}
else{
	$res['flag'] = 4;
	$res['error'] = $conn->error;
	echo json_encode($res);
}
foreach ($res as $key => $value){
	if ($value === null){
		$res[$key] = '0';
	}
}
echo json_encode($res);
/*
flag:
1 输入字符串形式不合法（包括为空，无法反序列化，key数量不对，key名称不对）
2 身份不符
3 输入值内容 value 部分不合法（非时间）
4 数据库查询出错
*/