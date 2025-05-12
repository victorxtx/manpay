<?php
/*
文件: ajax/filter_search.php
发起:
	js/admin.js: 377
	js/merch.js: 312
验证: 是
输入: POST application/json

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
$str_data = file_get_contents('php://input');
$_INPUT = json_decode($str_data, true);
if ($_INPUT == null || empty($_INPUT) || !isset($_INPUT['search_string'])){
	$res['code'] = 'input_error';
	echo json_encode($res);
	exit;
}
$search_string = $_INPUT['search_string'];
$input_length = strlen($search_string);
if ($input_length > 32){
	$res['code'] = 'too_long';
	echo json_encode($res);
	exit;
}
for ($i = 0; $i < $input_length; $i++){
	if (!in_array($search_string[$i], $allow_chars)){
		$res['code'] = 'forbidden_char';
		$res['char'] = $search_string[$i];
		echo json_encode($res);
		exit;
	}
}
// cols for order
$str_order_cols = '';
foreach ($order_cols as $col){
	$str_order_cols .= "`$col`,";
}
$str_order_cols = substr($str_order_cols, 0, -1);
$side = $_SESSION['side'];
$username = $_SESSION['username'];
$sql_num_orders = '';
$sql_orders = '';
if (in_array($side, $admin_side)){ // 管理侧搜索订单
	$sql_num_orders = // 总订单数，用于分页
	"SELECT
		COUNT(*)
	FROM
		`order`
	WHERE
		`trade_no` = '$search_string'
	OR
		`out_trade_no` = '$search_string';";
	$sql_orders = // 具体订单数据
	"SELECT
		$str_order_cols
	FROM
		`order`
	WHERE
		`trade_no` = '$search_string'
	OR
		`out_trade_no` = '$search_string'
	LIMIT 0, $orders_per_page;";
}
else if (in_array($side, $merch_side)){
	$sql_num_orders =
	"SELECT
		COUNT(*)
	FROM
		`order`
	WHERE
		(`trade_no` = '$search_string' OR `out_trade_no` = '$search_string')
	AND
		`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username');";
	$sql_orders =
	"SELECT
		$str_order_cols
	FROM
		`order`
	WHERE
		(`trade_no` = '$search_string' OR `out_trade_no` = '$search_string')
	AND
		`pid` = (SELECT `pid` FROM `user` WHERE `username` = '$username')
	LIMIT 0, $orders_per_page;";
}
$conn = connect();
$result_num_orders = execute($conn, $sql_num_orders);
$num_orders = $result_num_orders->fetch_row()[0];
$num_pages = ceil($num_orders / $orders_per_page);
$result_orders = execute($conn, $sql_orders);
if ($result_orders->num_rows == 0){
	$res['code'] = 'empty_set';
	echo json_encode($res);
	exit;
}
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
$res['code'] = 'ok';
$res['html'] = ob_get_contents();
ob_end_clean();
ob_start();
?>
<div class="page-grid">
<?php
if ($num_pages > 1){
	for ($i = 0; $i < $num_pages; $i++){
	?>
	<div class="button blue btn-order-page<?php if($i==1)echo' disabled';echo" order-page-$i";?>">第 <?php echo $i + 1;?> 页</div>
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