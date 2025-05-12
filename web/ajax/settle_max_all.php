<?php
/*
文件: ajax/settle_max_all.php
功能:
	全体 max 按钮后端清洗后，前端设置
发起: js/admin.js: 1134
输入:
[
	{
		"pid": <pid>,
		"balance_front
	}
]
输出:
{
	"flag": <flag>
	"data": {

	}
}
<flag>
	1: 身份错误（必须管理侧）
	2: 输入生格式错误
	3: 输入解包后格式不规范
	0: 成功，并包含 data 字段
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
if (empty($_POST) || !is_array($_POST)){
	$res['flag'] = 2; // 输入格式不合法
	echo json_encode($res);
	exit;
}
if (!is_strict_indexed_array($_POST)){
	$res['flag'] = 3; // 输入形式不合法
	echo json_encode($res);
	exit;
}
$sql_merch =
"SELECT
	`pid`,
	`balance`
FROM
	`user`
WHERE
	`side` >= 10
AND
	`stat` = 0;";
$mysql = connect();
$result_merch = $mysql->query($sql_merch);
$data_merch = $result_merch->fetch_all(MYSQLI_ASSOC);
foreach ($data_merch as &$arr){
	$arr['balance'] = number_format($arr['balance'] / 100, 2, '.', '');
}
unset($value);
// 比对余额（要不要比对待定）
/* 数据库结果形式
{
	0 => [
		'pid' => 2,
		'balance' => 100.00
	],
	1 => [
		'pid' => 3,
		'balance' => 230.00
	]
}
*/
$res = [
	'flag' => 0,
	'data' => $data_merch
];
echo json_encode($res);