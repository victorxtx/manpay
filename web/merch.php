<?php
// var_dump($_SESSION['side']);exit;
const VALID = true;
include_once 'lib/config.php';
include_once 'lib/mysql.php';
include_once 'lib/tools.php';
session_start();
$identity = check_identity();
if ($identity == 'is_customer') {
	echo '<script type="text/javascript">location.replace("customer.php");</script>';
	exit;
}
if ($identity == 'neither') {
	echo '<script type="text/javascript">location.replace("./");</script>';
	exit;
}
if ($identity == 'both') {
	echo '<script type="text/javascript">location.replace("./who.php");</script>';
	exit;
}
// 单身份：商户
// if ($_SERVER['REMOTE_ADDR'] == '10.0.0.1'){
// 	echo $_SESSION['side'];
// }
if (in_array($_SESSION['side'], $admin_side)) { // 如果管理员跑到 merch 来，就弹回去
?>
	<script type="text/javascript">
		setTimeout(() => {
			location.replace('admin.php');
		}, 300);
	</script>
<?php
	exit;
}
// 所以 $_SESSION['username'] 必定存在且唯一
$username = $_SESSION['username'];
$conn = connect();
$sql_user = "SELECT * FROM `user` WHERE `username` = '$username'";
$result_user = execute($conn, $sql_user);
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
	<link rel="stylesheet" type="text/css" href="css/grid-filter.css">
	<link rel="stylesheet" type="text/css" href="css/merch.css">
	<link rel="stylesheet" type="text/css" href="css/grid-order.css">
	<link rel="stylesheet" type="text/css" href="css/tab-doc.css">
	<link rel="icon" href="img/rmb.svg">
	<title>商户后台管理</title>
</head>

