var header = document.querySelector('.header');
var btnLogin = document.querySelector('.btn-login');
var btnReg = document.querySelector('.btn-reg');
var btnLogout = document.querySelector('.btn-logout');
var btnAdmin = document.querySelector('.btn-admin');

// login popup & sub input
var loginPopup = document.querySelector('.login-popup');
var loginInputUser = document.querySelector('.login-input-user');
var loginInputPass = document.querySelector('.login-input-pass');
var loginInputCapt = document.querySelector('.login-input-capt');
var loginSubmit = document.querySelector('.login-submit');
var regPopup = document.querySelector('.reg-popup');
var regInputUser = document.querySelector('.reg-input-user');
var regInputPass = document.querySelector('.reg-input-pass');
var regInputQq = document.querySelector('.reg-input-qq');
var regInputCapt = document.querySelector('.reg-input-capt');
var regSubmit = document.querySelector('.reg-submit');
// callback
window.onload = function(){
	let capShowLogin = document.querySelector('.captcha-show-login');
	let capShowReg = document.querySelector('.captcha-show-reg');
	if (capShowLogin){
		setTimeout(() => {
			let tLoing = (new Date()).getTime();
			capShowLogin.style.backgroundImage = 'url(capshow.php?a=0&t=' + tLoing + ')';
		}, 100);
	}
	if (capShowReg){
		setTimeout(() => {
			let tReg = (new Date()).getTime();
			capShowReg.style.backgroundImage = 'url(capshow.php?a=1&t=' + tReg + ')';
		}, 200);
	}
}
if (btnLogin){
	btnLogin.addEventListener('click', btnLoginClick);
}
if (btnAdmin){
	btnAdmin.addEventListener('click', goAdmin);
}
function btnLoginClick(){ // login popup show
	let loginPopup = document.querySelector('.login-popup');
	let regPopup = document.querySelector('.reg-popup');
	if (loginPopup.classList.contains('show')){
		loginPopup.classList.remove('show');
	}
	else{
		loginPopup.classList.add('show');
		regPopup.classList.remove('show');
		loginInputUser.focus();
	}
}
if (btnReg){
	btnReg.addEventListener('click', btnRegClick);
}
function btnRegClick(){ // reg popup show
	let loginPopup = document.querySelector('.login-popup');
	let regPopup = document.querySelector('.reg-popup');
	if (regPopup.classList.contains('show')){
		regPopup.classList.remove('show');
	}
	else{
		regPopup.classList.add('show');
		loginPopup.classList.remove('show');
		regInputUser.focus();
	}
}
if (loginSubmit){ // login Submission
	loginSubmit.addEventListener('click', loginSubmitDo)
}
function loginSubmitDo(){
	let loginPopup = document.querySelector('.login-popup');
	let loginUserVal = document.querySelector('.login-input-user').value;
	let loginPassVal = document.querySelector('.login-input-pass').value;
	let loginCaptVal = document.querySelector('.login-input-capt').value;
	if (loginUserVal == "" || loginPassVal == "" || loginCaptVal == ""){
		alert('三个都要填一下哦', '出错啦~');
	}
	else if (loginUserVal.length < 5){
		alert('用户名最短5位哦', '出错啦~');
	}
	else if (loginUserVal.length > 16){
		alert('用户名最长16位哦', '出错啦~');
	}
	else if (loginPassVal.length < 6){
		alert('密码最短6位哦', '出错啦~');
	}
	else if (loginPassVal.length > 32){
		alert('密码最长32位哦', '出错啦~');
	}
	else if (loginCaptVal.length != 5){
		alert('验证码应该是5位哦', '出错啦~');
	}
	else{
		let xhrLogin = GetXmlHttpObject();
		xhrLogin.open('post', 'ajax/login.php', true);
		xhrLogin.setRequestHeader('content-type','application/json');
		xhrLogin.addEventListener('readystatechange', function(){
			if (xhrLogin.readyState == 4 && xhrLogin.status == 200){
				// console.log(xhrLogin.response);
				if (xhrLogin.responseText == ''){
					return;
				}
				let res = null;
				try{
					res = JSON.parse(xhrLogin.response);
				}
				catch(err){
					console.log(err);
					return;
				}
				let capImgLogin = document.querySelector('.captcha-show-login');
				switch (res.code){
					case 'input_not_all_filled':
					case 'user_too_short':
					case 'user_too_long':
					case 'user_invalid_char':
					case 'pass_too_short':
					case 'pass_too_slong':
					case 'pass_invalid_char':
					case 'capt_length_error':
					case 'capt_invalid_char':
					case 'get_the_fuck_off':
					case 'key_count_error':
					case 'key_name_mismatch':
						location.replace('https://www.mps.gov.cn/');
						break;
					case 'user_reserve':
						alert('账号准备期，请联系管理员转正~', '~');
						break;
					case 'user_banned':
						alert('该账号被封禁，请联系管理员~', '出错啦~');
						break;
					case 'is_member': // index.php
						location.replace('./');
						break;
					case 'is_customer': // customer.php
						location.replace('customer.php');
						break;
					case 'both': // who.php
						location.replace('who.php');
						break;
					case 'user_inexistent':
						capImgLogin.style.backgroundImage = 'url(capshow.php?a=0&t=' + Date.now() + ')';
						alert('<span style="color:red">账号</span>不对哦', '出错啦~');
						loginInputCapt.value = '';
						break;
					case 'user_duplicates':
						capImgLogin.style.backgroundImage = 'url(capshow.php?a=0&t=' + Date.now() + ')';
						alert('<span style="color:red">数据库错误！</span>', '出错啦~');
						break;
					case 'pass_wrong':
						capImgLogin.style.backgroundImage = 'url(capshow.php?a=0&t=' + Date.now() + ')';
						loginInputCapt.value = '';
						alert('<span style="color:red">密码</span>有错哦', '出错啦~');
						break;
					case 'capt_wrong':
						capImgLogin.style.backgroundImage = 'url(capshow.php?a=0&t=' + Date.now() + ')';
						loginInputCapt.value = '';
						alert('<span style="color:red">验证码</span>有错哦', '出错啦~');
						break;
					case 'ok':
						alert('登录成功!', '好哦~');
						// remove btnLogins
						document.querySelector(".btn-login").remove();
						document.querySelector(".btn-reg").remove();
						// remove 2 popups
						document.querySelector('.reg-popup').remove();
						// loginPopup unshow and delete
						document.querySelector(".login-popup").classList.remove('show');
						setTimeout(() => {
							document.querySelector(".login-popup").remove();
						}, 250);
						// assemble welcome
						let btnWelcome = document.createElement('div');
						btnWelcome.classList.add('btn-auth', 'btn-welcome');
						let spanGreeting = document.createElement('span');
						spanGreeting.classList.add('greeting');
						spanGreeting.innerHTML = '欢迎回来';
						let brWelcome = document.createElement('br');
						let spanName = document.createElement('span');
						spanName.classList.add('call-name');
						spanName.innerHTML = res.username;
						btnWelcome.append(spanGreeting, brWelcome, spanName);
						// assemble logout
						let btnLogout = document.createElement('div');
						btnLogout.classList.add('btn-logout');
						btnLogout.innerHTML = '退 出';
						// bind event
						btnLogout.addEventListener('click', logoutClick);
						// clear input
						document.querySelectorAll('login-input').forEach(function(){
							this.value = '';
						});
						// 组装“后台管理”
						let btnAdmin = document.createElement('div');
						btnAdmin.classList.add('btn-auth', 'btn-admin');
						btnAdmin.innerHTML = '后台管理';
						btnAdmin.addEventListener('click', goAdmin);
						// append welcome/logout/admin to header
						header.append(btnWelcome, btnLogout, btnAdmin);
						break;
					default:
						window.location.replace('https://www.mps.gov.cn/');
						break;
				}
			}
		});
		objLogin = {
			"user": loginUserVal,
			"pass": loginPassVal,
			"capt": loginCaptVal
		};
		xhrLogin.send(JSON.stringify(objLogin));
	}
}
if (btnLogout){
	btnLogout.addEventListener('click', logoutClick);
}
function logoutClick(){
	let xhrLogout = GetXmlHttpObject();
	xhrLogout.open('get', 'ajax/logout.php', true);
	xhrLogout.onload = function(){
		let res = null;
		try{
			res = JSON.parse(xhrLogout.response);
		}
		catch(err){
			console.log(err);
			return;
		}
		// console.log(res);
		if (res.flag == 0){
			location.replace('./');
			return;
		}
		else if (res.flag == 1){
			return;
		}
	}
	xhrLogout.send(null);
}
function goAdmin(){
	window.location.href = ('admin_route.php');
}
if (regSubmit){
	regSubmit.addEventListener('click', regSubmitDo);
}
function regSubmitDo(){
	let regPopup = document.querySelector('.reg-popup');
	let regUserVal = document.querySelector('.reg-input-user').value;
	let regPassVal = document.querySelector('.reg-input-pass').value;
	let regQqVal = document.querySelector('.reg-input-qq').value;
	let regCaptVal = document.querySelector('.reg-input-capt').value;
	if (regUserVal == '' || regPassVal == '' || regQqVal == '' || regCaptVal == ''){
		alert('四个都要填一下哦', '出错啦~');
		return;
	}
	else if (regUserVal.length < 5){
		alert('用户名最短5位哦', '出错啦~');
		return;
	}
	else if (regUserVal.length > 16){
		alert('用户名最长16位哦', '出错啦~');
		return;
	}
	else if (regPassVal.length < 6){
		alert('密码最短6位哦', '出错啦~');
		return;
	}
	else if (regPassVal.length > 32){
		alert('密码最长32位哦', regPopup);
		return;
	}
	else if (regQqVal.length < 5){
		alert('QQ最短5位哦 (小马自己就是10000)', '出错啦~');
		return;
	}
	else if (regQqVal.length > 15){
		alert('QQ最长15位哦', '出错啦~');
		return;
	}
	else if (regCaptVal.length != 5){
		alert('验证码应该是5位哦', '出错啦~');
		return;
	}
	else{
		let xhrReg = GetXmlHttpObject();
		xhrReg.open('post', 'ajax/reg.php', true);
		xhrReg.setRequestHeader('content-type','application/json');
		xhrReg.addEventListener('readystatechange', function(){
			if (xhrReg.readyState == 4 && xhrReg.status == 200){
				let res = null
				try{
					res = JSON.parse(xhrReg.response);
				}
				catch(err){
					console.log(err);
					return;
				}
				// console.log(res);return;
				let capImgReg = document.querySelector('.captcha-show-reg');
				switch (res.code){
					case 'input_not_all_filled':
					case 'user_too_short':
					case 'user_too_long':
					case 'user_invalid_char':
					case 'pass_too_short':
					case 'pass_too_long':
					case 'pass_invalid_char':
					case 'qq_too_short':
					case 'qq_too_long':
					case 'qq_invalid_fomat':
					case 'qq_invalid_char':
					case 'capt_length_error':
					case 'capt_invalid_char':
					case 'get_the_fuck_off':
						window.location.replace('https://www.mps.gov.cn/');
						break;
					case 'is_customer':
						setTimeout(() => {
							location.replace('./customer.php');
						}, 3000);
						break;
					case 'is_member':
						setTimeout(() => {
							location.replace('./')
						}, 3000);
						alert('请不要捣乱哦！', '出错啦~');
						break;
					case 'both':
						setTimeout(() => {
							location.replace('./who.php');
						}, 3000);
						alert('请先确定您的身份', '有点小问题~');
						break;
					case 'capt_wrong':
						capImgReg.style.backgroundImage = 'url(capshow.php?a=1&t=' + Date.now() + ')';
						alert('验证码输错了哦', '出错啦~');
						regInputCapt.value = '';
						break;
					case 'user_exists':
						capImgReg.style.backgroundImage = 'url(capshow.php?a=1&t=' + Date.now() + ')';
						alert('账号名已注册，请换一个哦亲', '出错啦~');
						regInputUser.value = '';
						regInputCapt.value = '';
						break;
					case 'user_duplicates':
						capImgReg.style.backgroundImage = 'url(capshow.php?a=1&t=' + Date.now() + ')';
						alert('服务器出错，请反馈管理员', '出错啦~');
						regInputUser.value = '';
						regInputCapt.value = '';
						break;
					case 'user_create_error':
						capImgReg.style.backgroundImage = 'url(capshow.php?a=1&t=' + Date.now() + ')';
						alert('糟糕，服务器出大事了，请反馈管理员', '出错啦~');
						regInputUser.value = '';
						regInputCapt.value = '';
						break;
					case 'qq_exists':
						capImgReg.style.backgroundImage = 'url(capshow.php?a=1&t=' + Date.now() + ')';
						alert('注册 QQ 号已存在，请更换一个新的', '有问题哦~');
						break;
					case 'qq_duplicates':
						capImgReg.style.backgroundImage = 'url(capshow.php?a=1&t=' + Date.now() + ')';
						alert('数据库 QQ 号出错，请联系管理员反馈', '出错了哦~');
						break;
					case 'ok':
						alert('恭喜您，注册成功!', '出错啦~')
						// remove btnLogins
						document.querySelector(".btn-login").remove();
						document.querySelector(".btn-reg").remove();
						// remove 2 popups
						document.querySelector('.login-popup').remove();
						document.querySelector('.reg-popup').classList.remove('show');
						setTimeout(() => {
							document.querySelector('.reg-popup').remove();	
						}, 250);
						// assemble welcome
						let btnWelcome = document.createElement('div');
						btnWelcome.classList.add('btn-auth', 'btn-welcome');
						let spanGreeting = document.createElement('span');
						spanGreeting.classList.add('greeting');
						spanGreeting.innerHTML = '欢迎回来';
						let brWelcome = document.createElement('br');
						let spanName = document.createElement('span');
						spanName.classList.add('call-name');
						spanName.innerHTML = regUserVal;
						btnWelcome.append(spanGreeting, brWelcome, spanName);
						// assemble logout
						let btnLogout = document.createElement('div');
						btnLogout.classList.add('btn-logout');
						btnLogout.innerHTML = '退 出';
						btnLogout.addEventListener('click', logoutClick);
						// assemble admin
						let btnAdmin = document.createElement('div');
						btnAdmin.classList.add('btn-auth', 'btn-admin');
						btnAdmin.innerHTML = '后台管理';
						btnAdmin.addEventListener('click', goAdmin);
						// append
						header.append(btnWelcome, btnLogout, btnAdmin);
						break;
					default:
						break;
				}
			}
		});
		let objReg = {
			"user": regUserVal,
			"pass": regPassVal,
			"qq": regQqVal,
			"capt": regCaptVal
		};
		xhrReg.send(JSON.stringify(objReg));
	}
}
// oninput
if (regInputQq){
	regInputQq.addEventListener('input', qqFilter);
}
function qqFilter(){
	regInputQq.value = regInputQq.value.replace(/[\D]/g, '');
	// 粘贴0开始的字符串未处理，若处罚会直接弹走（到公安部官网）
}

// util
function GetXmlHttpObject(){
	var xmlHttpRequest = null;
	try{
		xmlHttpRequest = new XMLHttpRequest();
	}
	catch (e){
		// Internet Explorer
		try{
			xmlHttpRequest=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e){
			xmlHttpRequest=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttpRequest;
}
window.alert = function(
	msg,
	title = '消息',
	btnOk = () => {},
	parent = document.body,
	width = 'fit-content',
	height = 'fit-content',
	timeout = 5000
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