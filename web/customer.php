<?php
const VALID = true;
include_once 'lib/config.php';
include_once 'lib/mysql.php';
include_once 'lib/tools.php';
session_start();
$identity = check_identity();
if ($identity != 'is_customer') {
?>
	<script type="text/javascript">
		location.replace('./');
	</script>
<?php
	exit;
}
$oid = $_COOKIE['oid'];
$conn = connect();
$sql_orders =
"SELECT
	`trade_no`, `out_trade_no`, `sitename`, `name`, `money`, `order_place_time`, `pay_status`, `pay_time`
FROM
	`order`
WHERE
	`oid` = $oid;";
$result_orders = execute($conn, $sql_orders);
$data_orders = mysqli_fetch_all($result_orders, MYSQLI_ASSOC);
matrix_transposition($data_orders);
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
	<link rel="stylesheet" type="text/css" href="css/customer.css">
	<link rel="stylesheet" type="text/css" href="css/alert.css">
	<link rel="icon" href="img/rmb.svg">
	<title>临时订单表</title>
</head>
<body>
	<div class="main">
		<div class="title">临时订单表</div>
		<div class="description">
			<div class="desc">* 你好勇士！看到这个页面，说明您曾经通过您所在的网站向本平台发起过支付订单。</div>
			<div class="desc">* 下面的表格，是根据您的浏览器查询到的可能属于您的订单。</div>
			<div class="desc">* 您可以 <button class="btn btn-discard">放弃</button> 这些订单，并离开这个页面；</div>
			<div class="desc">* 您也可以复制下面的订单号，向平台 <button class="btn btn-chat">发起对话</button>，完成订单支付！</div>
			<div class="desc">* </div>
		</div>
		<div class="table">
		<?php
		foreach ($data_orders as $col => $cells){
		?>
			<div class="col-wrap">
				<div class="col-cell col-head"><?php echo col_name($col);?></div>
			<?php
			foreach ($cells as $num => $cell){
				if ($col == 'money'){
				?>
				<div class="col-cell col-data"><?php echo number_format($cell / 100, 2, '.', '').' 元';?></div>
				<?php
				}
				else if ($col == 'pay_status'){
				?>
				<div class="col-cell col-data col-data-pay-status"><?php echo pay_status($cell);?></div>
				<?php
				}
				else if ($col == 'pay_time'){
				?>
				<div class="col-cell col-data col-order-complete-time"><?php if($cell==null)echo'-';else echo$cell;?></div>
				<?php
				}
				else{
				?>
				<div class="col-cell col-data"><?php echo $cell;?></div>
				<?php
				}
			}
			?>
			</div>
		<?php
		}
		?>
		</div>
	</div>
	<div>
	<?php
	// dump($_SESSION);
	?>
	</div>
	<script type="text/javascript" src="js/customer.js"></script>
</body>

</html>