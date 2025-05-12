<?php
const VALID = true;
include '../lib/config.php';
include '../lib/mysql.php';
include 'lib/tools.php';
$identity = check_identity();

switch ($identity){
	case 'is_customer':
		clear_customer();
		break;
	case 'is_member':
		clear_member();
		break;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/alert.css">
	<title>欢迎再来...</title>
</head>
<body>
<script type="text/javascript">
window.alert = function(
	msg,
	title = '消息',
	btnOk = () => {},
	timeout = 5000,
	parent = document.body,
	width = 'fit-content',
	height = 'fit-content',
){
	let lastIsoBox = document.querySelector('.iso-box');
	if (lastIsoBox){
		lastIsoBox.remove();
	}
	let	isoBox = document.createElement('div');
	isoBox.classList.add('iso-box')
	isoBox.style.position = 'absolute';
	isoBox.style.width = visualViewport.width + visualViewport.pageLeft + 'px';
	isoBox.style.height = visualViewport.height + visualViewport.pageTop + 'px';
	// isoBox.style.width = '100vw';
	// isoBox.style.height = '100vh';
	parent.append(isoBox);
	let popupBox = document.createElement('div');
	popupBox.classList.add('popup-box');
	if (!isNaN(width)){
		popupBox.style.width = width + 'px';
	}
	else{
		popupBox.style.width = width;
	}
	if (!isNaN(height)){
		popupBox.style.height = height + 'px';
	}
	else{
		popupBox.style.height = height;
	}
	isoBox.append(popupBox);
	let popupTitle = document.createElement('div');
	popupTitle.classList.add('popup-title');
	popupTitle.innerHTML = title;
	let popupContent = document.createElement('div');
	popupContent.classList.add('popup-content');
	popupContent.innerHTML = msg;
	let btnPopupWrap = document.createElement('div');
	btnPopupWrap.classList.add('btn-popup-wrap');
	let btnPopupOk = document.createElement('div');
	btnPopupOk.classList.add('btn-popup-ok');
	btnPopupOk.innerHTML = '好的';
	btnPopupOk.addEventListener('click', function(){
		btnOk();
		isoBox.remove();
	});
	btnPopupWrap.append(btnPopupOk);
	popupBox.append(popupTitle, popupContent, btnPopupWrap);
	setTimeout(() => {
		isoBox.remove();
	}, timeout);
}
// iso-box cover
window.addEventListener('resize', function(){
	let isoBox = document.querySelector('.iso-box');
	if (!isoBox){
		return;
	}
	isoBox.style.width = visualViewport.width + visualViewport.pageLeft + 'px';
	isoBox.style.height = visualViewport.height + visualViewport.pageTop + 'px';
})
window.addEventListener('scroll', function(e){
	let isoBox = document.querySelector('.iso-box');
	if (!isoBox){
		return;
	}
	isoBox.style.width = visualViewport.width + visualViewport.pageLeft + 'px';
	isoBox.style.height = visualViewport.height + visualViewport.pageTop + 'px';
})
alert('感谢您使用本系统，欢迎再来！', '再会~', function(){location.replace('../');}, 3000);
setTimeout(() => {
	location.replace('../');
}, 3000);
</script>
</body>
</html>