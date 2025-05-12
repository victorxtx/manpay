<?php
/*
文件: ajax/order_page.php
功能: 为会员在订单管理页提供订单分页显示 html 代码
类型: AJAX
发起:
	js/admin.js: 572
	js/merch.js: 448
验证: 是
输入: GET
{
	"t": <timestamp>, // 10 位 unix 时间戳 + 3 位小数
	"p": <num_page>, // 若页面存在页按钮，则 i 表示索引从 0 开始的按钮元素引用序号
}
输出: string/html
	页码为 i 的所有订单显示 html

	
*/
const VALID = true;
include_once '../lib/config.php';
include_once '../lib/mysql.php';
include_once '../lib/tools.php';
session_start();
$res = [];
$identity = check_identity();
switch ($identity){
case 'is_customer':
	$res['code'] = 'id_customer';
	echo json_encode($res);
	exit;
case 'is_neither':
	$res['code'] = 'id_neither';
	echo json_encode($res);
	exit;
case 'both':
	$res['code'] = 'id_both';
	echo json_encode($res);
	exit;
}
// 过滤 t
$t = $_GET['t'];
if (!is_numeric($t) || floor($t) != ceil($t)){
	$res['code'] = 't_error'; // 参数 t 违规
	echo json_encode($res);
	exit;
}
// 过滤 p
$p = $_GET['p'];
if (!is_numeric($p) || floor($p) != ceil($p)){
	$res['code'] = 'p_error'; // 参数 p 违规
	echo json_encode($res);
	exit;
}
$stat = $_SESSION['stat'];
switch ($stat){
	case 1:
		$res['code'] = 'user_banned';
		echo json_encode($res);
		exit;
	case 2:
		$res['code'] = 'user_reserve';
		echo json_encode($res);
		exit;
}
$side = $_SESSION['side'];
$username = $_SESSION['username'];
$str_search_filter = '';
$search_filter = $_SESSION['search_filter'];
$sql_num_orders = '';
$sql_orders = '';
$orders = [];
$conn = connect();
$offset = $p * $orders_per_page;
$str_order_cols = '';
foreach ($order_cols as $col){
	$str_order_cols .= "`$col`,";
}
$str_order_cols = substr($str_order_cols, 0, -1);
if (in_array($side, $admin_side) && $search_filter == 0){ // 管理员 | 未处理订单（最小集合）
	$sql_num_orders =
	"SELECT
		COUNT(*)
	FROM
		`order`
	WHERE
		`pay_status` = 0
	AND
		`notify_status` = 0;";
	$sql_orders =
	"SELECT
		$str_order_cols
	FROM
		`order`
	WHERE
		`pay_status` = 0
	AND
		`notify_status` = 0
	ORDER BY
		`oid` DESC
	LIMIT $offset, $orders_per_page;";
}
else if (in_array($side, $admin_side) && $search_filter == 1){ // 管理员 | 含已支付但未通知
	$sql_num_orders =
	"SELECT
		COUNT(*)
	FROM
		`order`
	WHERE
		`notify_status` = 0;";
	$sql_orders =
	"SELECT
		$str_order_cols
	FROM
		`order`
	WHERE
		`notify_status` = 0
	ORDER BY
		`oid` DESC
	LIMIT $offset, $orders_per_page;";
}
else if (in_array($side, $admin_side) && $search_filter == 2){ // 管理员 | 所有订单
	$sql_num_orders =
	"SELECT
		COUNT(*)
	FROM
		`order`;";
	$sql_orders =
	"SELECT
		$str_order_cols
	FROM
		`order`
	ORDER BY
		`oid` DESC
	LIMIT $offset, $orders_per_page;";
}
else if (in_array($side, $merch_side) && $search_filter == 0){
	$sql_num_orders =
	"SELECT
		COUNT(*)
	FROM
		`order`
	WHERE
		`pay_status` = 0
	AND
		`notify_status` = 0
	AND
		`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username');";
	$sql_orders =
	"SELECT
		$str_order_cols
	FROM
		`order`
	WHERE
		`pay_status` = 0
	AND
		`notify_status` = 0
	AND
		`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username')
	ORDER BY
		`oid` DESC
	LIMIT $offset, $orders_per_page;";
}
else if (in_array($side, $merch_side) && $search_filter == 1){
	$sql_num_orders =
	"SELECT
		COUNT(*)
	FROM
		`order`
	WHERE
		`notify_status` = 0
	AND
		`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username');";
	$sql_orders =
	"SELECT
		$str_order_cols
	FROM
		`order`
	WHERE
		`notify_status` = 0
	AND
		`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username')
	ORDER BY
		`oid` DESC
	LIMIT $offset, $orders_per_page;";
}
else if (in_array($side, $merch_side) && $search_filter == 2){
	$sql_num_orders =
	"SELECT
		COUNT(*)
	FROM
		`order`
	WHERE
		`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username');";
	$sql_orders =
	"SELECT
		$str_order_cols
	FROM
		`order`
	WHERE
		`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username')
	ORDER BY
		`oid` DESC
	LIMIT $offset, $orders_per_page;";
}
$result_num_orders = execute($conn, $sql_num_orders);
$num_orders = $result_num_orders->fetch_row()[0];
$num_pages = ceil($num_orders / $orders_per_page);
$result_orders = execute($conn, $sql_orders);
$orders = $result_orders->fetch_all(MYSQLI_ASSOC);
matrix_transposition($orders);
ob_start();
?>
<div class="order-grid">
<?php
foreach ($orders as $col_name => $col_value){ // 列名遍历，横排
?>
	<div class="cell-column">
	<?php // 每次列包内表头行
	if ($col_name == 'notify_status'){
	?>
		<div class="order-cell order-cell-head" style="text-align:center"><?php echo col_name($col_name);?></div>
	<?php
	}
	else if ($col_name == 'name'){
	?>
		<div class="order-cell order-cell-head cell-data-center" style="text-align:center"><?php echo col_name($col_name);?></div>
	<?php
	}
	else if ($col_name == 'pay_status'){
	?>
		<div class="order-cell order-cell-head" style="text-align:center"><?php echo col_name($col_name);?></div>
	<?php
	}
	else{
	?>
		<div class="order-cell order-cell-head"><?php echo col_name($col_name);?></div>
	<?php
	}
	?>
	<?php
	foreach ($col_value as $key => $value){ // 列包内部，纵排
		if ($col_name == 'pay_status'){
		?>
		<div class="order-cell order-cell-data" style="width:112px;justify-content:space-between">
			<?php
			if ($value){ // pay_status == 1
			?>
			<div class="status pay-status status-yes">已支付</div>
			<div class="do-pay payed">已付</div>
			<?php
			}
			else{ // pay_status == 0
			?>
			<div class="status pay-status status-no">未支付</div>
			<div class="do-pay">支付</div>
			<?php
			}
			?>
		</div>
		<?php
		}
		else if ($col_name == 'oid'){
		?>
		<div class="order-cell order-cell-data order-cell-oid"><?php echo $value;?></div>
		<?php
		}
		else if ($col_name == 'pid'){
		?>
		<div class="order-cell order-cell-data order-cell-pid"><?php echo $value;?></div>
		<?php
		}
		else if ($col_name == 'out_trade_no'){
		?>
		<div class="order-cell order-cell-data" style="max-width:480px"><?php echo $value;?></div>
		<?php
		}
		else if ($col_name == 'random_discount_rate'){
		?>
		<div class="order-cell order-cell-data"><?php echo number_format($value * 100, 2, '.', '').'%';?></div>
		<?php
		}
		else if ($col_name == 'money' || $col_name == 'actual_amount'){
		?>
		<div class="order-cell order-cell-data cell-data-center"><?php echo number_format($value / 100, 2);?></div>
		<?php
		}
		else if ($col_name == 'name'){
		?>
		<div class="order-cell order-cell-data cell-data-center"><?php echo $value;?></div>
		<?php
		}
		else if ($col_name == 'notify_status'){
		?>
		<div class="order-cell order-cell-data" style="width:112px;justify-content:space-between">
			<?php
			if ($value){ // notify_status == 1
			?>
			<div class="status notify-status status-yes">已通知</div>
			<?php
			}
			else{
			?>
			<div class="status notify-status status-no">未通知</div>
			<?php
			}
			?>
			<div class="do-notify">补发</div>
		</div>
		<?php
		}
		else if ($col_name == 'notify_time' || $col_name == 'pay_time'){
			$class_name = $col_name == 'notify_time' ? 'notify-time' : 'pay-time';
		?>
		<div class="order-cell order-cell-data cell-data-center <?php echo $class_name;?>"><?php if ($value)echo $value;else echo '-';?></div>
		<?php
		}
		else if ($col_name == 'notifier' || $col_name == 'payer'){
		?>
		<div class="order-cell order-cell-data cell-data-center <?php echo $col_name;?>"><?php if ($value)echo $value;else echo '-';?></div>
		<?php
		}
		else{
		?>
		<div class="order-cell order-cell-data"><?php echo $value;?></div>
		<?php
		}
	}
	?>
	</div>
<?php
}
?>
</div>
<?php
$html = ob_get_contents();
$res['code'] = 'ok';
$res['html'] = $html;
ob_end_clean();
ob_start();

?>
<div class="page-grid">
<?php
if ($num_pages > 1){
	for ($i = 0; $i < $num_pages; $i++){
	?>
	<div class="button blue btn-order-page<?php if($i==$p)echo' disabled';echo" order-page-$i";?>">第 <?php echo $i + 1;?> 页</div>
	<?php
	}
}
else{
?>
	<div class="button blue btn-order-page disabled order-page-0">第 1 页</div>
<?php
}
?>
</div>
<?php
$res['page'] = ob_get_contents();
ob_end_clean();
echo json_encode($res);