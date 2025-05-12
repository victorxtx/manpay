var header = document.querySelector('.header');
var left = document.querySelector('.left');
var right = document.querySelector('.right');
var tabWraps = document.querySelectorAll('.tab-wrap');
var tabOrder = document.querySelector('.tab-order');
var tabDoc = document.querySelector('.tab-doc');
var tabMe =  document.querySelector('.tab-me');
var filterGrid = document.querySelector('.filter-grid');
var btnReset = document.querySelector('.btn-reset');
var btnResetCD = null;
var btnResetTimer = null;
var btnConfirm = document.querySelector('.btn-confirm');
var btnConfirmValid = null;
var btnExit = document.querySelector('.btn-exit');
// ui menu
window.onload = function(){
	let xhrKeyCD = GetXmlHttpObject();
	xhrKeyCD.open('get', 'ajax/key_last.php', true);
	xhrKeyCD.addEventListener('readystatechange', function(){
		if (xhrKeyCD.readyState == 4 && xhrKeyCD.status == 200){
			let res = JSON.parse(xhrKeyCD.response);
			if (res.flag == 0){
				btnResetCD = res.time_diff;
				if (res.time_diff > 0){
					btnReset.classList.add('disabled');
					btnResetTimer = setInterval(function(){
						if (btnResetCD <= 0){
							btnReset.classList.remove('disabled');
							clearInterval(btnResetTimer);
						}
						btnResetCD--;
					}, 1000)
				}
			}
			else if (res.flag == 1){
				location.replace('.');
			}
		}
	});
	xhrKeyCD.send();
	orderEventListen();
	let menuActive = null;
	if (!(menuActive = localStorage.getItem('active'))){
		localStorage.setItem('active', 'order');
	}
	document.querySelector('.menu-item-' + menuActive).classList.add('active');
	document.querySelector('.tab-' + menuActive).classList.add('show')
}
// ui menuItem
document.querySelectorAll('.menu-item').forEach(menuItem => {
	menuItem.addEventListener('click', function(){
		if (!this.classList.contains('active')){
			document.querySelectorAll('.menu-item').forEach(menuItem => { // all menuItems
				menuItem.classList.remove('active');
			});
			this.classList.add('active'); // this menuItem
			tabWraps.forEach(tabWrap => {
				tabWrap.classList.remove('show');
			});
			switch (this.id){
			case 'menu-item-order':
				tabOrder.classList.add('show');
				localStorage.setItem('active', 'order');
				break;
			case 'menu-item-doc':
				tabDoc.classList.add('show');
				localStorage.setItem('active', 'doc');
				break;
			case 'menu-item-me':
				tabMe.classList.add('show');
				localStorage.setItem('active', 'me');
				break;
			default:
				break;
			}
		}
	});
});
document.querySelector('.btn-view-doc').addEventListener('click', viewDoc);
function viewDoc(){
	document.querySelectorAll('.menu').forEach(menu => {
		menu.classList.remove('active');
	});
	document.querySelectorAll('.menu-item').forEach(menuItem => {
		menuItem.classList.remove('active');
	});
	let menuItemDocs = document.querySelector('.menu-item-docs');
	menuItemDocs.parentElement.querySelector('.menu').classList.add('active');
	menuItemDocs.classList.add('active');
	document.querySelectorAll('.tab-wrap').forEach(tabWrap => {
		tabWrap.classList.remove('show');
	});
	document.querySelector('.tab-doc').classList.add('show');
}
btnExit.addEventListener('click', function(){
	let xhrExit = GetXmlHttpObject();
	xhrExit.open('get', 'ajax/logout.php', true);
	xhrExit.addEventListener('readystatechange', function(){
		if (xhrExit.readyState == 4 && xhrExit.status == 200){
			location.href = './';
			return;
		}
	});
	xhrExit.send();
});
// api area
btnReset.addEventListener('click', function(){
	if (btnResetCD > 0){
		alert(`重置 key 冷却时间还有<span style="color:rgba(234,90,90,1)"> ${btnResetCD} 秒 </span>`);
		btnReset.classList.add('disabled');
		return;
	}
	btnReset.classList.add('disabled');
	btnResetCD = 300;
	btnResetTimer = setInterval(() => {
		if (btnResetCD <= 0){
			btnReset.classList.remove('disabled');
			clearInterval(btnResetTimer);
		}
		btnResetCD--;
	}, 1000);
	let xhrReset = GetXmlHttpObject();
	xhrReset.open('get', 'ajax/reset_key.php', true);
	xhrReset.addEventListener('readystatechange', function(){
		if (xhrReset.readyState == 4 && xhrReset.status == 200){
			let res = JSON.parse(xhrReset.response);
			if (res.flag == 0){
				alert('更新成功！<br>新 key 已刷新！');
				document.querySelector('.api-info-key').innerHTML = res.key;
				return;
			}
			else if (res.flag == 1){
				location.replace('.');
				return;
			}
			else if (res.flag == 2){
				alert('数据库错误<br>请反馈管理员！');
				return;
			}
			else if (res.flag == 3){
				alert('重置操作冷却中！<br>还剩 <span style="color:rgba(234,90,90,1)">' + res.time + '</span> 秒...')
				return;
			}
			else if (res.flag == 4){
				location.replace('https://www.bing.com/');
			}
		}
	});
	xhrReset.send();
});
if (btnConfirm.classList.contains('disabled')){
	btnConfirmValid = false;
}
else{
	btnConfirmValid = true;
}
btnConfirm.addEventListener('click', function(){
	if (!btnConfirmValid){
		alert('请 <span style="color:rgba(234,90,90,1)">5 秒后 </span>再更改回调方式！');
		return;
	}
	btnConfirmValid = false;
	btnConfirm.classList.add('disabled');
	setTimeout(() => {
		btnConfirm.classList.remove('disabled');
		btnConfirmValid = true;
	}, 5000);
	let method = null;
	document.querySelectorAll('.radio-method').forEach(iMethod => {
		if (iMethod.checked){
			method = iMethod.value;
		}
	});
	let xhrConfirm = GetXmlHttpObject();
	xhrConfirm.open('get', 'ajax/notify_method_change.php?method=' + method, true);
	xhrConfirm.addEventListener('readystatechange', function(){
		if (xhrConfirm.readyState == 4 && xhrConfirm.status == 200){
			let res = JSON.parse(xhrConfirm.response);
			if (res.flag == 0){
				alert('修改通知回调方式<span style="color:rgba(234,90,90,1)">成功</span>！');
				return;
			}
			else if (res.flag == 1){
				location.replace('.');
				return;
			}
			else if (res.flag == 2){
				alert('前后一致，毋需更改');
				return;
			}
			else if (res.flag == 3){
				alert('数据库错误<br>请反馈管理员！');
				return;
			}
		}
	});
	xhrConfirm.send();
});
// filter_area.filter_status
var oldFilterStatus = null;
var currentFilterStatus = null;
var btnFilterConfirm = document.querySelector('.btn-filter-confirm');
var btnFilterSearch = document.querySelector('.btn-filter-search');
Object.values(document.querySelectorAll('.radio-filter-status')).some((radioFilter, i) => { // init
	if (radioFilter.checked){
		oldFilterStatus = i;
		currentFilterStatus = i;
		return true;
	}
	else{
		return false;
	}
});
document.querySelectorAll('.radio-filter-status').forEach((radioFilter, i) => { //init
	radioFilter.addEventListener('change', function(){
		if (i == oldFilterStatus){
			btnFilterConfirm.classList.add('disabled');
		}
		else{
			btnFilterConfirm.classList.remove('disabled');
		}
		currentFilterStatus = i;
	});
});
document.querySelector('.btn-filter-confirm').addEventListener('click', function(){
	if (currentFilterStatus == oldFilterStatus){
		alert('筛选条件没有改变', '出错啦~');
		return;
	}
	let xhrFilterChange = GetXmlHttpObject();
	xhrFilterChange.open('get', 'ajax/filter_change.php?f=' + currentFilterStatus, true);
	xhrFilterChange.onload = function(){
		let res = null;
		try{
			res = JSON.parse(xhrFilterChange.response);
		}
		catch(err){
			return;
		}
		console.log(res);
		switch(res.code){
			case 'id_customer':
				location.replace('customer.php');
				break;
			case 'id_neither':
				location.replace('./');
				break;
			case 'id_both':
				location.replace('who.php');
				break;
			case 'no_arg':
				alert('检测到参数攻击，请自重~', '出错啦~');
				break;
			case 'arg_error':
				alert('检测到参数攻击，请自重~', '出错啦~');
				break;
			case 'value_same':
				alert('检测到绕过攻击，请自重~', '出错啦~');
				break;
			case 'db_error':
				alert('抱歉，数据库出错，请反馈管理员', '出错啦~');
				break;
			case 'ok':
				setTimeout(() => {
					location.reload();
				}, 3000);
				alert('修改成功！', 'YES!', function(){
					location.reload();
				});
				oldFilterStatus = res.filter;
				document.querySelectorAll('.radio-filter-status').forEach((radioFilter, i) => { //init
					radioFilter.addEventListener('change', function(){
						if (i == oldFilterStatus){
							btnFilterConfirm.classList.add('disabled');
						}
						else{
							btnFilterConfirm.classList.remove('disabled');
						}
						currentFilterStatus = i;
					});
				});
				break;
		}
	}
	xhrFilterChange.send(null);
});
// filter_area.search
var allowChars = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9','0','_'];
var searchText = document.querySelector('.search-text');
document.querySelector('.btn-filter-search').addEventListener('click', doSearch);
function doSearch(){
	let textVal = searchText.value;
	if (textVal == ''){
		setTimeout(() => {
			location.reload();
		}, 3000);
		alert('成功清空搜索结果！', '哦耶~', ()=>{location.reload();});
		return;
	}
	if (textVal.length > 32)	{
		alert('订单号最长 32 个字符哦~', '小问题啦~');
		return;
	}
	for (let char of textVal){
		if (!allowChars.includes(char)){
			console.log(char);
			alert('订单号<span class="text-red">只能</span>包含 <span class="text-red">A</span>-<span class="text-red">Z</span>、<span class="text-red">a</span>-<span class="text-red">z</span>、<span class="text-red">0</span>-<span class="text-red">9</span> 和 <span class="text-red">_</span> 哦~', '请注意~', ()=>{}, 10000);
			return;
		}
	}
	let xhrSearch = GetXmlHttpObject();
	xhrSearch.open('post', 'ajax/filter_search.php', true);
	xhrSearch.setRequestHeader("content-type", "application/json");
	xhrSearch.onload = function(){
		// console.log(xhrSearch.response);return;
		let res = null;
		try{
			res = JSON.parse(xhrSearch.response);
		}
		catch(err){
			return;
		}
		console.log(res);
		switch(res.code){
			case 'input_error':
				alert('输入数据格式错误哦~', '出错啦~');
				break;
			case 'too_long':
				alert('订单号过长，自己先去割一下哦~', '出错啦~');
				break;
			case 'forbidden_char':
				alert(`检测到非法字符 ${res.char}，请遵守使用规范，谢谢`, '出错啦~');
				break;
			case 'empty_set':
				alert('没有搜到订单号哦~', '再试试啦~');
				break;
			case 'ok':
				document.querySelector('.order-grid').remove();
				filterGrid.insertAdjacentHTML('afterend', res.html);
				document.querySelector('.page-grid').remove();
				tabOrder.insertAdjacentHTML('beforeend', res.page);
				orderEventListen();
				break;
			case '':
			break;
		}
	}
	let objSearch = {
		"search_string": textVal
	};
	xhrSearch.send(JSON.stringify(objSearch));
};
searchText.addEventListener('keydown', function(e){
	// console.log(e.keyCode);
	if (e.keyCode == 13){
		doSearch();
	}
});
// order-grid area
function orderEventListen(){
	document.querySelectorAll('.do-notify').forEach(sendNotify); // notify
	function sendNotify(btnNotify, i){
		let oids = document.querySelectorAll('.order-cell-oid');
		btnNotify.addEventListener('click', function(){
			let xhrNotify = GetXmlHttpObject();
			xhrNotify.open('get', 'ajax/notify_handler.php?oid=' + oids[i].innerHTML, true);
			xhrNotify.addEventListener('readystatechange', function(){
				if (xhrNotify.readyState == 4 && xhrNotify.status == 200){
					let res = null;
					try{
						res = JSON.parse(xhrNotify.response)
					}
					catch(err){
						return;
					}
					// console.log(res);return;
					if (res.flag == 9){
						alert('该订单暂未完成支付。 <br>请等待订单支付完成<br>或联系管理员', '请稍等...', function(){}, 10000);
						return;
					}
					else if (res.flag == 8){
						alert('后台数据库出错，请反馈管理员', '出错了哦~');
						return;
					}
					else if (res.flag == 7){
						alert('发到后端的数据被篡改~', '出错了哦~', function(){location.replace('./');});
						location.replace('./');
						return;
					}
					else if (res.flag == 6){
						alert('该订单所属商户为封禁状态', '出错了哦~');
						return;
					}
					else if (res.flag == 5){
						alert('本次通知 <span style="color:rgba(244,50,50,1)">未成功</span><br>原因：商户接收通知页面反馈的字符串为 <span style="color:rgba(244,50,50,1)">' + res.data + '</span>，<br><br>* 请输出 <span style="color:rgba(20,184,50)">SUCCESS</span> 以完成通知', '通知失败', ()=>{}, 60000);
						return;
					}
					else if (res.flag == 4){
						alert('商户反馈值类型错误', '出错了哦~');
						return;
					}
					else if (res.flag == 3){
						setTimeout(() => {
							location.replace('./');
						}, 5000);
						alert('无权执行此操作', '出错了哦~', function(){location.replace('./');});
						return;
					}
					else if (res.flag == 2){
						alert('操作者身份错误', '出错了哦~', function(){location.replace('./');});
						location.replace('./');
						return;
					}
					else if (res.flag == 1){
						setTimeout(() => {
							location.replace('./');
						}, 5000);
						alert('检测到参数攻击！', '入侵检测警报~', function(){location.replace('./');});
						return;
					}
					else if (res.flag == 0){
						alert('收到 <span style="color:rgba(20,184,50)">SUCCESS</span><br>通知<span style="color:rgba(20,184,50)">成功</span>！');
						let notifyStatus = document.querySelectorAll('.notify-status')[i];
						notifyStatus.classList.remove('status-no');
						notifyStatus.classList.add('status-yes');
						notifyStatus.innerHTML = '已通知';
						let notifyTime = document.querySelectorAll('.notify-time')[i];
						notifyTime.innerHTML = res.time;
						let notifier = document.querySelectorAll('.notifier')[i];
						notifier.innerHTML = res.notifier;
					}
				}
			});
			xhrNotify.send(null);
		});
	}
	document.querySelectorAll('.btn-order-page').forEach((btnOrderPage, i) => {
		btnOrderPage.addEventListener('click', function(){
			if (btnOrderPage.classList.contains('disabled')){
				console.log(btnOrderPage)
				return;
			}
			let xhrPage = GetXmlHttpObject();
			t = new Date().getTime();
			xhrPage.open('get', 'ajax/order_page.php?t=' + t + '&p=' + i, true);
			xhrPage.onload = function(){
				// console.log(xhrPage.response);return;
				let res = null
				try{
					res = JSON.parse(xhrPage.response);
				}
				catch(err){
					return;
				}
				// console.log(res);return;
				if (res.code == 'ok'){
					document.querySelector('.order-grid').remove();
					filterGrid.insertAdjacentHTML('afterend', res.html);
					document.querySelector('.page-grid').remove();
					tabOrder.insertAdjacentHTML('beforeend', res.page);
					orderEventListen();
				}
				else if (res.code == 'id_customer'){
					setTimeout(() => {
						location.replace('./customer.php');
					}, 3000);
					alert('身份有误', '出错了哦~', () => {location.replace('./customer.php');});
				}
				else if (res.code == 'id_neither'){
					setTimeout(() => {
						location.replace('./');
					}, 3000);
					alert('身份有误', '出错了哦~', () => {location.replace('./');});
				}
				else if (res.code == 'id_both'){
					setTimeout(() => {
						location.replace('./who.php');
					}, 3000);
					alert('身份有误', '出错了哦~', () => {location.replace('./who.php');});
				}
				else if (res.code == 't_error'){
					alert('时间格式错误！', '出错了哦~');
					return;
				}
				else if (res.code == 'p_error'){
					alert('页码错误！', '出错了哦~');
					return;
				}
			}
			xhrPage.send(null);
		});
	});
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
	btnOk = () => {}, // 该函数执行后，会自动关闭
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
