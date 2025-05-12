var inputTextAcc = document.querySelector('.input-text-acc');
var inputTextPass = document.querySelector('.input-text-pass');
var btnForget = document.querySelector('.btn-forget');
var btnEnter = document.querySelector('.btn-enter');
function GetXmlHttpObject(){
	var xmlHttp = null;
	try{
		// Firefox, Opera 8.0+, Safari
		// console.log("XMLHttpRequest");
		xmlHttp = new XMLHttpRequest();
	}
	catch(e){
		// Internet Explorer
		try{
			// console.log("Msxml2");
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			// console.log("Microsoft");
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
onload = function(){
	
}
inputTextAcc.addEventListener('keypress', (e) => {
	if (e.keyCode == 13){
		e.preventDefault();
		goAuth();
	}
});
inputTextPass.addEventListener('keypress', (e) => {
	if (e.keyCode == 13){
		e.preventDefault();
		goAuth();
	}
});
btnEnter.addEventListener('click', goAuth); // 进入 click
function goAuth(){
	let inputAcc = inputTextAcc.value;
	let inputPass = inputTextPass.value;
	if (inputAcc == '' || inputPass.value == ''){
		alert('两个都要填一下哦！', '出错啦~');
		return;
	}
	if (inputAcc.length < 5){
		alert('用户名最短5位哦', '出错啦~');
		return;
	}
	if (inputAcc.length > 16){
		alert('用户名最长16位哦', '出错啦~');
		return;
	}
	if (inputPass.length < 6){
		alert('密码最短6位哦', '出错啦~');
		return;
	}
	if (inputPass.length > 32){
		alert('密码最长32位哦', '出错啦~');
		return;
	}

	let xhr = GetXmlHttpObject();
	xhr.open('post', 'ajax/login.php', true);
	xhr.setRequestHeader("Content-type", "application/json");
	xhr.onreadystatechange = function(){
		if (xhr.readyState == 4 && xhr.status == 200){
			console.log(xhr.response);
			let res = JSON.parse(xhr.response);
			switch (res.flag){
				case 0:
					location.replace('service.php');
					break;
				case 1: // important
					alert('账号有误，请再试试', '出错了哦~');
					break;
				case 2: // important
					alert('密码有误，请再试试', '出错了哦~·');
					break;
				case 9:
					setTimeout(() => {
						location.replace('../merch.php');
					}, 3000);
					alert('商户暂不能使用聊天系统，<br>3 秒后转到商户后台...', '通知~');
					break;
				case 10:
					alert('数据库出错，麻烦帮忙联系一下管理员~', '出错了~');
					break;
				case 21:
					location.replace('../who.php');
					break;
				case 22:
					location.replace('../');
					break;
				case 23:
					setTimeout(() => {
						location.replace('../customer.php');
					}, 3000);
					alert('玩家请移步您的临时订单列表，3 秒后跳转...');
				// case 3 ~ 8 格式攻击
				default:
					location.replace('https://www.bing.com/');
					return;
			}
		}
	}
	let objAuth = {
		"user": inputTextAcc.value,
		"pass": inputTextPass.value
	}
	xhr.send(JSON.stringify(objAuth));
}
btnForget.addEventListener('click', goForget);
function goForget(){
	alert('');
}
window.alert = function(
	msg,
	title = '消息',
	timeout = 500000,
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
