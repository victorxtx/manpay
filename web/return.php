<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>商户展示成功页面</title>
	<link rel="stylesheet" type="text/css" href="css/alert">
	<link rel="icon" href="img/rmb.svg">
	<style>

	</style>
</head>
<body>
<script type="text/javascript">
	alert('这里是模拟商户的 return_url 展示“成功支付”的页面。请商户按照文档对参数进行排序验签后确定这个页面展示的内容', '您已成功支付')
	window.alert = function(
		msg,
		title = '消息',
		btnOk = () => {},
		timeout = 3000,
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
</script>
</body>
</html>