<?php
const VALID = true;
include_once 'lib/config.php';
include_once 'lib/mysql.php';
include_once 'lib/tools.php';
session_start();
$identity = check_identity();
if ($identity == 'is_customer'){
?>
	<script type="text/javascript">
		setTimeout(() => {
			location.replace("customer.php");	
		}, 500);
	</script>
<?php
	exit;
}
if ($identity == 'neither'){
?>
	<script type="text/javascript">
		setTimeout(() => {
			location.replace("./");
		}, 500);
	</script>
<?php
	exit;
}
if ($identity == 'both'){
?>
	<script type="text/javascript">
		setTimeout(() => {
			location.replace("./who.php");		
		}, 500);
	</script>
<?php
	exit;
}
// 单身份：商户
// if ($_SERVER['REMOTE_ADDR'] == '10.0.0.1'){
// 	echo $_SESSION['side'];
// }
if (in_array($_SESSION['side'], $merch_side)){
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace('merch.php');
	}, 300);
</script>
<?php
	exit;
}
// 所以 $_SESSION['username'] 必定存在且唯一
$username = $_SESSION['username'];
$mysql = connect();
$sql_user = "SELECT * FROM `user` WHERE `username` = '$username'";
$result_user = execute($mysql, $sql_user);
$data_user = mysqli_fetch_assoc($result_user);
$pid = $data_user['pid'];
$nickname = $data_user['nickname'];
$side = $data_user['side'];
$reg_time = $data_user['reg_time'];
$reg_ip = $data_user['last_ip'];
$last_time = $data_user['last_time'];
$last_ip = $data_user['last_ip'];
$stat = $data_user['stat'];
$balance = $data_user['balance'];
$key = $data_user['key'];
$key_last = $data_user['key_last'];
$notify_method = $data_user['notify_method'];
$search_filter = $data_user['search_filter'];
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
	<link rel="stylesheet" type="text/css" href="css/button.css">
	<link rel="stylesheet" type="text/css" href="css/alert.css">
	<link rel="stylesheet" type="text/css" href="css/admin.css">
	<link rel="stylesheet" type="text/css" href="css/grid-order.css">
	<link rel="stylesheet" type="text/css" href="css/grid-filter.css">
	<link rel="stylesheet" type="text/css" href="css/tab-doc.css">
	<link rel="stylesheet" type="text/css" href="css/tab-qr.css">
	<link rel="stylesheet" type="text/css" href="css/upload.css">
	<link rel="stylesheet" type="text/css" href="css/tab-member.css">
	<link rel="stylesheet" type="text/css" href="css/tab-settle.css">
	<link rel="stylesheet" type="text/css" href="css/log.css">
	<link rel="icon" href="img/rmb.svg">
	<script type="text/javascript" src="js/admin.js" defer></script>
	<script type="text/javascript" src="js/qr.js" defer></script>
	<script type="text/javascript" src="js/upload.js" defer></script>
	<title>商户后台管理</title>
