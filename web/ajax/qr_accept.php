<?php
/** 处理文件拖拉上传
 * $_FILES[$method] = [
 *   'name' => 'PP2232.ICO',
 *   'full_path' => 'PP2232.ICO',
 *   'type' => 'image/x-icon',
 *   'tmp_name' => '/tmp/phpbdu145jkq0n89brpqie',
 *   'error' => 0,
 *   'size' => 12345,
 * ]
 */

const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include '../lib/tools.php';
session_start();
$identity = check_identity();
switch ($identity){
case 'is_customer':
	$res['code'] = 'customer';
	echo json_encode($res);
	exit;
case 'is_neither':
	$res['code'] = 'neither';
	echo json_encode($res);
	exit;
case 'both':
	$res['code'] = 'both';
	echo json_encode($res);
	exit;
}
if (!empty($_GET)){
	$res['code'] = 'get_not_null';
	echo json_encode($res);
	exit;
}
if (empty($_FILES)){
	$res['code'] = 'no_files';
	echo json_encode($res);
	exit;
}
if (count($_FILES) != 1){
	$res['code'] = 'file_not_uniq';
	echo json_encode($res);
	exit;
}
$method = array_keys($_FILES)[0];
if (!in_array($method, ['alipay', 'wxpay', 'huabei'])){
	$res['code'] = 'method_not_found';
	echo json_encode($res);
	exit;
}
$file = $_FILES[$method];
/*
$file: [
	'name' => 'comfyui-sixgod_prompt.rar',
	'full_path' => 'comfyui-sixgod_prompt.rar',
	'type' => 'application/octet-stream',
	'tmp_name' => '/tmp/phpjb7prkq2cllf1CzuWzn',
	'error' => 0,
	'size' => 4412846,
]
*/
$file_mime = exif_imagetype($file['tmp_name']);
$file_name = $file['name'];
$name_info = pathinfo($file_name);
// 扩展名不存在
if (!isset($name_info['extension']) || !$name_info['extension']){
	$res['code'] = 'ext_not_exists';
	echo json_encode($res);
	exit;
}
// 扩展名不匹配
$ext = strtolower($name_info['extension']);
if (!in_array($file_mime, [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP])){
	$res['code'] = 'unsupported_mime';
	$res['name'] = $file_name;
	echo json_encode($res);
	exit;
}
if ($file_mime == IMAGETYPE_JPEG){
	if (!in_array($ext, ['jpg', 'jpeg', 'jpe'])){
		$res['code'] = 'ext_not_match';
		$res['mime'] = 'JPEG';
		$res['ext'] = $ext;
		echo json_encode($res);
		exit;
	}
}
else if ($file_mime == IMAGETYPE_PNG){
	if ($ext != 'png'){
		$res['code'] = 'ext_not_match';
		$res['mime'] = 'png';
		$res['ext'] = $ext;
		echo json_encode($res);
		exit;
	}
}
else if ($file_mime == IMAGETYPE_GIF){
	if ($ext != 'gif'){
		$res['code'] = 'ext_not_match';
		$res['mime'] = 'gif';
		$res['ext'] = $ext;
		echo json_encode($res);
		exit;
	}
}
else if ($file_mime == IMAGETYPE_BMP){
	if ($ext != 'bmp'){
		$res['code'] = 'ext_not_match';
		$res['mime'] = 'gif';
		$res['ext'] = $ext;
		echo json_encode($res);
		exit;
	}
}
$dest = "img/qr-files/$method/$file_name";
if (move_uploaded_file($file['tmp_name'], "../$dest")){
	$res['code'] = 'ok';
}
else{
	$res['code'] = 'move_error';
}
echo json_encode($res);