<body>
	<div class="header">
		<div class="logo">平台首页</div>

		<div class="button red small btn-exit">退 出</div>
		<div class="user-info"><?php echo $username; ?></div>
	</div>
	<div class="body">
		<div class="left">
			<div class="menu-cata">应用</div>
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
							<path d="M384.75581 648.54146c-4.437333-3.445841-39.635302-30.069841-137.289143-36.547047-5.371937-0.446984-10.784508-4.25854-15.555048-7.452445-6.070857-4.039111-12.377397-8.281397-16.97727-13.750857-7.476825-8.923429-15.920762-16.432762-23.015619-26.339555-47.477841-66.186159-94.004825-79.473778-100.961524-82.098794-9.264762-4.469841-44.755302 1.032127-27.704889 24.697905 1.080889 0.178794 54.288254 74.922667 57.742223 80.896 6.111492 10.548825 13.027556 14.864254 19.521016 22.267936 5.932698 6.761651 12.499302 12.978794 18.562031 19.626667 0 0-34.027683-20.146794-42.179047-28.972699-3.632762-3.957841-5.380063-9.622349-8.27327-14.319746a351.963429 351.963429 0 0 0-10.540699-15.904508 543.857778 543.857778 0 0 0-13.954031-19.301587c-6.160254-8.021333-12.55619-15.872-18.903365-23.755174-3.949714-4.908698-8.411429-9.64673-11.67035-14.774858-16.091429-25.421206 2.283683-40.626794 19.772953-42.544761-0.845206-2.925714-8.890921-79.798857-10.296889-92.704508-4.01473-37.059048-7.980698-64.512-29.224635-71.980699-5.477587-1.934222-9.346032-1.284063-13.425778 3.023238-4.827429 5.071238-7.704381 31.98781-9.094095 49.005715 0 0-9.289143 91.314794-7.054222 133.924571 1.194667 22.853079 72.183873 180.06146 92.582603 204.353016 20.382476 24.283429 179.923302 116.248381 179.923301 116.248381 5.331302 4.437333 34.872889 27.184762 34.872889 57.010793v64.747683l171.999492 0.203175 0.146286-59.416381c0.398222-10.979556 2.267429-21.902222 3.584-32.841143 3.74654-12.491175 0.747683-36.100063-0.999619-45.267302-19.976127-89.153016-93.549714-141.815873-101.587301-148.033016zM1012.703492 357.603556c-1.397841-17.026032-4.266667-43.934476-9.094095-49.005715-4.079746-4.307302-7.956317-4.95746-13.433905-3.023238-21.227683 7.476825-25.209905 34.929778-29.216508 71.980699-1.414095 12.905651-9.451683 89.778794-10.305016 92.704508 17.48927 1.917968 35.872508 17.123556 19.772953 42.544761-3.250794 5.136254-7.704381 9.866159-11.662223 14.774858-6.347175 7.883175-12.751238 15.733841-18.911492 23.755174a533.536508 533.536508 0 0 0-13.945904 19.301587 345.185524 345.185524 0 0 0-10.540699 15.904508c-2.885079 4.697397-4.632381 10.353778-8.281397 14.319746-8.159492 8.834032-42.179048 28.972698-42.179047 28.972699 6.06273-6.647873 12.63746-12.865016 18.570158-19.626667 6.49346-7.403683 13.401397-11.719111 19.512889-22.267936 3.470222-5.973333 56.677587-80.717206 57.75035-80.896 17.050413-23.665778-18.448254-29.167746-27.713016-24.697905-6.948571 2.616889-53.475556 15.912635-100.953397 82.098794-7.094857 9.906794-15.546921 17.424254-23.015619 26.339555-4.599873 5.477587-10.91454 9.711746-16.985397 13.750857-4.77054 3.193905-10.183111 7.00546-15.555048 7.452445-97.653841 6.477206-132.843683 33.101206-137.289142 36.547047-8.037587 6.22527-81.603048 58.88-101.587302 148.024889-1.755429 9.167238-4.746159 32.776127-0.999619 45.267302 1.316571 10.938921 3.193905 21.861587 3.584 32.841143l0.146286 59.416381 171.999492-0.203175v-64.747683c0-29.826032 29.541587-52.57346 34.872889-57.010793 0 0 159.532698-91.956825 179.923301-116.248381s91.371683-181.499937 92.574476-204.353016c2.251175-42.601651-7.037968-133.916444-7.037968-133.916444zM615.659683 560.72127c81.594921-76.751238 182.889651-172.056381 196.803047-256.869587 9.768635-59.977143 0.219429-109.413587-28.379428-146.944-28.867048-37.888-72.78527-59.513905-134.282159-66.121143a165.172825 165.172825 0 0 0-16.684699-0.869588c-43.495619 0-87.413841 16.205206-121.116444 43.568762-33.694476-27.363556-77.620825-43.568762-121.108317-43.568762-5.36381 0-10.752 0.276317-16.684699 0.869588-61.496889 6.607238-105.423238 28.233143-134.282159 66.121143-28.606984 37.530413-38.148063 86.966857-28.379428 146.944 13.913397 84.821333 115.208127 180.118349 196.803047 256.869587 0 0 40.699937 40.846222 52.931048 51.330032 9.191619 7.891302 29.135238 23.79581 50.728635 23.795809 21.601524 0 41.545143-15.904508 50.736762-23.795809 12.214857-10.48381 52.914794-51.330032 52.914794-51.330032z m-207.262477-369.615238c-51.98019 0-94.281143 37.61981-94.281143 83.862349a25.396825 25.396825 0 1 1-50.809904 0c0-74.264381 65.080889-134.672254 145.091047-134.672254a25.396825 25.396825 0 1 1 0 50.809905z"></path>
						</svg>
					</div>
					会员
				</div>
				<label class="menu-item menu-item-me" id="menu-item-me">
					<div class="menu-item-icon menu-item-icon-me">
						<svg class="menu-item-svg menu-item-svg-me" viewBox="0 0 1024 1024">
							<path d="M512 483.57376A241.78688 241.78688 0 1 1 512 0a241.78688 241.78688 0 0 1 0 483.57376zM0 967.0656v-28.42624c0-219.9552 178.29888-398.25408 398.21312-398.25408h227.57376c219.9552 0 398.21312 178.29888 398.21312 398.25408v28.42624c0 31.41632-25.47712 56.89344-56.89344 56.89344H56.9344A56.89344 56.89344 0 0 1 0 967.10656z"></path>
						</svg>
					</div>
					我 / 设置
				</label>
			</div>
		</div>
		<div class="right">
			<div class="tab-wrap tab-order">
				<div class="tab-title">支付API</div>
				<div class="api-info-grid">
					<div class="api-info-line">
						<div class="api-cell api-info-name">您的商户pid</div>
						<div class="api-cell api-info-value">
							<div class="api-info-value-text"><?php echo $pid = $data_user['pid']; ?></div>
						</div>
					</div>
					<div class="api-info-line">
						<div class="api-cell api-info-name">您的商户秘钥</div>
						<div class="api-cell api-info-value">
							<span class="api-info-key"><?php echo $key; ?></span>
							<div class="btn-api btn-reset">重置key</div>
						</div>
					</div>
					<div class="api-info-line">
						<div class="api-cell api-info-name">通知回调方式</div>
						<div class="api-cell api-info-value">
							<label class="method-wrap get-wrap">
								<input class="radio-method" id="radio-get" type="radio" name="callback-method" value="0" <?php if (!$notify_method) echo ' checked'; ?>>
								<label class="label-method" for="radio-get">GET</label>
							</label>
							<label class="method-wrap post-wrap">
								<input class="radio-method" id="radio-post" type="radio" name="callback-method" value="1" <?php if ($notify_method) echo ' checked'; ?>>
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
				<div class="tab-title"><span class="tab-title-sub">筛选</span></div>
				<div class="filter-grid">
					<div class="filter-grid-row">
						<div class="filter-grid-cell filter-grid-cell-name">订单状态筛选</div>
						<div class="filter-grid-cell filter-grid-cell-value">
							<?php
							for ($i = 0; $i <= 2; $i++) {
							?>
								<label class="order-status-wrap" for="radio-filter-status-<?php echo $i; ?>">
									<input class="radio-filter-status" id="radio-filter-status-<?php echo $i; ?>" type="radio" name="radio-filter-status" value="<?php echo $i; ?>" <?php if ($search_filter == $i) echo ' checked'; ?>>
									<label class="order-status" for="radio-filter-status-<?php echo $i; ?>"><?php
									switch ($i) {
									case 0:
										echo '仅<span class="text-red">未支付</span>且<span class="text-red">未通知</span>订单';
										break;
									case 1:
										echo '包含<span class="text-green">已支付</span>但<span class="text-red">未通知</span>订单';
										break;
									case 2:
										echo '包含<span class="text-green">已支付</span>且<span class="text-green">已通知</span>的<span class="text-green">全部</span>订单';
										break;
									}
									?></label>
								</label>
							<?php
							}
							?>
						</div>
						<div class="filter-grid-cell filter-grid-cell-action">
							<div class="btn-filter btn-filter-confirm disabled">确 定</div>
						</div>
					</div>
					<div class="filter-grid-row">
						<div class="filter-grid-cell filter-grid-cell-name"><span class="text-red">订单号</span>搜索</div>
						<div class="filter-grid-cell filter-grid-cell-value"><input class="search-text" type="text"></div>
						<div class="filter-grid-cell filter-grid-cell-action">
							<div class="btn-filter btn-filter-search">搜 索</div>
						</div>
					</div>
				</div>
				<?php
				$str_status_filter = '';
				switch ($search_filter) {
					case 0:
						$str_status_filter = "WHERE `pay_status` = 0 AND `notify_status` = 0 AND `pid` = $pid";
						break;
					case 1:
						$str_status_filter = "WHERE `notify_status` = 0 AND `pid` = $pid";
						break;
					case 2:
						$str_status_filter = "WHERE `pid` = $pid";
						break;
				}
				$sql_num_all = "SELECT COUNT(*) FROM `order`$str_status_filter;";
				$result_num_all = execute($conn, $sql_num_all);
				$num_all = $result_num_all->fetch_row()[0];
				$num_pages = ceil($num_all / 20);
				$str_order_cols = '';
				foreach ($order_cols as $col) {
					$str_order_cols .= "`$col`,";
				}
				$str_order_cols = substr($str_order_cols, 0, -1);
				$sql_order = "SELECT $str_order_cols FROM `order`$str_status_filter ORDER BY `oid` DESC LIMIT 0, 20;";
				$result_order = execute($conn, $sql_order);
				$orders = mysqli_fetch_all($result_order, MYSQLI_ASSOC);
				matrix_transposition($orders);
				?>

				<div class="order-grid">
					<?php
					foreach ($orders as $col_name => $col_value) { // 列包
					?>
						<div class="cell-column">
							<?php
							if ($col_name == 'notify_status' || $col_name == 'pay_status') {
							?>
								<div class="order-cell order-cell-head" style="text-align:center"><?php echo col_name($col_name); ?></div>
							<?php
							} else if ($col_name == 'name') {
							?>
								<div class="order-cell order-cell-head cell-data-center" style="text-align:center"><?php echo col_name($col_name); ?></div>
							<?php
							} else {
							?>
								<div class="order-cell order-cell-head"><?php echo col_name($col_name); ?></div>
							<?php
							}
							?>
							<?php
							foreach ($col_value as $key => $value) {
								if ($col_name == 'pay_status') {
							?>
									<div class="order-cell order-cell-data">
										<?php
										if ($value) { // pay_status == 1
										?>
											<div class="status pay-status status-yes">已支付</div>
										<?php
										} else { // pay_status == 0
										?>
											<div class="status pay-status status-no">未支付</div>
										<?php
										}
										?>
									</div>
								<?php
								} else if ($col_name == 'oid') {
								?>
									<div class="order-cell order-cell-data order-cell-oid"><?php echo $value; ?></div>
								<?php
								} else if ($col_name == 'out_trade_no') {
								?>
									<div class="order-cell order-cell-data" style="max-width:480px"><?php echo $value; ?></div>
								<?php
								} else if ($col_name == 'money' || $col_name == 'commission') {
								?>
									<div class="order-cell order-cell-data"><?php echo number_format($value / 100, 2); ?></div>
								<?php
								} else if ($col_name == 'notify_status') {
								?>
									<div class="order-cell order-cell-data" style="width:112px;justify-content:space-between">
										<?php
										if ($value) { // notify_status == 1
										?>
											<div class="status notify-status status-yes">已通知</div>
										<?php
										} else {
										?>
											<div class="status notify-status status-no">未通知</div>
										<?php
										}
										?>
										<div class="do-notify">补发</div>
									</div>
								<?php
								} else if ($col_name == 'pay_time' || $col_name == 'notify_time') {
									$class_name = $col_name == 'notify_time' ? 'notify-time' : 'pay-time';
								?>
									<div class="order-cell order-cell-data cell-data-center <?php echo $class_name; ?>"><?php
									if ($value){
										echo $value;
									}
									else{
										echo '-';
									}
									?></div>
								<?php
								} else if ($col_name == 'notifier' || $col_name == 'payer') {
								?>
									<div class="order-cell order-cell-data cell-data-center <?php echo $col_name; ?>"><?php
									if ($value){
										echo $value;
									}
									else{
										echo '-';
									}
									?></div>
								<?php
								} else {
								?>
									<div class="order-cell order-cell-data"><?php echo $value; ?></div>
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
				if ($num_pages > 1) {
				?>
					<div class="page-grid">
						<?php
						for ($i = 0; $i < $num_pages; $i++) {
						?>
							<div class="button blue btn-order-page<?php
							if ($i == 0){
								echo ' disabled';
							}
							echo " order-page-$i";
							?>">第 <?php echo $i + 1; ?> 页</div>
						<?php
						}
						?>
					</div>
				<?php
				} else {
				?>
					<div class="button blue btn-order-page disabled order-page-0">第 1 页（当前）</div>
				<?php
				}
				?>
			</div>
			<div class="tab-wrap tab-doc">
				<?php echo echo_doc() ?>
			</div>
			<div class="tab-wrap tab-me"></div>
		</div>
	</div>
	<script type="text/javascript" src="js/merch.js"></script>
</body>

</html>