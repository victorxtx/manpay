<?php
const VALID = true;
include_once 'lib/config.php';
include_once 'lib/mysql.php';
include_once 'lib/tools.php';
session_start();
$identity = check_identity();
if ($identity == 'both'){
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("who.php");
	}, 500);
</script>
<?php
	exit;
}
else if ($identity == 'is_customer'){
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("customer.php");
	}, 500);

</script>
<?php
	exit;
}
else if ($identity == 'neither'){
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("./");
	}, 500);

</script>
<?php
	exit;
}
// is_member，所以 $_SESSION['side'] 存在
$side = $_SESSION['side'];
if (in_array($side, $merch_side)){ // 注册用户
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("merch.php");
	}, 500);
</script>
<?php
}
else if (in_array($side, $admin_side)){ // 管理侧
?>
<script type="text/javascript">
	setTimeout(() => {
		location.replace("admin.php");
	}, 500);

</script>
<?php
}