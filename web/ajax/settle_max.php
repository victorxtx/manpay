<?php
/*
文件: ajax/settle_max.php
功能:
	提现单体 max 按钮后端清洗后，前端设置
	把前端发来的显示余额和数据库内做对比
	若一致，返回允许让前端结算额设置为显示余额
	若不一致，返回让前端弹窗提示余额已有变化
发起: admin.js: 963
输入: 
{
	'pid': <pid>,
	'balanceFront': <balance_front>
}
输出: 
{
	"flag": <flag>,
	"balance_back": <balance_back>
}
<flag>:
	0: ok
	1: 身份不是管理侧
	2: 输入内容格式为空、不符合JSON规范，参数个数不对，参数名称不对
	3: 参数值形式、内容有错
	4: 前端 > 后端
		"balance_back": <balance_back>
	5: 前端 < 后端
		"balance_back": <balance_back>
	0: 前端 == 后端
		"balance_back": <balance_back>
*/
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
$res = [];
$identity = check_identity();
if ($identity != 'is_member'){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
if (!in_array($_SESSION['side'], $admin_side)){
	$res['flag'] = 1;
	echo json_encode($res);
	exit;
}
$input = file_get_contents('php://input');
if (empty($input)){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
$_POST = json_decode($input, true);
if ($_POST === null || !is_array($_POST)){
	$res['flag'] = 2; // 输入格式不合法
	echo json_encode($res);
	exit;
}
if (count($_POST) != 2){
	$res['flag'] = 2; // 输入格式不合法
	echo json_encode($res);
	exit;
}
if (!isset($_POST['pid']) || !isset($_POST['balance_front'])){
	$res['flag'] = 2;
	echo json_encode($res);
	exit;
}
$pid = $_POST['pid'];
$balance_front = $_POST['balance_front'];
if (!is_numeric($pid) || floor($pid) != ceil($pid)){
	$res['flag'] = 3;
	echo json_encode($res);
	exit;
}
if (!is_numeric($balance_front)){
	$res['flag'] = 3;
	echo json_encode($res);
	exit;
}
// 入库
$mysql = connect();
$sql =
"SELECT
	`balance`
FROM
	`user`
WHERE
	`pid` = $pid;";
$result = $mysql->query($sql);
$data = $result->fetch_row();
$balance_back = number_format($data[0] / 100, 2, '.', '');
if ($balance_front > $balance_back){ // 其他有管理权限的人已为该 pid 提现，导致后端余额比前端更小
	$res['flag'] = 4;
}
if ($balance_front < $balance_back){ // 管理员进入页面之后，又有玩家向该 pid 提交支付请求并完成支付，导致后端余额更大
	$res['flag'] = 5;
}
if ($balance_front == $balance_back){ // 前端余额 == 后端余额
	$res['flag'] = 0;
}
// 到这里，前后端的“可提现余额值”一致，所以前端在 case 0 时只需关心 settleable 值，不必关心余额显示差异
// 可提现额，是账户余额 $balance_back 乘以当前费率 $commission_fee_rate
$res['balance_back'] = $balance_back;
echo json_encode($res);