</head>
<body>
	<div class="header">
		<div class="logo" onclick="location.replace('<?php echo $front_host.$base_dir.'/';?>');return;">ManPay</div>
		<div class="avatar"></div>
		<div class="button red small btn-exit">退 出</div>
		<!-- <div class="button green small btn-service">进入客服聊天</div> -->
	</div>
	<div class="left">
		<div class="menu-cata">管理后台</div>
		<div class="menu-wrap">
			<div class="menu menu-business">
				<div class="menu-icon menu-icon-business">
					<svg class="menu-svg menu-svg-business" viewBox="0 0 1024 1024">
						<path d="M874.666667 469.333333H810.666667V298.666667c0-46.933333-38.4-85.333333-85.333334-85.333334h-170.666666V149.333333C554.666667 89.6 507.733333 42.666667 448 42.666667S341.333333 89.6 341.333333 149.333333V213.333333H170.666667c-46.933333 0-85.333333 38.4-85.333334 85.333334v170.666666h64C209.066667 469.333333 256 516.266667 256 576S209.066667 682.666667 149.333333 682.666667H85.333333v170.666666c0 46.933333 38.4 85.333333 85.333334 85.333334h170.666666v-64c0-59.733333 46.933333-106.666667 106.666667-106.666667s106.666667 46.933333 106.666667 106.666667V938.666667h170.666666c46.933333 0 85.333333-38.4 85.333334-85.333334v-170.666666h64c59.733333 0 106.666667-46.933333 106.666666-106.666667S934.4 469.333333 874.666667 469.333333z"></path>
					</svg>
				</div>
				业务
			</div>
			<label class="menu-item menu-item-order" id="menu-item-order">
				<div class="menu-item-icon menu-item-icon-order">
					<svg class="menu-item-svg menu-item-svg-order" viewBox="0 0 1024 1024" width="20" height="20">
						<path d="M342.73 692.02c-24.92 0-45.19 20.27-45.19 45.18s20.27 45.18 45.19 45.18c24.91 0 45.18-20.27 45.18-45.18s-20.26-45.18-45.18-45.18z" p-id="7234"></path>
						<path d="M815.34 173.34h-38.9v-55.67c0-30.75-24.92-55.67-55.67-55.67s-55.67 24.92-55.67 55.67v55.67H358.91v-55.67c0-30.75-24.92-55.67-55.67-55.67s-55.67 24.92-55.67 55.67v55.67h-38.9c-47.69 0-86.36 38.66-86.36 86.36v615.94c0 47.69 38.67 86.36 86.36 86.36h606.67c47.69 0 86.36-38.67 86.36-86.36V259.7c0-47.7-38.67-86.36-86.36-86.36zM342.73 819.88c-45.59 0-82.69-37.09-82.69-82.68s37.1-82.68 82.69-82.68c45.59 0 82.68 37.09 82.68 82.68s-37.08 82.68-82.68 82.68z m90.05-344.05l-90.6 90.6c-5.49 5.49-12.69 8.24-19.88 8.24s-14.37-2.74-19.86-8.21l-49.71-49.53c-11-10.97-11.03-28.77-0.07-39.78 10.96-11 28.77-11.03 39.77-0.07l29.83 29.72L393 436.06c10.99-10.99 28.8-10.99 39.78 0 10.98 10.98 10.98 28.79 0 39.77z m317.04 349.4H529.51c-15.54 0-28.13-12.59-28.13-28.13 0-15.53 12.59-28.12 28.13-28.12h220.31c15.54 0 28.13 12.59 28.13 28.12 0 15.54-12.6 28.13-28.13 28.13z m0-120.01H529.51c-15.54 0-28.13-12.59-28.13-28.12 0-15.54 12.59-28.13 28.13-28.13h220.31c15.54 0 28.13 12.59 28.13 28.13 0 15.53-12.6 28.12-28.13 28.12z m0-120.01H529.51c-15.54 0-28.13-12.59-28.13-28.12 0-15.54 12.59-28.13 28.13-28.13h220.31c15.54 0 28.13 12.59 28.13 28.13 0 15.53-12.6 28.12-28.13 28.12z m0-120.01H529.51c-15.54 0-28.13-12.59-28.13-28.12 0-15.53 12.59-28.13 28.13-28.13h220.31c15.54 0 28.13 12.6 28.13 28.13s-12.6 28.12-28.13 28.12z"></path>
					</svg>
				</div>
				订单 / 信息
			</label>
			<label class="menu-item menu-item-qr" id="menu-item-qr">
				<div class="menu-item-icon menu-item-icon-qr">
					<svg class="menu-item-svg menu-item-svg-qr" viewBox="0 0 1024 1024">
						<path d="M262.4 262.4l115.2 0 0 115.2-115.2 0 0-115.2Z"></path>
						<path d="M262.4 646.4l115.2 0 0 115.2-115.2 0 0-115.2Z"></path>
						<path d="M646.4 262.4l115.2 0 0 115.2-115.2 0 0-115.2Z"></path>
						<path d="M806.4 806.4l-115.2 0 0 57.6 115.2 0c32 0 57.6-25.6 57.6-57.6l0-57.6-57.6 0L806.4 806.4z"></path>
						<path d="M160 217.6l0 262.4 320 0 0-320L217.6 160C185.6 160 160 185.6 160 217.6zM422.4 422.4 217.6 422.4 217.6 217.6l204.8 0L422.4 422.4z"></path>
						<path d="M160 806.4c0 32 25.6 57.6 57.6 57.6l262.4 0 0-320-320 0L160 806.4zM217.6 601.6l204.8 0 0 204.8L217.6 806.4 217.6 601.6z"></path>
						<path d="M544 544l204.8 0 0 57.6-204.8 0 0-57.6Z"></path>
						<path d="M691.2 627.2 627.2 627.2 627.2 691.2 691.2 691.2 691.2 748.8 748.8 748.8 748.8 691.2 864 691.2 864 627.2 748.8 627.2Z"></path>
						<path d="M544 748.8l57.6 0 0 115.2-57.6 0 0-115.2Z"></path>
						<path d="M627.2 748.8l57.6 0 0 57.6-57.6 0 0-57.6Z"></path>
						<path d="M544 627.2l57.6 0 0 57.6-57.6 0 0-57.6Z"></path>
						<path d="M806.4 544l57.6 0 0 57.6-57.6 0 0-57.6Z"></path>
						<path d="M806.4 160 544 160l0 320 320 0L864 217.6C864 185.6 838.4 160 806.4 160zM806.4 422.4 601.6 422.4 601.6 217.6l204.8 0L806.4 422.4z"></path>
					</svg>
				</div>
				收款码 / 管理
			</label>
			<?php
			if ($side == 0){
			?>
			<label class="menu-item menu-item-upload" id="menu-item-upload">
				<div class="menu-item-icon menu-item-icon-upload">
					<svg class="menu-item-svg menu-item-svg-upload" viewBox="0 0 1024 1024">
						<path d="M512 904.06016226c-17.96502091 0-32.23136105-14.79472311-32.23136105-32.23136105V504.60263845L398.39766188 585.97361552c-6.34059562 5.81221265-14.26634013 9.51089342-22.72046762 9.51089342-8.45412749 0-16.90825497-3.1702978-22.72046763-9.51089342-12.68119123-12.68119123-12.68119123-33.28812698-0.52838297-45.96931821l136.85118871-136.85118872c6.34059562-6.34059562 14.26634013-9.51089342 22.72046763-9.51089342 8.45412749 0 16.90825497 3.1702978 22.72046763 9.51089342l136.85118871 136.85118872c6.34059562 6.34059562 9.51089342 14.26634013 9.51089342 22.72046762 0 17.96502091-14.79472311 32.23136105-32.23136105 32.23136105-8.45412749 0-16.90825497-3.1702978-22.72046762-9.51089343l-81.37097707-81.37097707v366.69777979c-0.52838296 18.49340388-14.79472311 33.28812698-32.75974402 33.28812699z"></path>
						<path d="M914.65491818 409.03306265c-33.59973746-35.22553119-81.28968739-61.23823116-126.81191232-70.45106241l-4.87738125-1.08386249-0.54193124-4.87738125c-15.71600623-106.7604561-112.72169984-220.02408718-260.12699963-220.02408718-76.41230614 0-146.86336854 24.38690622-198.34683722 68.28333741-46.06415619 39.56098119-74.7865124 91.58638113-82.37354989 151.74074977l-0.54193124 4.87738125-4.87738125 1.08386249c-42.8125687 9.21283124-182.08889974 52.02539992-182.08889973 218.39829345 0 128.43770608 112.17976859 218.39829345 273.1333496 218.39829343 11.38055624 0 21.67724997-6.50317499 26.55463122-15.71600622 2.16772499-3.25158749 3.25158749-7.58703749 3.25158749-14.09021248 0-21.13531873-9.21283124-37.93518744-29.80621871-37.93518745-98.63148736 0-205.39194346-47.14801869-205.39194345-150.11495604 0-59.61243741 23.30304372-101.88307485 70.99299364-129.52156855 37.93518744-21.67724997 77.49616863-25.47076871 78.58003114-25.47076873 19.50952497-1.62579376 35.22553119-18.42566247 35.2255312-37.93518744 0-105.67659361 89.96058737-182.63083099 214.6047747-182.63083099 116.51521858 0 194.55331847 95.37989987 194.55331847 184.25662474 0 21.13531873 17.34179997 37.93518744 37.93518745 37.93518744 28.72235621 0 75.8703749 16.25793747 110.55397484 52.56733118 17.34179997 18.42566247 37.93518744 48.77381243 36.30939371 89.96058737-3.79351875 96.46376236-79.66389364 156.61813103-197.26297473 156.61813103-4.87738125 0-9.21283124 0-14.63214373-0.54193125h-1.0838625c-17.34179997 0-26.01269997 12.46441873-28.18042496 28.72235622-1.0838625 2.70965625-1.62579376 5.41931249-1.62579375 8.12896872-0.54193126 16.79986873 12.46441873 30.34814996 28.72235621 30.89008122 5.41931249 0 11.38055624 0.54193126 16.79986873 0.54193124 177.21151851 0 260.66893089-111.63783735 265.54631212-222.19181218 3.25158749-51.48346868-16.25793747-99.71534985-54.19312492-139.8182623z"></path>
					</svg>
				</div>
				收款码 / 上传
			</label>
			<?php
			}
			?>
			<label class="menu-item menu-item-doc" id="menu-item-doc">
				<div class="menu-item-icon menu-item-icon-doc">
					<svg class="menu-item-svg menu-item-svg-doc" viewBox="0 0 1024 1024" width="20" height="20">
						<path d="M192 384h640a42.666667 42.666667 0 0 1 42.666667 42.666667v362.666666a42.666667 42.666667 0 0 1-42.666667 42.666667H192v106.666667a21.333333 21.333333 0 0 0 21.333333 21.333333h725.333334a21.333333 21.333333 0 0 0 21.333333-21.333333V308.821333L949.909333 298.666667h-126.528A98.048 98.048 0 0 1 725.333333 200.618667V72.661333L716.714667 64H213.333333a21.333333 21.333333 0 0 0-21.333333 21.333333v298.666667zM128 832H42.666667a42.666667 42.666667 0 0 1-42.666667-42.666667V426.666667a42.666667 42.666667 0 0 1 42.666667-42.666667h85.333333V85.333333a85.333333 85.333333 0 0 1 85.333333-85.333333h530.026667L1024 282.453333V938.666667a85.333333 85.333333 0 0 1-85.333333 85.333333H213.333333a85.333333 85.333333 0 0 1-85.333333-85.333333v-106.666667zM65.92 472.490667V746.666667h91.349333c40.597333 0 71.04-12.288 92.053334-36.864 19.946667-23.424 30.101333-56.832 30.101333-100.224 0-43.776-10.154667-77.184-30.101333-100.224-20.992-24.576-51.456-36.864-92.053334-36.864H65.92z m40.96 38.4H149.546667c31.146667 0 53.909333 7.68 68.266666 23.424 13.994667 15.36 20.992 40.704 20.992 75.264 0 33.792-6.997333 58.752-21.013333 74.88-14.336 15.744-37.098667 23.808-68.245333 23.808H106.88v-197.376z m322.005333-43.776c-38.506667 0-68.608 13.44-90.304 41.088-21.013333 26.112-31.146667 59.904-31.146666 101.76 0 41.472 10.133333 75.264 31.146666 101.376 21.696 26.88 51.797333 40.704 90.304 40.704 38.144 0 68.245333-13.44 90.304-40.32 20.992-25.728 31.488-59.52 31.488-101.76 0-42.24-10.496-76.416-31.488-102.144-22.058667-27.264-52.16-40.704-90.304-40.704z m0 39.552c25.898667 0 45.845333 8.832 59.84 27.264 13.653333 18.432 20.650667 43.776 20.650667 76.032s-6.997333 57.216-20.650667 75.264c-13.994667 18.048-33.941333 27.264-59.84 27.264-25.898667 0-46.208-9.6-60.202666-28.416-13.653333-18.432-20.309333-43.008-20.309334-74.112 0-31.488 6.656-56.064 20.309334-74.496 14.336-19.2 34.304-28.8 60.202666-28.8z m268.8-39.552c-39.893333 0-70.357333 14.208-91.349333 43.392-18.56 25.344-27.648 58.752-27.648 99.456 0 41.472 8.746667 74.496 26.602667 99.072 20.288 28.416 51.434667 43.008 93.098666 43.008 26.944 0 50.048-8.448 69.290667-25.344 20.650667-18.048 33.6-43.008 39.210667-75.264h-39.893334c-4.906667 20.736-13.312 36.48-25.216 46.848-11.2 9.216-25.898667 14.208-43.733333 14.208-27.306667 0-47.616-9.6-60.565333-28.032-11.904-17.28-17.856-42.24-17.856-74.496 0-31.488 5.973333-56.064 18.218666-73.728 13.290667-19.968 32.896-29.568 59.136-29.568 17.493333 0 31.850667 3.84 43.050667 12.288 11.562667 8.448 19.264 21.504 23.466667 39.552h39.893333c-3.84-27.648-14.72-49.92-32.896-66.048-18.901333-16.896-43.413333-25.344-72.810667-25.344z"></path>
					</svg>
				</div>
				文档 / 说明
			</label>
		</div>
		<div class="menu-cata"></div>
		<div class="menu-wrap">
			<div class="menu menu-hr">
				<div class="menu-icon menu-icon-hr">
					<svg class="menu-svg menu-svg-hr" viewBox="0 0 1024 1024">
						<path d="M384.75581 648.54146c-4.437333-3.445841-39.635302-30.069841-137.289143-36.547047-5.371937-0.446984-10.784508-4.25854-15.555048-7.452445-6.070857-4.039111-12.377397-8.281397-16.97727-13.750857-7.476825-8.923429-15.920762-16.432762-23.015619-26.339555-47.477841-66.186159-94.004825-79.473778-100.961524-82.098794-9.264762-4.469841-44.755302 1.032127-27.704889 24.697905 1.080889 0.178794 54.288254 74.922667 57.742223 80.896 6.111492 10.548825 13.027556 14.864254 19.521016 22.267936 5.932698 6.761651 12.499302 12.978794 18.562031 19.626667 0 0-34.027683-20.146794-42.179047-28.972699-3.632762-3.957841-5.380063-9.622349-8.27327-14.319746a351.963429 351.963429 0 0 0-10.540699-15.904508 543.857778 543.857778 0 0 0-13.954031-19.301587c-6.160254-8.021333-12.55619-15.872-18.903365-23.755174-3.949714-4.908698-8.411429-9.64673-11.67035-14.774858-16.091429-25.421206 2.283683-40.626794 19.772953-42.544761-0.845206-2.925714-8.890921-79.798857-10.296889-92.704508-4.01473-37.059048-7.980698-64.512-29.224635-71.980699-5.477587-1.934222-9.346032-1.284063-13.425778 3.023238-4.827429 5.071238-7.704381 31.98781-9.094095 49.005715 0 0-9.289143 91.314794-7.054222 133.924571 1.194667 22.853079 72.183873 180.06146 92.582603 204.353016 20.382476 24.283429 179.923302 116.248381 179.923301 116.248381 5.331302 4.437333 34.872889 27.184762 34.872889 57.010793v64.747683l171.999492 0.203175 0.146286-59.416381c0.398222-10.979556 2.267429-21.902222 3.584-32.841143 3.74654-12.491175 0.747683-36.100063-0.999619-45.267302-19.976127-89.153016-93.549714-141.815873-101.587301-148.033016zM1012.703492 357.603556c-1.397841-17.026032-4.266667-43.934476-9.094095-49.005715-4.079746-4.307302-7.956317-4.95746-13.433905-3.023238-21.227683 7.476825-25.209905 34.929778-29.216508 71.980699-1.414095 12.905651-9.451683 89.778794-10.305016 92.704508 17.48927 1.917968 35.872508 17.123556 19.772953 42.544761-3.250794 5.136254-7.704381 9.866159-11.662223 14.774858-6.347175 7.883175-12.751238 15.733841-18.911492 23.755174a533.536508 533.536508 0 0 0-13.945904 19.301587 345.185524 345.185524 0 0 0-10.540699 15.904508c-2.885079 4.697397-4.632381 10.353778-8.281397 14.319746-8.159492 8.834032-42.179048 28.972698-42.179047 28.972699 6.06273-6.647873 12.63746-12.865016 18.570158-19.626667 6.49346-7.403683 13.401397-11.719111 19.512889-22.267936 3.470222-5.973333 56.677587-80.717206 57.75035-80.896 17.050413-23.665778-18.448254-29.167746-27.713016-24.697905-6.948571 2.616889-53.475556 15.912635-100.953397 82.098794-7.094857 9.906794-15.546921 17.424254-23.015619 26.339555-4.599873 5.477587-10.91454 9.711746-16.985397 13.750857-4.77054 3.193905-10.183111 7.00546-15.555048 7.452445-97.653841 6.477206-132.843683 33.101206-137.289142 36.547047-8.037587 6.22527-81.603048 58.88-101.587302 148.024889-1.755429 9.167238-4.746159 32.776127-0.999619 45.267302 1.316571 10.938921 3.193905 21.861587 3.584 32.841143l0.146286 59.416381 171.999492-0.203175v-64.747683c0-29.826032 29.541587-52.57346 34.872889-57.010793 0 0 159.532698-91.956825 179.923301-116.248381s91.371683-181.499937 92.574476-204.353016c2.251175-42.601651-7.037968-133.916444-7.037968-133.916444zM615.659683 560.72127c81.594921-76.751238 182.889651-172.056381 196.803047-256.869587 9.768635-59.977143 0.219429-109.413587-28.379428-146.944-28.867048-37.888-72.78527-59.513905-134.282159-66.121143a165.172825 165.172825 0 0 0-16.684699-0.869588c-43.495619 0-87.413841 16.205206-121.116444 43.568762-33.694476-27.363556-77.620825-43.568762-121.108317-43.568762-5.36381 0-10.752 0.276317-16.684699 0.869588-61.496889 6.607238-105.423238 28.233143-134.282159 66.121143-28.606984 37.530413-38.148063 86.966857-28.379428 146.944 13.913397 84.821333 115.208127 180.118349 196.803047 256.869587 0 0 40.699937 40.846222 52.931048 51.330032 9.191619 7.891302 29.135238 23.79581 50.728635 23.795809 21.601524 0 41.545143-15.904508 50.736762-23.795809 12.214857-10.48381 52.914794-51.330032 52.914794-51.330032z m-207.262477-369.615238c-51.98019 0-94.281143 37.61981-94.281143 83.862349a25.396825 25.396825 0 1 1-50.809904 0c0-74.264381 65.080889-134.672254 145.091047-134.672254a25.396825 25.396825 0 1 1 0 50.809905z" ></path>
					</svg>
				</div>
				会员
			</div>
			<label class="menu-item menu-item-member" id="menu-item-member">
				<div class="menu-item-icon menu-item-icon-member">
					<svg class="menu-item-svg menu-item-svg-member" viewBox="0 0 1024 1024" >
						<path d="M664.32 896c29.376-31.04 39.68-75.52 39.68-138.24 0-99.456-34.944-184.448-107.712-240.128C619.136 513.92 644.288 512 672 512c241.728 0 288 147.648 288 245.76S956.352 896 672 896h-7.68zM550.4 392C566.784 360.96 576 325.568 576 288s-9.216-72.96-25.6-104a160 160 0 1 1 0 208zM352 448a160 160 0 1 1 0-320 160 160 0 0 1 0 320z m0 448C67.648 896 64 853.184 64 757.76S110.272 512 352 512s288 147.648 288 245.76S636.352 896 352 896z"></path>
					</svg>
				</div>
				商户 / 信息
			</label>
			<label class="menu-item menu-item-settle" id="menu-item-settle">
				<div class="menu-item-icon menu-item-icon-settle">
					<svg class="menu-item-svg menu-item-svg-settle" viewBox="0 0 1024 1024" width="20" height="20">
					<path d="M928.5 597.2c-0.7-3.4-1.6-6.5-2.8-9-4-8.6-9.7-16.9-17-24.8-7.3-7.9-14-13.4-20-16.6-2.9-1.4-6.2-2.7-10.1-3.9-3.9-1.2-7.9-1.8-12.1-1.9-4.2-0.1-8.4 0.4-12.7 1.7-4.3 1.3-8.5 3.5-12.5 6.7-3.2 2.6-7.1 6.1-11.8 10.6-4.7 4.4-8.8 8.3-12.3 11.4l80.5 80.5c2-1.7 4.2-3.7 6.5-6 2-2 4.4-4.3 7.1-6.9 2.7-2.6 5.8-5.6 9.3-9 3.2-3.4 5.5-7 6.9-10.8 1.4-3.7 2.1-7.5 2.1-11.2 0-3.7-0.4-7.3-1.1-10.8zM625.9 762.2c-9.5 9.5-17.4 17.4-23.7 23.9-6.3 6.5-10.3 10.6-12.1 12.3-4 4-7.1 7.7-9.3 11-2.1 3.3-4.1 7-5.8 11-1.4 2.6-3.3 7.3-5.6 14.2-2.3 6.9-4.7 14.4-7.1 22.6-2.4 7.9-4.7 15.8-6.9 23.7-2.2 7.6-3.5 13.4-4.1 17.4-1.1 7.5-0.4 12.8 2.2 15.9 2.6 3.2 7.7 4.3 15.5 3.4 3.7-0.6 9.4-1.9 17-3.9s15.6-4.4 24.1-7.1c8.5-2.7 16.6-5.5 24.3-8.4 7.7-2.9 13.6-5.3 17.7-7.3 4-1.7 7.9-4 11.6-6.9 3.7-2.9 7-5.6 9.9-8.2 1.2-0.9 4.7-4.3 10.8-10.3 6-6 13.6-13.7 22.8-23 9.2-9.3 19.5-19.6 31-30.8l34.5-34.9 92.6-92.6-80.5-81-158.9 159zM635.9 630.2c16.1 0 29.1-13 29.1-29.1S652 572 635.9 572H500.3v-68.3h135.6c16.1 0 29.1-13 29.1-29.1s-13-29.1-29.1-29.1H514.8L640.9 327c11.7-11 12.3-29.4 1.3-41.2-11-11.7-29.5-12.3-41.2-1.3L471.2 406.4 341.4 284.5c-11.7-11-30.2-10.4-41.2 1.3-11 11.7-10.4 30.2 1.3 41.2l126.1 118.4H306.5c-16.1 0-29.1 13-29.1 29.1s13 29.1 29.1 29.1h135.6V572H306.5c-16.1 0-29.1 13-29.1 29.1s13 29.1 29.1 29.1h135.6v69.3c0 16.1 13 29.1 29.1 29.1s29.1-13 29.1-29.1v-69.3h135.6z" p-id="5358"></path><path d="M390.7 859.7H206.8c-27.1 0-49.2-19.2-49.2-42.8V217.1c0-23.6 22.1-42.8 49.2-42.8h513.4c27.1 0 49.2 19.2 49.2 42.8v220.3c0 16.1 13 29.1 29.1 29.1s29.1-13 29.1-29.1V217.1c0-55.7-48.2-101.1-107.4-101.1H206.8c-59.2 0-107.4 45.3-107.4 101.1V817c0 55.7 48.2 101.1 107.4 101.1h183.9c16.1 0 29.1-13 29.1-29.1s-13-29.3-29.1-29.3z" p-id="5359"></path>
					</svg>
				</div>
				余额 / 结算
			</label>
			<label class="menu-item menu-item-me" id="menu-item-me">
				<div class="menu-item-icon menu-item-icon-me">
					<svg class="menu-item-svg menu-item-svg-me" viewBox="0 0 1024 1024">
						<path d="M512 483.57376A241.78688 241.78688 0 1 1 512 0a241.78688 241.78688 0 0 1 0 483.57376zM0 967.0656v-28.42624c0-219.9552 178.29888-398.25408 398.21312-398.25408h227.57376c219.9552 0 398.21312 178.29888 398.21312 398.25408v28.42624c0 31.41632-25.47712 56.89344-56.89344 56.89344H56.9344A56.89344 56.89344 0 0 1 0 967.10656z"></path>
					</svg>
				</div>
				账号 / 设置
			</label>
		</div>
		<div class="menu-cata"></div>
		<div class="menu-wrap">
			<div class="menu menu-system">
				<div class="menu-icon menu-icon-system">
					<svg class="menu-svg menu-svg-system" viewBox="0 0 1024 1024" width="32" height="32">
						<path d="M510 425.1c-49.6 0-89.7 39.6-89.7 88.3 0 48.8 40.1 88.3 89.7 88.3 49.5 0 89.7-39.5 89.7-88.3 0-48.7-40.1-88.3-89.7-88.3z m313.8 423.3l25.3-24.9c28-27.6 28-72.3 0-99.9l-43.8-43.1c14.1-24.2 25.2-50.1 32.9-77.6h47.3c39.6 0 71.8-31.6 71.8-70.6V497c0-39-32.2-70.7-71.8-70.7h-46.8c-7.4-27.1-18-53-31.8-77l42.1-41.4c28-27.7 28-72.4 0-99.9l-25.3-25c-28-27.6-73.5-27.6-101.5 0L681 223.4c-25.2-14.5-52.5-25.7-81.3-33.5v-47.5c0-39-32.1-70.6-71.7-70.6h-35.9c-39.6 0-71.7 31.6-71.7 70.6V190c-28.8 7.8-56.1 19-81.3 33.5l-41.3-40.6c-28-27.6-73.5-27.6-101.5 0l-25.4 25c-28 27.5-28 72.3 0 99.9l42.1 41.4c-13.7 24-24.4 49.9-31.7 77h-46.8c-39.7 0-71.8 31.7-71.8 70.7v35.4c0 39 32.1 70.6 71.7 70.6h47.3c7.7 27.5 18.8 53.4 32.9 77.6l-43.8 43.1c-28 27.6-28 72.3 0 99.9l25.4 24.9c28 27.6 73.5 27.6 101.5 0l44-43.4c24.5 13.7 50.8 24.4 78.6 31.8v45.3c0 39 32.1 70.6 71.7 70.6h35.9c39.6 0 71.7-31.6 71.7-70.6v-45.3c27.8-7.4 54.1-18.1 78.5-31.8l44.1 43.4c28.1 27.6 73.6 27.6 101.6 0zM510 709.9c-109 0-197.3-87-197.3-194.2 0-107.4 88.4-194.3 197.3-194.3 109 0 197.3 86.9 197.3 194.3 0.1 107.2-88.3 194.2-197.3 194.2z" fill="rgba(255,200,20,1)"></path>
					</svg>
				</div>
				系统
			</div>
			<label class="menu-item menu-item-log" id="menu-item-log">
				<div class="menu-item-icon menu-item-icon-log`">
					<svg class="menu-item-svg menu-item-svg-log" viewBox="0 0 1024 1024" width="32" height="32">
						<path d="M411.8 154.5h35.4v51.2h-35.4zM302.8 154.5h35.4v51.2h-35.4zM520.8 154.5h35.4v51.2h-35.4zM266.1 262.2c14 0 25.4-11.4 25.4-25.4V126.5c0-14-11.4-25.4-25.4-25.4-14 0-25.4 11.4-25.4 25.4v110.3c0 13.9 11.4 25.4 25.4 25.4zM375 262.2c14 0 25.4-11.4 25.4-25.4V126.5c0-14-11.4-25.4-25.4-25.4-14 0-25.4 11.4-25.4 25.4v110.3c0 13.9 11.5 25.4 25.4 25.4zM484 262.2c14 0 25.4-11.4 25.4-25.4V126.5c0-14-11.4-25.4-25.4-25.4s-25.4 11.4-25.4 25.4v110.3c0 13.9 11.5 25.4 25.4 25.4zM732.6 233.2c0-43.4-35.3-78.8-78.8-78.8h-24v51.2h24c15.2 0 27.5 12.4 27.5 27.5v137.5l51.2-64.6v-72.8z"></path>
						<path d="M681.3 825.4c0 15.2-12.3 27.5-27.5 27.5H205.3c-15.2 0-27.5-12.3-27.5-27.5V233.2c0-15.2 12.3-27.5 27.5-27.5h24v-51.2h-24c-43.4 0-78.8 35.3-78.8 78.8v592.2c0 43.4 35.3 78.8 78.8 78.8h448.5c43.4 0 78.8-35.3 78.8-78.8V650l-51.2 64.6v110.8zM593 262.2c14 0 25.4-11.4 25.4-25.4V126.5c0-14-11.4-25.4-25.4-25.4-14 0-25.4 11.4-25.4 25.4v110.3c0 13.9 11.4 25.4 25.4 25.4z"></path>
						<path d="M261.7 406.1l335.6-0.2c15.1 0 27.4-12.3 27.4-27.4 0-15.1-12.3-27.4-27.4-27.4l-335.6 0.2c-15.1 0-27.4 12.3-27.4 27.4 0 15 12.4 27.4 27.4 27.4zM261.7 483c-15.1 0-27.4 12.3-27.4 27.4 0 15.1 12.3 27.4 27.4 27.4l287.3-0.2 43.5-54.8-330.8 0.2zM951.3 286.7L869 221.5c-11.2-8.9-27.5-7-36.3 4.2l-45.2 57 122.8 97.4 45.2-57c8.8-11.2 7-27.5-4.2-36.4zM261.7 614.7c-15.1 0-27.4 12.3-27.4 27.4 0 15.1 12.3 27.4 27.4 27.4l206.1-0.1 3-15.2 4.6-23.8 3.1-15.8-216.8 0.1z"></path>
						<path d="M767.4 308.1L752 327.5 732.6 352l-51.2 64.6-61.7 77.8-34.3 43.2-61 77-4.6 5.8-2.3 2.9-15.4 19.4-5.2 26.8-0.9 4.7L472.6 794c-1.4 7.1 5.1 12.3 11.8 9.3l111.3-50.1 29.2-13.2h0.1l17.7-22.3 38.7-48.8 51.2-64.6 157.6-198.8-122.8-97.4z m-189 431.3l-47.7 21.5-27.5-21.8 10-51.4 1.3-6.7 70.1 55.6-6.2 2.8z m154.2-311.3l-51.2 64.6-96.6 121.8-15.8 19.9c-4.9 6.1-13.3 7.6-18.8 3.2-5.5-4.4-6-12.9-1.2-19.1l3.2-4 129.1-162.9 51.2-64.6 22.1-27.8c4.9-6.1 13.3-7.6 18.8-3.2 5.5 4.4 6 12.9 1.2 19.1l-42 53z"></path>
					</svg>
				</div>
				制作 / 日志
			</label>
		</div>
	</div>
	<div class="right">
		<div class="tab-wrap tab-order">
			<div class="tab-title">业务 <span class="tab-title-sub">订单 / 信息</span></div>
			<div class="tab-title-sub">我的信息</div>
			<div class="api-info-grid">
				<div class="api-info-line">
					<div class="api-cell api-info-name">我的商户 <b>pid</b></div>
					<div class="api-cell api-info-value">
						<div class="api-info-value-text"><?php echo $pid = $data_user['pid'];?></div>
					</div>
				</div>
				<div class="api-info-line">
					<div class="api-cell api-info-name">您的商户秘钥</div>
					<div class="api-cell api-info-value">
						<span class="api-info-key"><?php echo $key;?></span><div class="btn-api btn-reset">重置 key</div>
					</div>
				</div>
				<div class="api-info-line">
					<div class="api-cell api-info-name">通知回调方式</div>
					<div class="api-cell api-info-value">
						<label class="method-wrap get-wrap">
							<input class="radio-method" id="radio-get" type="radio" name="callback-method" value="0"<?php if(!$notify_method)echo ' checked';?>>
							<label class="label-method" for="radio-get">GET</label>
						</label>
						<label class="method-wrap post-wrap">
							<input class="radio-method" id="radio-post" type="radio" name="callback-method" value="1"<?php if($notify_method)echo ' checked';?>>
							<label class="label-method" for="radio-post">POST</label>
						</label>
						<div class="btn-api btn-confirm">确认</div>
					</div>
				</div>
				<div class="api-info-line">
					<div class="api-cell api-info-name">API接口文档</div>
					<div class="api-cell api-info-value">
						<div class="btn-api btn-view-doc">浏览文档</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="tab-title">订单</div>
			<div class="tab-title-sub">筛选</div>
			<div class="filter-grid">
				<div class="filter-grid-row">
					<div class="filter-grid-cell filter-grid-cell-name">订单状态筛选</div>
					<div class="filter-grid-cell filter-grid-cell-value">
					<?php
					for ($i = 0; $i <= 2; $i++){
					?>
						<label class="order-status-wrap" for="radio-filter-status-<?php echo $i;?>">
							<input class="radio-filter-status" id="radio-filter-status-<?php echo $i;?>" type="radio" name="radio-filter-status" value="<?php echo $i;?>"<?php if($search_filter==$i)echo ' checked';?>>
							<label class="order-status" for="radio-filter-status-<?php echo $i;?>"><?php switch($i){case 0:echo'仅<span class="text-red">未支付</span>且<span class="text-red">未通知</span>订单';break;case 1:echo'包含<span class="text-green">已支付</span>但<span class="text-red">未通知</span>订单';break;case 2:echo'包含<span class="text-green">已支付</span>且<span class="text-green">已通知</span>的<span class="text-green">全部</span>订单';break;}?></label>
						</label>
					<?php
					}
					?>
					</div>
					<div class="filter-grid-cell filter-grid-cell-action"><div class="btn-filter btn-filter-confirm disabled">确 定</div></div>
				</div>
				<div class="filter-grid-row">
					<div class="filter-grid-cell filter-grid-cell-name"><span class="text-red">订单号</span>搜索</div>
					<div class="filter-grid-cell filter-grid-cell-value"><input class="search-text" type="text"></div>
					<div class="filter-grid-cell filter-grid-cell-action"><div class="btn-filter btn-filter-search">搜 索</div></div>
				</div>
			</div>
			<?php
			$str_status_filter = '';
			switch ($search_filter){
			case 0:
				$str_status_filter = " WHERE `pay_status` = 0 AND `notify_status` = 0";
				break;
			case 1:
				$str_status_filter = " WHERE `notify_status` = 0";
				break;
			case 2:
				$str_status_filter = "";
				break;
			}
			$sql_num_all =
			"SELECT
				COUNT(*)
			FROM
				`order`$str_status_filter;";
			$result_num_all = execute($mysql, $sql_num_all);
			$num_all = $result_num_all->fetch_row()[0];
			$num_pages = ceil($num_all / $orders_per_page);
			$str_order_cols = '';
			foreach ($order_cols as $col){
				$str_order_cols .= "`$col`,";
			}
			$str_order_cols = substr($str_order_cols, 0, -1);
			$sql_order =
			"SELECT
				$str_order_cols
			FROM
				`order`
			$str_status_filter
			ORDER BY
				`oid` DESC
			LIMIT 0, $orders_per_page;";
			$result_order = execute($mysql, $sql_order);
			$num_orders = $result_order->num_rows;
			$orders = mysqli_fetch_all($result_order, MYSQLI_ASSOC);
			$orders = matrix_transposition($orders);
			// var_dump($orders);
			?>
			<div class="order-grid">
			<?php
			// 订单 / 信息 表格
			if (!empty($orders)) // 还要修改
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
			if ($num_pages > 1){
			?>
			<div class="page-grid">
				<?php
				for ($i = 0; $i < $num_pages; $i++){
				?>
				<div class="button blue btn-order-page<?php if($i == 0)echo ' disabled';echo" order-page-$i";?>">第 <?php echo $i + 1;?> 页</div>
				<?php
				}
				?>
			</div>
			<?php
			}
			else{
			?>
				<div class="button blue btn-order-page disabled order-page-0">第 1 页（当前）</div>
			<?php
			}
			?>
		</div>
		<div class="tab-wrap tab-qr">
			<div class="tab-title">业务 <span class="tab-title-sub">收款码 / 管理</span></div>
			<br>
			<?php
			/*
			Dir 中数据形态:
			$qrs_dir = [
				[
					'filename' => 'wx01.png',
					'method' => 'wxpay',
				]
				[
					'filename' => 'wx02.png',
					'method' => 'wxpay',
				]
			];
			Redis 中数据形态:
			$qrs_redis = [
				[
					"sequence" => 0,
					"filename" => "wx01.png",
					"method" => "wxpay",
					"text" => "",
					"comment" => "",
					"range" => ""
				],
				[
					"sequence" => 0,
					"filename" => "wx02.png",
					"method" => "wxpay",
					"text" => "",
					"comment" => "",
					"range" => ""
				],
			]
			*/
			// $dir_ali = scandir('img/qr-files/alipay');
			// var_dump($dir_ali);
			$redis = redis_connect();
			$redis->select(REDIS_DBNM_QPAY);
			$qrs_redis = qr_sync($redis); // 读取目录和redis，并整理
			// var_dump($qrs_redis);
			// 根据 range 分组，简化循环代码
			$qrs_range_0 = [];
			$qrs_range_1 = [];
			$qrs_range_2 = [];
			$qrs_range_3 = [];
			foreach ($qrs_redis as $qr_redis_str){
				$qr_redis = json_decode($qr_redis_str, true);
				switch ($qr_redis['range']){
				case 0:
					$qrs_range_0[] = $qr_redis;
					break;
				case 1:
					$qrs_range_1[] = $qr_redis;
					break;
				case 2:
					$qrs_range_2[] = $qr_redis;
					break;	
				case 3:
					$qrs_range_3[] = $qr_redis;
					break;
				}
			}
			$ranges = zres_flip($redis->zRange(REDIS_ZKEY_AMOUNT_RANGE, 0, -1, ['WITHSCORES' => 1]));
			foreach ($ranges as $key => $range_str){
				$ranges[$key] = json_decode($range_str, true);
			}
			?>
			<div class="qr-area enabled"><?php // Area 1?>
				<div class="qr-heading">
					<div class="qr-heading-status">已启用</div>
					<div class="qr-heading-num-wrap">
						<div class="qr-heading-num-text">金额范围</div>
						<input type="text" value="<?php echo $ranges[1]['min'];?>" class="qr-heading-num qr-heading-num-min" maxlength="5">
						<div class="qr-heading-tilde">~</div>
						<input type="text" value="<?php echo $ranges[1]['max'];?>" class="qr-heading-num qr-heading-num-max" maxlength="5">
						<div class="btn-range-ok">确 定</div>
					</div>
				</div>
				<div class="qr-container">
				<?php
				if (!empty($qrs_range_1)){
					foreach ($qrs_range_1 as $qr_info){
				?>
					<div class="qr-position"><?php // qr图的位置，用于为qr图提供停靠?>
						<div class="qr-position-order"><?php echo $qr_info['sequence'];?></div>
						<div class="qr-item-docker">
							<div class="qr-item"><?php // 可拖动元素?>
								<div class="qr-filename"><?php echo $qr_info['filename'];?></div>
								<div class="qr-img-wrap">
									<div class="qr-img-common qr-method"><?php echo img_method($qr_info['method']);?></div>
									<?php
									$method = $qr_info['method'];
									$filename = $qr_info['filename'];
									$dest_file = "img/qr-files/$method/$filename";
									$img_exif_type = exif_imagetype($dest_file);
									switch ($img_exif_type){
									case IMAGETYPE_JPEG:
										$img_type = 'image/jpeg';
										break;
									case IMAGETYPE_PNG:
										$img_type = 'image/png';
										break;
									case IMAGETYPE_BMP:
										$img_type = 'image/bmp';
										break;
									case IMAGETYPE_GIF:
										$img_type = 'image/gif';
										break;
									}
									$img_content = file_get_contents($dest_file);
									$b64 = base64_encode($img_content);
									?>
									<div class="qr-img-common qr-img" style="background-image:url(<?php echo "data:$img_type;base64,$b64";?>);"></div><?php // 收款码图片本体?>
								</div>
								<div class="qr-info-edit">
									<div class="qr-text-wrap">
										<div class="qr-text-head-wrap">
											<div class="qr-text-label">展示文字</div>
											<div class="btn-qr-text btn-text-out">确定</div>
										</div>
										<input class="input-text input-text-out" type="text" placeholder="对外展示文字" value="<?php echo $qr_info['text'];?>">
									</div>
									<div class="qr-text-wrap">
										<div class="qr-text-head-wrap">
											<div class="qr-text-label">备注文字</div>
											<div class="btn-qr-text btn-text-in">确定</div>
										</div>
										<input class="input-text input-text-in" type="text" placeholder="内部备注文字" value="<?php echo $qr_info['comment'];?>">
									</div>
								</div>
								<div class="btn-toggle-edit">展 开 ↓</div>
								<div class="qr-delete"><span class="btn-delete">✖</span></div>
							</div>
						</div>
					</div>
				<?php
					}
				}
				?>
					<div class="qr-position">
						<div class="qr-position-order"><?php echo isset($qr_info['sequence']) ? $qr_info['sequence'] + 1 : 1;?></div>
						<div class="qr-item-docker docker-add">
							
						</div>
					</div>
				</div>
			</div>
			<div class="qr-area enabled"><?php // Area 2 ?>
				<div class="qr-heading">
					<div class="qr-heading-status">已启用</div>
					<div class="qr-heading-num-wrap">
						<div class="qr-heading-num-text">金额范围</div>
						<input type="text" value="<?php echo $ranges[2]['min'];?>" class="qr-heading-num qr-heading-num-min" maxlength="5">
						<div class="qr-heading-tilde">~</div>
						<input type="text" value="<?php echo $ranges[2]['max'];?>" class="qr-heading-num qr-heading-num-max" maxlength="5">
						<div class="btn-range-ok">确 定</div>
					</div>
				</div>
				<div class="qr-container">
				<?php
				if (!empty($qrs_range_2)){
					foreach ($qrs_range_2 as $qr_info){
				?>
					<div class="qr-position"><?php // qr图的位置，用于为qr图提供停靠?>
						<div class="qr-position-order"><?php echo $qr_info['sequence'];?></div>
						<div class="qr-item-docker">
							<div class="qr-item"><?php // 可拖动元素?>
								<div class="qr-filename"><?php echo $qr_info['filename'];?></div>
								<div class="qr-img-wrap">
									<div class="qr-img-common qr-method"><?php echo img_method($qr_info['method']);?></div>
									<?php
									$method = $qr_info['method'];
									$filename = $qr_info['filename'];
									$dest_file = "img/qr-files/$method/$filename";
									$img_exif_type = exif_imagetype($dest_file);
									switch ($img_exif_type){
									case IMAGETYPE_JPEG:
										$img_type = 'image/jpeg';
										break;
									case IMAGETYPE_PNG:
										$img_type = 'image/png';
										break;
									case IMAGETYPE_BMP:
										$img_type = 'image/bmp';
										break;
									case IMAGETYPE_GIF:
										$img_type = 'image/gif';
										break;
									}
									$img_content = file_get_contents($dest_file);
									$b64 = base64_encode($img_content);
									?>
									<div class="qr-img-common qr-img" style="background-image:url(<?php echo "data:$img_type;base64,$b64";?>);"></div><?php // 收款码图片本体?>
								</div>
								<div class="qr-info-edit">
									<div class="qr-text-wrap">
										<div class="qr-text-head-wrap">
											<div class="qr-text-label">展示文字</div>
											<div class="btn-qr-text btn-text-out">确定</div>
										</div>
										<input class="input-text input-text-out" type="text" placeholder="对外展示文字" value="<?php echo $qr_info['text'];?>">
									</div>
									<div class="qr-text-wrap">
										<div class="qr-text-head-wrap">
											<div class="qr-text-label">备注文字</div>
											<div class="btn-qr-text btn-text-in">确定</div>
										</div>
										<input class="input-text input-text-in" type="text" placeholder="内部备注文字" value="<?php echo $qr_info['comment'];?>">
									</div>
								</div>
								<div class="btn-toggle-edit">展 开 ↓</div>
								<div class="qr-delete"><span class="btn-delete">✖</span></div>
							</div>
						</div>
					</div>
				<?php
					}
				}
				?>
					<div class="qr-position">
						<div class="qr-position-order"><?php echo isset($qr_info['sequence']) ? $qr_info['sequence'] + 1 : 1;?></div>
						<div class="qr-item-docker docker-add">
							
						</div>
					</div>
				</div>
			</div>
			<div class="qr-area enabled"><?php // Area 3 ?>
				<div class="qr-heading">
					<div class="qr-heading-status">已启用</div>
					<div class="qr-heading-num-wrap">
						<div class="qr-heading-num-text">金额范围</div>
						<input type="text" value="<?php echo $ranges[3]['min'];?>" class="qr-heading-num qr-heading-num-min" maxlength="5">
						<div class="qr-heading-tilde">~</div>
						<input type="text" value="<?php echo $ranges[3]['max'];?>" class="qr-heading-num qr-heading-num-max" maxlength="5">
						<div class="btn-range-ok">确 定</div>
					</div>
				</div>
				<div class="qr-container"><?php // Section 3 Start ?>
				<?php
				if (!empty($qrs_range_3)){
					foreach ($qrs_range_3 as $qr_info){
				?>
					<div class="qr-position"><?php // qr图的位置，用于为qr图提供停靠?>
						<div class="qr-position-order"><?php echo $qr_info['sequence'];?></div>
						<div class="qr-item-docker">
							<div class="qr-item"><?php // 可拖动元素?>
								
								<div class="qr-filename"><?php echo $qr_info['filename'];?></div>
								<div class="qr-img-wrap">
									<div class="qr-img-common qr-method"><?php echo img_method($qr_info['method']);?></div>
									<?php
									$method = $qr_info['method'];
									$filename = $qr_info['filename'];
									$dest_file = "img/qr-files/$method/$filename";
									$img_exif_type = exif_imagetype($dest_file);
									switch ($img_exif_type){
									case IMAGETYPE_JPEG:
										$img_type = 'image/jpeg';
										break;
									case IMAGETYPE_PNG:
										$img_type = 'image/png';
										break;
									case IMAGETYPE_BMP:
										$img_type = 'image/bmp';
										break;
									case IMAGETYPE_GIF:
										$img_type = 'image/gif';
										break;
									}
									$img_content = file_get_contents($dest_file);
									$b64 = base64_encode($img_content);
									?>
									<div class="qr-img-common qr-img" style="background-image:url(<?php echo "data:$img_type;base64,$b64";?>);"></div><?php // 收款码图片本体?>
								</div>
								<div class="qr-info-edit">
									<div class="qr-text-wrap">
										<div class="qr-text-head-wrap">
											<div class="qr-text-label">展示文字</div>
											<div class="btn-qr-text btn-text-out">确定</div>
										</div>
										<input class="input-text input-text-out" type="text" placeholder="对外展示文字" value="<?php echo $qr_info['text'];?>">
									</div>
									<div class="qr-text-wrap">
										<div class="qr-text-head-wrap">
											<div class="qr-text-label">备注文字</div>
											<div class="btn-qr-text btn-text-in">确定</div>
										</div>
										<input class="input-text input-text-in" type="text" placeholder="内部备注文字" value="<?php echo $qr_info['comment'];?>">
									</div>
								</div>
								<div class="btn-toggle-edit">展 开 ↓</div>
								<div class="qr-delete"><span class="btn-delete">✖</span></div>
							</div>
						</div>
					</div>
				<?php
					}
				}
				?>
					<div class="qr-position">
						<div class="qr-position-order"><?php echo isset($qr_info['sequence']) ? $qr_info['sequence'] + 1 : 1;?></div>
						<div class="qr-item-docker docker-add">
							
						</div>
					</div>
				</div>
			</div>
			<div class="qr-area disabled"><?php // Area 0 ?>
				<div class="qr-heading">
					<div class="qr-heading-status">未启用</div>
				</div>
				<div class="qr-container">
				<?php
				if (!empty($qrs_range_0)){
					foreach ($qrs_range_0 as $qr_info){
				?>
					<div class="qr-position"><?php // qr图的位置，用于为qr图提供停靠?>
						<div class="qr-position-order">拖上去启用</div>
						<div class="qr-item-docker">
							<div class="qr-item"><?php // 可拖动元素?>
								<div class="qr-filename"><?php echo $qr_info['filename'];?></div>
								<div class="qr-img-wrap">
									<div class="qr-img-common qr-method"><?php echo img_method($qr_info['method']);?></div>
									<?php
									$method = $qr_info['method'];
									$filename = $qr_info['filename'];
									$dest_file = "img/qr-files/$method/$filename";
									$img_exif_type = exif_imagetype($dest_file);
									switch ($img_exif_type){
									case IMAGETYPE_JPEG:
										$img_type = 'image/jpeg';
										break;
									case IMAGETYPE_PNG:
										$img_type = 'image/png';
										break;
									case IMAGETYPE_BMP:
										$img_type = 'image/bmp';
										break;
									case IMAGETYPE_GIF:
										$img_type = 'image/gif';
										break;
									}
									$img_content = file_get_contents($dest_file);
									$b64 = base64_encode($img_content);
									?>
									<div class="qr-img-common qr-img" style="background-image:url(<?php echo "data:$img_type;base64,$b64";?>);"></div><?php // 收款码图片本体?>
								</div>
								<div class="qr-info-edit">
									<div class="qr-text-wrap">
										<div class="qr-text-head-wrap">
											<div class="qr-text-label">展示文字</div>
											<div class="btn-qr-text btn-text-out">确定</div>
										</div>
										<input class="input-text input-text-out" type="text" placeholder="对外展示文字" value="<?php echo $qr_info['text'];?>">
									</div>
									<div class="qr-text-wrap">
										<div class="qr-text-head-wrap">
											<div class="qr-text-label">备注文字</div>
											<div class="btn-qr-text btn-text-in">确定</div>
										</div>
										<input class="input-text input-text-in" type="text" placeholder="内部备注文字" value="<?php echo $qr_info['comment'];?>">
									</div>
								</div>
								<div class="btn-toggle-edit">展 开 ↓</div>
								<div class="qr-delete"><span class="btn-delete">✖</span></div>
							</div>
						</div>
					</div>
				<?php
					}
				}
				?>
					<div class="qr-position"><?php // Area 0 空位?>
						<div class="qr-position-order">拖到这里备用</div>
						<div class="qr-item-docker docker-add">
							
						</div>
					</div>
				</div>
				<!-- <div class="test" style="width:100px;height:50px;border:1px solid white"></div> -->
			</div>
		</div>
		<?php
		if ($side == 0){
		?>
		<div class="tab-wrap tab-upload">
			<div class="up-area-wrap">
				<div class="upload-item upload-item-alipay">
					<div class="up-area-name up-area-name-alipay">支付宝</div>
					<div class="up-area up-area-alipay">
						<svg class="up-area-img" viewBox="0 0 1024 1024" width="128" height="128"><path d="M1023.795 853.64v6.348a163.807 163.807 0 0 1-163.807 163.807h-696.18A163.807 163.807 0 0 1 0 859.988v-696.18A163.807 163.807 0 0 1 163.807 0h696.181a163.807 163.807 0 0 1 163.807 163.807V853.64z" fill="#009FE9" p-id="7028"></path><path d="M844.836 648.267c-40.952-14.333-95.623-34.809-156.846-57.128a949.058 949.058 0 0 0 90.094-222.573H573.325V307.14h245.711v-43.41l-245.71 2.458V143.33H472.173c-18.223 0-21.704 20.476-21.704 20.476v102.38H204.759v40.952h245.71v61.427H245.712v40.952h409.518a805.522 805.522 0 0 1-64.909 148.246c-128.384-42.795-266.186-77.604-354.233-55.08a213.564 213.564 0 0 0-112.003 63.27c-95.418 116.917-26.21 294.034 175.274 294.034 119.989 0 236.087-67.366 325.771-177.73 134.322 65.932 398.666 176.297 398.666 176.297V701.3s-32.352-4.095-178.96-53.033z m-563.702 144.97c-158.893 0-204.759-124.699-126.336-194.112a191.86 191.86 0 0 1 90.913-46.276c93.575-10.238 189.811 35.629 293.624 86.614-74.941 94.598-166.674 153.774-258.2 153.774z" fill="#FFFFFF" p-id="7029"></path></svg>
						<div class="up-img-name">支付宝</div>
						<div class="up-img-text">收款码拖放处</div>
					</div>
				</div>
				<div class="upload-item upload-item-wxpay">
					<div class="up-area-name up-area-name-wxpay">微信</div>
					<div class="up-area up-area-wxpay">
						<svg class="up-area-img" viewBox="0 0 1024 1024" width="128" height="128"><path d="M186.197333 0h651.605334C961.962667 0 1024 62.08 1024 186.197333v651.605334C1024 961.962667 961.962667 1024 837.802667 1024H186.197333C62.037333 1024 0 961.962667 0 837.802667V186.197333C0 62.037333 62.08 0 186.197333 0z" fill="#09BB07" p-id="6036"></path><path d="M404.096 596.266667a22.613333 22.613333 0 0 1-10.581333 2.432 23.253333 23.253333 0 0 1-20.48-12.074667l-1.706667-3.157333-64.597333-138.24c-0.810667-1.578667-0.810667-3.157333-0.810667-4.778667a11.093333 11.093333 0 0 1 11.52-11.264 13.226667 13.226667 0 0 1 7.338667 2.432l76.074666 53.034667a40.021333 40.021333 0 0 0 19.626667 5.546666 32.810667 32.810667 0 0 0 12.245333-2.389333l356.906667-155.776c-63.872-73.898667-169.386667-122.154667-288.938667-122.154667-194.730667 0-353.536 129.322667-353.536 289.109334 0 86.741333 47.488 165.461333 121.941334 218.453333 5.674667 4.053333 9.813333 11.264 9.813333 18.474667a21.930667 21.930667 0 0 1-1.706667 7.253333c-5.674667 21.632-15.573333 57.045333-15.573333 58.581333a28.458667 28.458667 0 0 0-1.706667 8.832 11.093333 11.093333 0 0 0 11.52 11.264 9.386667 9.386667 0 0 0 6.570667-2.389333l76.885333-44.16a39.893333 39.893333 0 0 1 18.816-5.589333c3.242667 0 7.381333 0.768 10.624 1.621333 37.546667 10.709333 76.373333 16.128 115.413334 16.085333 194.773333 0 353.536-129.322667 353.536-289.109333 0-48.213333-14.72-93.952-40.106667-134.186667l-406.613333 230.528-2.474667 1.621334z" fill="#FFFFFF" p-id="6037"></path></svg>
						<div class="up-img-name">微信</div>
						<div class="up-img-text">收款码拖放处</div>
					</div>
				</div>
				<div class="upload-item upload-item-huabei">
					<div class="up-area-name up-area-name-huabei">花呗</div>
					<div class="up-area up-area-huabei">
						<svg class="up-area-img" viewBox="0 0 1024 1024" width="128" height="128"><path d="M128 0h768C981.333333 0 1024 42.666667 1024 128v768c0 85.333333-42.666667 128-128 128H128C42.666667 1024 0 981.333333 0 896V128C0 42.666667 42.666667 0 128 0z" fill="#30B4FF" p-id="2724"></path><path d="M577.923879 83.642182c11.264-29.758061 63.550061-23.288242 120.707879 3.211636 154.422303 72.502303 261.368242 231.873939 261.368242 416.116364 0 252.617697-201.076364 457.029818-448 457.029818-247.683879 0-448-204.350061-448-457.076364 0-40.199758 3.196121-80.461576 15.297939-117.449697 33.792-105.472 78.010182-164.988121 132.670061-144.911515 57.157818 20.945455 57.157818 69.197576 24.963879 118.287515-22.528 33.854061-82.881939 148.092121-32.19394 261.56994 17.733818 39.408485 78.848 129.536 135.928243 146.432 247.683879 73.216 260.608-99.017697 242.889697-185.095758-17.671758-86.078061-142.336-154.484364-199.431758-188.276363-56.32-33.792-52.286061-84.48-28.16-104.572122 24.126061-20.169697 126.277818 23.272727 196.297697 52.286061 70.718061 29.758061 189.750303 20.914424 193.008485-55.559758 3.971879-76.412121-77.234424-115.075879-106.992485-128.698181-29.758061-13.699879-70.795636-42.697697-60.353939-73.293576z m-0.775758 82.121697c40.96 16.135758 62.727758 54.721939 48.252121 86.140121-14.460121 31.356121-59.516121 44.218182-100.538181 28.16-40.96-16.135758-62.712242-54.721939-48.252122-86.140121 14.460121-31.356121 59.516121-44.218182 100.476122-28.16h0.06206z" fill="#FFFFFF" p-id="2725"></path></svg>
						<div class="up-img-name">花呗</div>
						<div class="up-img-text">收款码拖放处</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		<div class="tab-wrap tab-doc">
		<?php
		echo echo_doc();
		?>
		</div>
		<div class="tab-wrap tab-member">
			<div class="user-bal-wrap">
				<div class="user-bal-heading">
					<div class="user-bal-icon"></div>
					<div class="user-bal-titlebar"></div>
					<div class="user-bal-btn-wrap">
						<div class="user-bal-btn user-bal-btn-minimize">
							<svg width="45px" height="36px">
								<line x1="17" y1="17" x2="28" y2="17" style="stroke: white; stroke-linecap: round"/>
							</svg>
						</div>
						<div class="user-bal-btn user-bal-btn-maximize">
							<svg width="45px" height="36px">
								<rect x="17" y="12" width="11" height="11" rx="3" style="stroke: white; fill: none" />
							</svg>
						</div>
						<div class="user-bal-btn user-bal-btn-close">
							<svg width="45px" height="36px">
								<line x1="16" y1="11" x2="28" y2="23" style="stroke: rgba(255,255,255,0.8); stroke-width: 1.1"/>
								<line x1="16" y1="23" x2="28" y2="11" style="stroke: rgba(255,255,255,0.8); stroke-width: 1.1"/>
							</svg>
						</div>
					</div>
				</div>
				<div class="user-bal-body">
					<div class="user-bal-date-wrap">
						<div class="user-bal-date-start">
							<span class="user-bal-date-label">开始日期</span>
							<input type="date" class="input-date input-date-start" name="input-date-start" min="2020-01-01" value="<?php echo date('Y-m-d', strtotime('this week monday', time()));?>">
						</div>
						<div class="user-bal-date-end">
							<span class="user-bal-date-label">截止日期</span>
							<input type="datetime-local" class="input-date input-date-end" name="input-date-start" min="2020-01-01T00:00" value="<?php echo date('Y-m-d\TH:i');?>">
						</div>
						<div class="btn-user-bal-query">查 询</div>
					</div>
					<div class="user-bal-date-main">
						<div class="user-bal-custom-label-wrap">
							<span class="user-bal-custom-label">查询结果</span>
							<svg style="flex: 1" width="394px" height="18px">
								<line x1="3%" y1="9" x2="100%" y2="9" style="stroke: rgba(255,255,255,0.8); stroke-width: 0.2"/>
							</svg>
						</div>
						<div class="user-bal-res-column">
							<div class="user-bal-res-time"></div>
							<div class="user-bal-res-data user-bal-res-submit">提交(标额)</div>
							<div class="user-bal-res-data user-bal-res-payed">已付(标额)</div>
							<div class="user-bal-res-data user-bal-res-submit">提交(折后额)</div>
							<div class="user-bal-res-data user-bal-res-payed">已付(折后额)</div>
						</div>
						<div class="user-bal-res-record">
							<div class="user-bal-res-time">已选时间内</div>
							<div class="user-bal-res-data user-bal-res-origin-submit-custom">-</div>
							<div class="user-bal-res-data user-bal-res-origin-payed-custom">-</div>
							<div class="user-bal-res-data user-bal-res-actual-submit-custom">-</div>
							<div class="user-bal-res-data user-bal-res-actual-payed-custom">-</div>
						</div>
						<div class="user-bal-custom-label-wrap">
						<span class="user-bal-custom-label">常用结果</span>
							<svg style="flex: 1" width="394px" height="18px">
								<line x1="3%" y1="9" x2="100%" y2="9" style="stroke: rgba(255,255,255,0.8); stroke-width: 0.2"/>
							</svg>
						</div>
						<div class="user-bal-res-column">
							<div class="user-bal-res-time"></div>
							<div class="user-bal-res-data">提交(标额)</div>
							<div class="user-bal-res-data">已付(标额)</div>
							<div class="user-bal-res-data">提交(折后额)</div>
							<div class="user-bal-res-data">已付(折后额)</div>
						</div>
						<div class="user-bal-res-record">
							<div class="user-bal-res-time">今天内</div>
							<div class="user-bal-res-data user-bal-res-origin-submit-today">-</div>
							<div class="user-bal-res-data user-bal-res-origin-payed-today">-</div>
							<div class="user-bal-res-data user-bal-res-actual-submit-today">-</div>
							<div class="user-bal-res-data user-bal-res-actual-payed-today">-</div>
						</div>
						<div class="user-bal-res-record">
							<div class="user-bal-res-time">本月内</div>
							<div class="user-bal-res-data user-bal-res-origin-submit-tomonth">-</div>
							<div class="user-bal-res-data user-bal-res-origin-payed-tomonth">-</div>
							<div class="user-bal-res-data user-bal-res-actual-submit-tomonth">-</div>
							<div class="user-bal-res-data user-bal-res-actual-payed-tomonth">-</div>
						</div>
						<div class="user-bal-res-record">
							<div class="user-bal-res-time">近 3 天</div>
							<div class="user-bal-res-data user-bal-res-origin-submit-recent-d3">-</div>
							<div class="user-bal-res-data user-bal-res-origin-payed-recent-d3">-</div>
							<div class="user-bal-res-data user-bal-res-actual-submit-recent-d3">-</div>
							<div class="user-bal-res-data user-bal-res-actual-payed-recent-d3">-</div>
						</div>
						<div class="user-bal-res-record">
							<div class="user-bal-res-time">近 7 天</div>
							<div class="user-bal-res-data user-bal-res-origin-submit-recent-d7">-</div>
							<div class="user-bal-res-data user-bal-res-origin-payed-recent-d7">-</div>
							<div class="user-bal-res-data user-bal-res-actual-submit-recent-d7">-</div>
							<div class="user-bal-res-data user-bal-res-actual-payed-recent-d7">-</div>
						</div>
						<div class="user-bal-res-record">
							<div class="user-bal-res-time">近 30 天</div>
							<div class="user-bal-res-data user-bal-res-origin-submit-recent-d30">-</div>
							<div class="user-bal-res-data user-bal-res-origin-payed-recent-d30">-</div>
							<div class="user-bal-res-data user-bal-res-actual-submit-recent-d30">-</div>
							<div class="user-bal-res-data user-bal-res-actual-payed-recent-d30">-</div>
						</div>
						<div class="user-bal-res-record">
							<div class="user-bal-res-time">近半年</div>
							<div class="user-bal-res-data user-bal-res-origin-submit-recent-hyear">-</div>
							<div class="user-bal-res-data user-bal-res-origin-payed-recent-hyear">-</div>
							<div class="user-bal-res-data user-bal-res-actual-submit-recent-hyear">-</div>
							<div class="user-bal-res-data user-bal-res-actual-payed-recent-hyear">-</div>
						</div>
						<div class="user-bal-res-record">
							<div class="user-bal-res-time">全部</div>
							<div class="user-bal-res-data user-bal-res-origin-submit-all">-</div>
							<div class="user-bal-res-data user-bal-res-origin-payed-all">-</div>
							<div class="user-bal-res-data user-bal-res-actual-submit-all">-</div>
							<div class="user-bal-res-data user-bal-res-actual-payed-all">-</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-title">会员 <span class="tab-title-sub">商户 / 信息</span></div>
			<?php
			$sql_user =
			"SELECT
				`pid`,
				`username`,
				`stat`,
				`side`,
				`qq`,
				`reg_time`,
				`reg_ip`,
				`last_time`,
				`last_ip`,
				`commission_fee_rate`,
				`balance`
				/*, `notify_method`, `key`*/
			FROM
				`user`
			WHERE
				`side` >= 10;";
			$result_user = $mysql->query($sql_user);
			$data_user = $result_user->fetch_all(MYSQLI_ASSOC);
			matrix_transposition($data_user);
			$balance_sync = [];
			$fee_rate = [];
			?>
			<div class="member-grid">
			<?php
			// 商户 / 信息
			foreach ($data_user as $col_name => $col_data){
			?>
				<div class="member-column">
					<div class="member-cell member-cell-head"><?php echo col_name($col_name);?></div>
				<?php
				$i = 0;
				foreach ($col_data as $key => $value){
					if ($col_name == 'pid'){
					?>
					<div class="member-cell member-cell-data member-cell-data-pid cell-data-center"><?php echo $value;?></div>
					<?php
					}
					else if ($col_name == 'username'){
					?>
					<div class="member-cell member-cell-data member-cell-data-username cell-data-center"><?php echo $value;?></div>
					<?php
					}
					else if ($col_name == 'side'){
					?>
					<div class="member-cell member-cell-data cell-data-center"><?php echo side_name($value);?></div>
					<?php
					}
					else if ($col_name == 'reg_ip' || $col_name == 'last_ip'){
					?>
					<div class="member-cell member-cell-data cell-data-center"><?php echo $value;?></div>
					<?php
					}
					else if ($col_name == 'stat'){
					?>
					<div class="member-cell member-cell-data cell-data-center"><?php echo stat_name($value);?></div>
					<?php
					}
					else if ($col_name == 'commission_fee_rate'){
						$fee_rate[] = $value;
					?>
					<div class="member-cell member-cell-data cell-data-center"><input class="input-fee-rate" value="<?php echo number_format($value * 100, 2, '.', '');?>" maxlength="5"> % <div class="btn-change-fee-rate disabled">修 改</div></div>
					<?php
					}
					else if ($col_name == 'balance'){
						$balance_sync[] = number_format($value / 100, 2, '.', '');
					?>
					<div class="member-cell member-cell-data member-cell-data-balance cell-data-end">
						<div class="balance-front"><?php echo $balance_sync[$i];?></div>
						<div class="btn-balance-popup">详 细</div>
					</div>
					<?php
					}
					/*
					else if ($col_name == 'notify_method'){
						echo "<div class=\"member-cell member-cell-data member-cell-data-notify cell-data-center\">".notify_name($value)."</div>";
					}
					else if ($col_name == 'key'){
						echo "<div class=\"member-cell member-cell-data member-cell-data-key\">".short_key($value)."</div>";
					}
					*/
					else{
						echo "<div class=\"member-cell member-cell-data\">$value</div>";
					}
					$i++;
				}
				?>
				</div>
			<?php
			}
			?>
				<div class="member-column">
					<div class="member-cell member-cell-head">可结算(按费率)</div>
				<?php
				$num_merch = count($data_user['pid']);
				for ($i = 0; $i < $num_merch; $i++){
					$settle_value = number_format($balance_sync[$i], 2, '.', '');
					echo "<div class=\"member-cell member-cell-data member-cell-data-settle\">";
					echo "	<div class=\"btn-settle-common btn-settle-reset\">归 零</div>";
					echo "	<input class=\"input-settle\" inputmode=\"decimal\" value=\"{$settle_value}\" maxlength=\"8\">";
					echo "	<div class=\"btn-settle-common btn-settle-max\">全 部</div>";
					echo "	<div class=\"btn-settle-common btn-settle\">结 算</div>";
					echo "</div>";
				}
				echo "<div class=\"member-cell member-cell-data member-cell-data-settle-all\">";
				echo "	<div class=\"btn-settle-common btn-settle-all-common btn-settle-reset-all\">全部归零</div>";
				echo "	<div class=\"btn-settle-common btn-settle-all-common btn-settle-max-all\">全部最大</div>";
				echo "	<div class=\"btn-settle-common btn-settle-all-common btn-settle-all\">全部结算</div>";
				echo "</div>";
				?>
				</div>
			</div>
		</div>
		<div class="tab-wrap tab-settle">
			<div class="tab-title">商户 <span class="tab-title-sub">余额 / 结算</span></div>
			<?php
			$sql_settle =
			"SELECT
				`sid`,
				`time`,
				`before_balance`,
				`amount`,
				`after_balance`,
				`operator`
			FROM
				`settle`;";
			$result_settle = $mysql->query($sql_settle);
			$data_settle = $result_settle->fetch_all(MYSQLI_ASSOC);
			matrix_transposition($data_settle);
			?>
			<div class="settle-grid">
			<?php
			foreach ($data_settle as $col_name => $col_data){
				?>
				<div class="settle-column">
					<div class="settle-cell settle-cell-head"><?php echo col_name($col_name);?></div>
				<?php
				foreach ($col_data as $key => $value){
					if (in_array($col_name, ['before_balance', 'amount', 'after_balance'])){
					?>
					<div class="settle-cell settle-cell-data cell-data-end"><?php echo number_format($value / 100, 2, '.', '');?></div>
					<?php
					}
					else{
					?>
					<div class="settle-cell settle-cell-data cell-data-center"><?php echo $value;?></div>
					<?php
					}
					?>
				<?php
				}
				?>
				</div>
			<?php
			}
			?>
			</div>
		</div>
		<div class="tab-wrap tab-me">
			<div class="tab-title">会员 <span class="tab-title-sub">账号 / 设置</span></div>
			<div>建设中</div>
		</div>
		<div class="tab-wrap tab-log">
			<div class="tab-title">系统 <span class="tab-title-sub">制作 / 日志</span></div>
			<div class="text-wrap">
				<div class="log-content log-title"><span class="log-time">2025-02-13</span>  未来功能：</div>
				<div class="log-content log-text log-text-todo">
					1、系统：注册用户自身信息展示、设置、密码管理<br>
					2、商户 API：自动化订单查询接口<br>
					3、管理员后台：对普通注册用户、客服人员角色的进一步管理<br>
				</div>
				<div class="log-content log-title"><span class="log-time">2025-05-11</span> 功能完善</div>
				<div class="log-content log-text log-text-done">
					1、订单：修复订单表每页显示条数存在的错误。<br>
					2、对接：修改参数名为 pid（与市场一致）<br>
					3、完善：结算功能完成，融入系统。<br>
					4、二维码管理：废弃多段金额，归一为单一金额范围，固定为 200 ~ 1000。<br>
					5、支付方式：增加蚂蚁花呗（huabei）支付方式，并提供轮转式二维码，与支付宝、微信协同。<br>
					6、收款码管理：修复目录和 REDIS 索引同步算法的重大错误。<br>
					7、数据库：修复数据库列名错误。commision_fee_rate_actual -> commission_fee_rate_actual
					8、设置：微信随机减费率从 0.30% ~ 0.35% -> 0.10% ~ 0.15%
				</div>
				<div class="log-content log-title"><span class="log-time">2025-02-13</span> 功能完善</div>
				<div class="log-content log-text log-text-done">
					1、系统：增加客服角色，客服角色从属于管理员组（手动）<br>
					2、管理员后台：客服角色看不到 <b class="text-red">[收款码 / 上传]</b> 板块，不能上传二维码<br>
					3、商户后台：后台界面全部重做，与管理员后台界面一致<br>
				</div>
				<div class="log-content log-title"><span class="log-time">2025-02-10</span>  细节完善</div>
				<div class="log-content log-text log-text-done">
					1、管理员后台：记录最后一次选择的菜单项<br>
					2、管理员后台：删除二维码时完全同步前后端二维码布局数据<br>
					3、管理员后台：增加开发日志<br>
				</div>
				<div class="log-content log-title"><span class="log-time">2025-02-09</span> 完成基线功能，上线</div>
				<div class="log-content log-text log-text-done">
					1、系统：注册 / 登录<br>
					2、系统：接收型支付网关（参数整理、生成订单、展示收款码、文档）<br>
					3、商户后台：账号设置（通知方式、私钥重置）<br>
					4、商户后台：订单 / 管理（筛选、通知）<br>
					5、管理员后台：订单 / 管理（筛选、支付、通知）<br>
					6、管理员后台：商户 / 信息（以商户为依据统计流水金额）<br>
					7、管理员后台：收款码 / 管理（启停用、分段、编辑）<br>
					8、管理员后台：收款码 / 上传（拖拽分类）<br>
				</div>
			</div>
		</div>
	</div>
</body>
</html>