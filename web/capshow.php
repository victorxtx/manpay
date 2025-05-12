<?php
if (session_status() === PHP_SESSION_NONE){
	session_start();
}
const VALID = true;
include 'lib/tools.php';
$identity = check_identity();
if ($identity == 'both'){
	exit;
}
if ($identity == 'is_customer'){
	exit;
}
if (!in_array($_GET['a'], [0, 1])){
	exit;
}
if ($_GET['a'] == 0){
	$_SESSION['pf_login_captcha'] = captcha();
}
else if ($_GET['a'] == 1){
	$_SESSION['pf_reg_captcha'] = captcha();
}