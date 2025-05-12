// frame
const header = document.querySelector('.header');
const left = document.querySelector('.left');
const menus = document.querySelectorAll('.menu');
const right = document.querySelector('.right');
const tabWraps = document.querySelectorAll('.tab-wrap');
const tabOrder = document.querySelector('.tab-order');
const tabQr = document.querySelector('.tab-qr');
const tabUpload = document.querySelector('.tab-upload');
const tabDoc = document.querySelector('.tab-doc');
const tabMember = document.querySelector('.tab-member');
const tabSettle = document.querySelector('.tab-settle');
const tabMe = document.querySelector('.tab-me');
const tabLog = document.querySelector('.tab-log');
const filterGrid = document.querySelector('.filter-grid');
const pageGrid = document.querySelector('.page-grid');
const btnReset = document.querySelector('.btn-reset');
var btnResetCD = null;
var btnResetTimer = null;
const btnConfirm = document.querySelector('.btn-confirm');
var btnConfirmValid = null;
const btnExit = document.querySelector('.btn-exit');
// fee rate
const inputFeeRates = document.querySelectorAll('.input-fee-rate');
const btnChangeFeeRates = document.querySelectorAll('.btn-change-fee-rate');
const globalFeeRates = [];
// const btnService = document.querySelector('.btn-service');
const userBalWrap = document.querySelector('.user-bal-wrap');
const userBalTitlebar = document.querySelector('.user-bal-titlebar');
const btnUserBalQuery = document.querySelector('.btn-user-bal-query');
// 自定义
const userBalResOriginSubmitCustom = document.querySelector('.user-bal-res-origin-submit-custom');
const userBalResOriginPayedCustom = document.querySelector('.user-bal-res-origin-payed-custom');
const userBalResActualSubmitCustom = document.querySelector('.user-bal-res-actual-submit-custom');
const userBalResActualPayedCustom = document.querySelector('.user-bal-res-actual-payed-custom');
// 预定义
const userBalResOriginSubmitToday = document.querySelector('.user-bal-res-origin-submit-today');
const userBalResOriginPayedToday = document.querySelector('.user-bal-res-origin-payed-today');
const userBalResActualSubmitToday = document.querySelector('.user-bal-res-actual-submit-today');
const userBalResActualPayedToday = document.querySelector('.user-bal-res-actual-payed-today');
const userBalResOriginSubmitTomonth = document.querySelector('.user-bal-res-origin-submit-tomonth');
const userBalResOriginPayedTomonth = document.querySelector('.user-bal-res-origin-payed-tomonth');
const userBalResActualSubmitTomonth = document.querySelector('.user-bal-res-actual-submit-tomonth');
const userBalResActualPayedTomonth = document.querySelector('.user-bal-res-actual-payed-tomonth');
const userBalResOriginSubmitRecentD3 = document.querySelector('.user-bal-res-origin-submit-recent-d3');
const userBalResOriginPayedRecentD3 = document.querySelector('.user-bal-res-origin-payed-recent-d3');
const userBalResActualSubmitRecentD3 = document.querySelector('.user-bal-res-actual-submit-recent-d3');
const userBalResActualPayedRecentD3 = document.querySelector('.user-bal-res-actual-payed-recent-d3');
const userBalResOriginSubmitRecentD7 = document.querySelector('.user-bal-res-origin-submit-recent-d7');
const userBalResOriginPayedRecentD7 = document.querySelector('.user-bal-res-origin-payed-recent-d7');
const userBalResActualSubmitRecentD7 = document.querySelector('.user-bal-res-actual-submit-recent-d7');
const userBalResActualPayedRecentD7 = document.querySelector('.user-bal-res-actual-payed-recent-d7');
const userBalResOriginSubmitRecentD30 = document.querySelector('.user-bal-res-origin-submit-recent-d30');
const userBalResOriginPayedRecentD30 = document.querySelector('.user-bal-res-origin-payed-recent-d30');
const userBalResActualSubmitRecentD30 = document.querySelector('.user-bal-res-actual-submit-recent-d30');
const userBalResActualPayedRecentD30 = document.querySelector('.user-bal-res-actual-payed-recent-d30');
const userBalResOriginSubmitRecentHyear = document.querySelector('.user-bal-res-origin-submit-recent-hyear');
const userBalResOriginPayedRecentHyear = document.querySelector('.user-bal-res-origin-payed-recent-hyear');
const userBalResActualSubmitRecentHyear = document.querySelector('.user-bal-res-actual-submit-recent-hyear');
const userBalResActualPayedRecentHyear = document.querySelector('.user-bal-res-actual-payed-recent-hyear');
const userBalResOriginSubmitAll = document.querySelector('.user-bal-res-origin-submit-all');
const userBalResOriginPayedAll = document.querySelector('.user-bal-res-origin-payed-all');
const userBalResActualSubmitAll = document.querySelector('.user-bal-res-actual-submit-all');
const userBalResActualPayedAll = document.querySelector('.user-bal-res-actual-payed-all');
const userBalBtnMinimize = document.querySelector('.user-bal-btn-minimize');
const userBalBtnMaximize = document.querySelector('.user-bal-btn-maximize');
const userBalBtnClose = document.querySelector('.user-bal-btn-close');
const userBalBody = document.querySelector('.user-bal-body');
const inputDateStart = document.querySelector('.input-date-start');
const inputDateEnd = document.querySelector('.input-date-end');
// settle
const memberCellDataPids = document.querySelectorAll('.member-cell-data-pid');
const balanceFronts = document.querySelectorAll('.balance-front');
const btnSettleResets = document.querySelectorAll('.btn-settle-reset');
const inputSettles = document.querySelectorAll('.input-settle');
const btnSettleMaxes = document.querySelectorAll('.btn-settle-max');
const btnSettles = document.querySelectorAll('.btn-settle');
const balanceFrontValues = [];
const btnSettleResetAll = document.querySelector('.btn-settle-reset-all');
const btnSettleMaxAll = document.querySelector('.btn-settle-max-all');
const btnSettleAll = document.querySelector('.btn-settle-all')

const btnMemberCDs = [...btnSettleMaxes, ...btnSettles, btnSettleMaxAll, btnSettleAll];

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
	memberEventListen();
	// save menu-item selection
	let menuActive = null;
	if (!(menuActive = localStorage.getItem('active'))){
		localStorage.setItem('active', 'order');
	}
	document.querySelector('.menu-item-' + menuActive).classList.add('active');
	document.querySelector('.tab-' + menuActive).classList.add('show');
	// get balance-front to js
	document.querySelectorAll('.balance-front').forEach((balanceFront, i) => {
		balanceFrontValues[i] = balanceFront.innerHTML;
	});
}
// head area
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
document.querySelectorAll('.menu-item').forEach(menuItem => {
	menuItem.addEventListener('click', function(){
		if (!this.classList.contains('active')){
			document.querySelectorAll('.menu-item').forEach(menuItem => { // deactive all menuItems
				menuItem.classList.remove('active');
			});
			this.classList.add('active'); // active this menuItem
			tabWraps.forEach(tabWrap => {
				tabWrap.classList.remove('show');
			});
			switch (this.id){
			case 'menu-item-order':
				tabOrder.classList.add('show');
				localStorage.setItem('active', 'order');
				break;
			case 'menu-item-qr':
				tabQr.classList.add('show');
				localStorage.setItem('active', 'qr');
				location.reload();
				break;
			case 'menu-item-upload':
				tabUpload.classList.add('show');
				localStorage.setItem('active', 'upload');
				break;
			case 'menu-item-doc':
				tabDoc.classList.add('show');
				localStorage.setItem('active', 'doc');
				break;
			case 'menu-item-member':
				tabMember.classList.add('show');
				localStorage.setItem('active', 'member');
				location.reload();
				break;
			case 'menu-item-settle':
				tabSettle.classList.add('show');
				localStorage.setItem('active', 'settle');
				location.reload();
				break;
			case 'menu-item-me':
				tabMe.classList.add('show');
				localStorage.setItem('active', 'me');
				break;
			case 'menu-item-log':
				tabLog.classList.add('show');
				localStorage.setItem('active', 'log');
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
	let menuItemDocs = document.querySelector('.menu-item-doc');
	menuItemDocs.parentElement.querySelector('.menu').classList.add('active');
	menuItemDocs.classList.add('active');
	document.querySelectorAll('.tab-wrap').forEach(tabWrap => {
		tabWrap.classList.remove('show');
	});
	document.querySelector('.tab-doc').classList.add('show');
}
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
			alert('订单号<span class="text-red">只能</span>包含 <span class="text-red">A</span>-<span class="text-red">Z</span>、<span class="text-red">a</span>-<span class="text-red">z</span>、<span class="text-red">0</span>-<span class="text-red">9</span> 和 <span class="text-red">_</span> 哦~', '请注意~', ()=>{}, 10000);
			return;
		}
	}
	let xhrSearch = GetXmlHttpObject();
	xhrSearch.open('post', 'ajax/filter_search.php', true);
	xhrSearch.setRequestHeader("content-type", "application/json");
	xhrSearch.onload = function(){
		let res = null;
		try{
			res = JSON.parse(xhrSearch.response);
		}
		catch(err){
			return;
		}
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
	if (e.keyCode == 13){
		doSearch();
	}
});
// order area
function orderEventListen(){
	let oids = document.querySelectorAll('.order-cell-oid');
	document.querySelectorAll('.do-pay').forEach(sendPay);
	function sendPay(btnPay, i){
		btnPay.addEventListener('click', function(){
			let xhrPay = GetXmlHttpObject();
			xhrPay.open('get', 'ajax/pay_handler.php?oid=' + oids[i].innerHTML, true);
			xhrPay.onload = function(){
				let res = null;
				try{
					res = JSON.parse(xhrPay.response)
				}
				catch(err){
					console.log(err);
					return;
				}
				switch(res.flag){
				case 7:
					alert('数据库内部错误，请反馈管理员', '出错了哦~', function(){return;});
					return;
				case 6:
					setTimeout(() => {
						location.replace('https://www.bing.com/');
					}, 5000);
					alert('检测到未登录攻击行为', '入侵报警~', function(){location.replace('https://www.bing.com/')})
					return;
				case 5:
					alert('数据库写入错误，请反馈管理员', '出错了哦~');
					return;
				case 4:
					alert('此订单已经支付', '出错了~');
					return;
				case 3:
					setTimeout(() => {
						location.replace('https://www.bing.com/');
					}, 5000);
					alert('无权执行此操作', '入侵报警~', function(){location.replace('https://www.bing.com/')})
					return;
				case 2:
					setTimeout(() => {
						location.replace('./')
					}, 5000);
					alert('请先登录', '出错了~', function(){location.replace('./');});
					return;
				case 1:
					setTimeout(() => {
						location.replace('https://www.bing.com/');
					}, 5000);
					alert('检测到攻击行为', '入侵报警~', function(){location.replace('https://www.bing.com/');});
					return;
				case 0:
					alert('设置成功', '操作成功~', function(){}, 30000);
					let payStatus = document.querySelectorAll('.pay-status')[i];
					payStatus.classList.remove('status-no');
					payStatus.classList.add('status-yes');
					payStatus.innerHTML = '已支付';
					let doPoy = document.querySelectorAll('.do-pay')[i];
					doPoy.classList.add('payed');
					doPoy.innerHTML = '已付';
					let payTime = document.querySelectorAll('.pay-time')[i];
					payTime.innerHTML = res.time;
					let payer = document.querySelectorAll('.payer')[i];
					payer.innerHTML = res.payer;
					return;
				}
			};
			xhrPay.send(null);
		});
	}
	document.querySelectorAll('.do-notify').forEach(sendNotify);
	function sendNotify(btnNotify, i){
		btnNotify.addEventListener('click', function(){
			let xhrNotify = GetXmlHttpObject();
			xhrNotify.open('get', 'ajax/notify_handler.php?oid=' + oids[i].innerHTML, true);
			xhrNotify.onload = function(){
				let res = null;
				try{
					res = JSON.parse(xhrNotify.response)
				}
				catch(err){
					return;
				}
				if (res.flag == 9){
					alert('该订单尚未完成支付。 <br>请先设置订单支付状态为已支付', '出错了哦~');
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
			xhrNotify.send(null);
		});
	}
	document.querySelectorAll('.btn-order-page').forEach(switchPage);
	function switchPage(btnOrderPage, i){
		btnOrderPage.addEventListener('click', function(){
			if (btnOrderPage.classList.contains('disabled')){
				return;
			}
			let xhrPage = GetXmlHttpObject();
			t = new Date().getTime();
			xhrPage.open('get', 'ajax/order_page.php?t=' + t + '&p=' + i, true);
			xhrPage.onload = function(){
				let res = null
				try{
					res = JSON.parse(xhrPage.response);
				}
				catch(err){
					console.log(err);
					return;
				}
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
	}
}//End of orderEventListen
function memberEventListen(){
	let pids = document.querySelectorAll('.member-cell-data-pid');
	let usernames = document.querySelectorAll('.member-cell-data-username');
	document.querySelectorAll('.btn-balance-popup').forEach(balanceDetailClick); // btn-balance-popup
	function balanceDetailClick(btnBalShow, i){
		btnBalShow.addEventListener('click', function(){
			if (this.classList.contains('active')){
				this.classList.remove('active');
				userBalWrap.classList.remove('show');
			}
			else{
				userBalResOriginSubmitCustom.innerHTML = '-';
				userBalResOriginPayedCustom.innerHTML = '-';
				document.querySelectorAll('.btn-balance-popup').forEach((btnEach) => {
					btnEach.classList.remove('active');
				});
				this.classList.add('active');
				userBalWrap.classList.add('show');
				userBalTitlebar.innerHTML = usernames[i].innerHTML;
				let xhrBalShow = GetXmlHttpObject();
				xhrBalShow.open('get', 'ajax/bal_preset.php?pid=' + pids[i].innerHTML, true);
				xhrBalShow.onload = function(){
					let res = null;
					try{
						res = JSON.parse(xhrBalShow.response);
					}
					catch(err){
						console.log(err);
						return;
					}
					let flag = res.flag;
					if (flag == 0){
						userBalResOriginSubmitToday.innerHTML = fmtMoney(res.today_submit_origin);
						userBalResOriginPayedToday.innerHTML = fmtMoney(res.today_payed_origin);
						userBalResActualSubmitToday.innerHTML = fmtMoney(res.today_submit_actual);
						userBalResActualPayedToday.innerHTML = fmtMoney(res.today_payed_actual);
						userBalResOriginSubmitTomonth.innerHTML = fmtMoney(res.tomonth_submit_origin);
						userBalResOriginPayedTomonth.innerHTML = fmtMoney(res.tomonth_payed_origin);
						userBalResActualSubmitTomonth.innerHTML = fmtMoney(res.tomonth_submit_actual);
						userBalResActualPayedTomonth.innerHTML = fmtMoney(res.tomonth_payed_actual);
						userBalResOriginSubmitRecentD3.innerHTML = fmtMoney(res.d3_submit_origin);
						userBalResOriginPayedRecentD3.innerHTML = fmtMoney(res.d3_payed_origin);
						userBalResActualSubmitRecentD3.innerHTML = fmtMoney(res.d3_submit_actual);
						userBalResActualPayedRecentD3.innerHTML = fmtMoney(res.d3_payed_actual);
						userBalResOriginSubmitRecentD7.innerHTML = fmtMoney(res.d7_submit_origin);
						userBalResOriginPayedRecentD7.innerHTML = fmtMoney(res.d7_payed_origin);
						userBalResActualSubmitRecentD7.innerHTML = fmtMoney(res.d7_submit_actual);
						userBalResActualPayedRecentD7.innerHTML = fmtMoney(res.d7_payed_actual);
						userBalResOriginSubmitRecentD30.innerHTML = fmtMoney(res.d30_submit_origin);
						userBalResOriginPayedRecentD30.innerHTML = fmtMoney(res.d30_payed_origin);
						userBalResActualSubmitRecentD30.innerHTML = fmtMoney(res.d30_submit_actual);
						userBalResActualPayedRecentD30.innerHTML = fmtMoney(res.d30_payed_actual);
						userBalResOriginSubmitRecentHyear.innerHTML = fmtMoney(res.hyear_submit_origin);
						userBalResOriginPayedRecentHyear.innerHTML = fmtMoney(res.hyear_payed_origin);
						userBalResActualSubmitRecentHyear.innerHTML = fmtMoney(res.hyear_submit_actual);
						userBalResActualPayedRecentHyear.innerHTML = fmtMoney(res.hyear_payed_actual);
						userBalResOriginSubmitAll.innerHTML = fmtMoney(res.all_submit_origin);
						userBalResOriginPayedAll.innerHTML = fmtMoney(res.all_payed_origin);
						userBalResActualSubmitAll.innerHTML = fmtMoney(res.all_submit_actual);
						userBalResActualPayedAll.innerHTML = fmtMoney(res.all_payed_actual);
					}
					else if (flag == 1){
						alert('出错拉~', '参数形式错误，不要乱来哦~');
					}
					else if (flag == 2){
						alert('出错啦~', '无此操作权限哦~')
					}
				}
				xhrBalShow.send(null);
			}
		});
	}
	userBalTitlebar.addEventListener('mousedown', userBalTitlebarMousedown); // move userBalWrap
	function userBalTitlebarMousedown(eDown){
		let x = eDown.pageX - userBalWrap.offsetLeft;
		let y = eDown.pageY - userBalWrap.offsetTop;
		let leftMargin = left.offsetWidth;
		let topMargin = header.offsetHeight;
		document.onmousemove = userBalWrapMove;
		function userBalWrapMove(eMove){
			if (eMove.pageX < x + leftMargin){ // follow mouse move
				userBalWrap.style.left = leftMargin + 'px';
			}
			else if (eMove.pageX >= window.innerWidth - userBalWrap.offsetWidth + x){
				userBalWrap.style.left = window.innerWidth - userBalWrap.offsetWidth + 'px';
			}
			else{
				userBalWrap.style.left = (eMove.pageX - x) + 'px';
			}
			if (eMove.pageY < y + topMargin){
				userBalWrap.style.top = topMargin + 'px';
			}
			else if (eMove.pageY >= window.innerHeight - userBalWrap.offsetHeight + y){
				userBalWrap.style.top = window.innerHeight - userBalWrap.offsetHeight + 'px';
			}
			else{
				userBalWrap.style.top = (eMove.pageY - y) + 'px';	
			}
		}
		document.onmouseup = function(){
			document.onmousemove = null;
		}
	}
	userBalBtnMinimize.addEventListener('click', btnBalMinimizeClick);
	function btnBalMinimizeClick(){
		userBalBody.classList.add('hidden');
		userBalBtnClose.style.borderBottomRightRadius = '7px';
	}
	userBalBtnMaximize.addEventListener('click', btnBalMaximizeClick);
	function btnBalMaximizeClick(){
		userBalBody.classList.remove('hidden');
		userBalBtnClose.style.borderBottomRightRadius = '3px';
	}
	userBalBtnClose.addEventListener('click', btnBalCloseClick);
	function btnBalCloseClick(){
		userBalWrap.classList.remove('show');
		document.querySelectorAll('.btn-balance-popup').forEach(btnEach => {
			btnEach.classList.remove('active');
		});
	}
	btnUserBalQuery.addEventListener('click', userBalQueryClick);
	function userBalQueryClick(){
		let timeStart = inputDateStart.value;
		let timeEnd = inputDateEnd.value;
		let username = userBalTitlebar.innerHTML;
		let xhrBalQuery = GetXmlHttpObject();
		xhrBalQuery.open('post', 'ajax/bal_custom.php');
		xhrBalQuery.setRequestHeader("content-type", "application/json");
		xhrBalQuery.onload = function(){
			if (xhrBalQuery.status == 200){
				let res = null;
				try{
					res = JSON.parse(xhrBalQuery.response);
				}
				catch(err){
					console.log(err);
					return;
				}
				if (!res){
					alert('没有数据', '暂时没有数据哦，需要至少先发起一个订单');
				}
				let flag = res.flag;
				if (flag == 0){
					userBalResOriginSubmitCustom.innerHTML = fmtMoney(res.custom_submit_origin);
					userBalResOriginPayedCustom.innerHTML = fmtMoney(res.custom_payed_origin);
					userBalResActualSubmitCustom.innerHTML = fmtMoney(res.custom_submit_actual);
					userBalResActualPayedCustom.innerHTML = fmtMoney(res.custom_payed_actual);
				}
				else if (flag == 1){
					alert('出错拉~', '提交的格式有问题，不要乱来哦');
				}
				else if (flag == 2){
					alert('出错拉~', '您无权执行此操作<br>如果你是管理员，请联系程序员', ()=>{}, 10000);
				}
				else if (flag == 3){
					alert('出错啦~', '提交的值有问题，不要乱来哦');
				}
				else if (flag == 4){
					alert('出错拉~', '数据库出错，请联系管理员反馈', ()=>{}, 5000);
				}
			}
		}
		let objBalQuery = {
			"timeStart": timeStart,
			"timeEnd": timeEnd,
			"username": username
		};
		xhrBalQuery.send(JSON.stringify(objBalQuery));
	}
	// member-change-fee-rate
	inputFeeRates.forEach((inputFeeRate, iInputRate) => {
		globalFeeRates[iInputRate] = inputFeeRate.value;
		bindDecimalInput(inputFeeRate);
	});
	btnChangeFeeRates.forEach((btnChangeFeeRate, iChangeRate) => {
		btnChangeFeeRate.addEventListener('click', function(){
			let newFeeRate = parseFloat(inputFeeRates[iChangeRate].value);
			let oldFeeRate = parseFloat(globalFeeRates[iChangeRate]);
			if (newFeeRate == oldFeeRate){
				alert('费率没有改变', '出错啦~');
				return;
			}
			if (newFeeRate > 100 || newFeeRate <= 0){
				alert('费率只能是在 0% 到 100% 之间<br>且不能是 0%，不能是 100%', '出错啦~');
				return;
			}
			let xhrChangeRate = getXmlHttpObject();
			xhrChangeRate.open('post', 'ajax/change_fee.php');
			xhrChangeRate.setRequestHeader('content-type', 'application/json');
			xhrChangeRate.onload = function(){
				if (xhrChangeRate.status == 200){
					let res = null;
					try{
						res = JSON.parse(xhrChangeRate.response);
					}
					catch(err){
						console.log(err);
						return;
					}
					let flag = res.flag;
					switch(flag){
					case 1:
						alert('身份错误', '出错啦~');
						break;
					case 2:
						alert('输入内容非法', '出错啦~');
						break;
					case 3:
						alert('输入参数形式错误', '出错啦~');
						break;
					case 4:
						alert('输入参数值非法', '出错啦~');
						break;
					case 5:
						alert('输入参数 PID 不存在', '出错啦~');
						break;
					case 6:
						alert('后台数据库严重出错，您用的是盗版？<br>有这个必要么？开源的啊！', '出错啦~');
						break;
					case 7:
						alert('新费率没有变化', '出错啦~');
						break;
					case 8:
						alert('能看到这条提示，说明你是一个比较纯粹的逆向<br>怎么说呢，开心就好吧，你随意~<br>尽你最大努力就好，学到东西就是收获', '出错啦~');
						break;
					case 9:
						alert('数据库错误<br>反馈管理员，扣技术的工资', '出错啦~');
						break;
					case 0:
						// this 是 xhrChangeRate
						alert('修改成功！', '成功啦~', function(){
							setTimeout(() => { // "this" inputFeeRate
								inputFeeRates[iChangeRate].classList.remove('text-red');
							}, 1000);
							setTimeout(() => { // "this" inputSettle
								inputSettles[iChangeRate].classList.remove('text-red');
							}, 1000);
						});
						setTimeout(() => { // "this" inputFeeRate
							inputFeeRates[iChangeRate].classList.remove('text-red');
						}, 6000);
						setTimeout(() => { // "this" inputSettle
							inputSettles[iChangeRate].classList.remove('text-red');
						}, 6000);
						btnChangeFeeRate.classList.add('disabled');
						let resp_new_fee_rate = numberFormat(res.new_fee_rate * 100, 2, '.', '');
						globalFeeRates[iChangeRate] = resp_new_fee_rate.toString();
						inputFeeRates[iChangeRate].value = resp_new_fee_rate;
						inputFeeRates[iChangeRate].classList.add('text-red');
						let frontValue = balanceFronts[iChangeRate].innerHTML;
						let newSettleValue = resp_new_fee_rate;
						inputSettles[iChangeRate].value = numberFormat(frontValue * (1 - (newSettleValue / 100)), 2, '.', '');
						inputSettles[iChangeRate].classList.add('text-red');
					}
				}
			}
			let pid = memberCellDataPids[iChangeRate].innerHTML;
			let new_fee_rate = numberFormat(inputFeeRates[iChangeRate].value / 100, 4, '.', '') ;
			let objChangeRate = {
				pid: pid,
				new_fee_rate: new_fee_rate
			};
			xhrChangeRate.send(JSON.stringify(objChangeRate));
		});
	});
	// member-settle
	btnSettleResets.forEach((btnSettleReset, iReset) => { // settle-reset
		btnSettleReset.addEventListener('click', function(){
			inputSettles[iReset].value = '0.00';
			inputSettles[iReset].classList.add('text-red');
			setTimeout(() => {
				inputSettles[iReset].classList.remove('text-red');
			}, 1000);
		});
	});
	inputSettles.forEach(function(inputSettle, i){
		inputSettle.addEventListener('input', function(){
			const oldValue = this.value;
			const caret = this.selectionStart;
			// 初步清理非法字符（仅保留数字和点）
			let cleaned = oldValue.replace(/[^\d.]/g, '');
			// 只允许一个小数点
			const parts = cleaned.split('.');
			if (parts.length > 2) {
				cleaned = parts[0] + '.' + parts[1]; // 丢弃多余部分
			}
			// 整数部分处理
			if (cleaned.includes('.')) {
				const [intPart, decimalPartRaw] = cleaned.split('.');
				const intFixed = intPart === '' ? '0' : String(Number(intPart)); // 防止空字符串
				const decimalPart = decimalPartRaw.slice(0, 2); // 最多两位小数
				cleaned = intFixed + '.' + decimalPart;
			}
			else {
				// 没有小数点时，只允许 '0' 或非零开头整数
				cleaned = cleaned.replace(/^0+(?=\d)/, ''); // 去除前导零（除非是单独的 0）
			}
			// 恢复光标位置
			const offset = cleaned.length - oldValue.length;
			this.value = cleaned;
			this.setSelectionRange(caret + offset, caret + offset);
		});
		inputSettle.addEventListener('blur', function(){
			if (parseFloat(this.value) == 0 && this.value != '0.00'){
				this.value = '0.00';
			}
			if (!this.value.includes('.')){
				this.value += '.00';
			}
			else{
				let [intPart, fraPart] = this.value.split('.');
				fraPart = (fraPart + '00').slice(0, 2);
				this.value = `${intPart}.${fraPart}`;
			}
		});
	});
	btnSettleMaxes.forEach((btnSettleMax, iMax) => {
		btnSettleMax.addEventListener('click', function(){
			btnMemberCDs.forEach(btnCD => {
				btnCD.classList.add('disabled');
				setTimeout(() => {
					btnCD.classList.remove('disabled');
				}, 1500);
			});
			let pid = document.querySelectorAll('.member-cell-data-pid')[iMax].innerHTML;
			let xhrSettleMax = GetXmlHttpObject();
			xhrSettleMax.open('post', 'ajax/settle_max.php');
			xhrSettleMax.setRequestHeader("content-type", "application/json");
			xhrSettleMax.onload = function(){
				if (xhrSettleMax.status == 200){
					let res = null;
					try{
						res = JSON.parse(xhrSettleMax.response);
					}
					catch(err){
						console.log(err);
						return;
					}
					console.log(res)
					let flag = res.flag;
					let balanceBack = res.balance_back;
					switch(flag){
					case 1:
						alert('权限错误', '出错啦~');
						setTimeout(() => {
							location.replace('./');
						}, 5000);
						return;
					case 2:
						alert('输入参数有错', '出错啦~');
						setTimeout(() => {
							location.replace('./');
						}, 5000);
						return;
					case 3:
						alert('参数值不合理', '出错啦~');
						setTimeout(() => {
							location.replace('./');
						}, 5000);
						return;
					case 4:
						alert('商户后台余额小于显示值<br>点击确定更新为最新值', '请注意~', () => {
							let balanceFront = document.querySelectorAll('.balance-front')[iMax];
							balanceFront.innerHTML = balanceBack;
							balanceFront.classList.add('text-red');
							setTimeout(() => {
								balanceFront.classList.remove('text-red');
							}, 1000);
							let inputSettle = document.querySelectorAll('.input-settle')[iMax];
							inputSettle.value = balanceBack;
							inputSettle.classList.add('text-red');
							setTimeout(() => {
								inputSettle.classList.remove('text-red');
							}, 1000);
						}, 30000);
						return;
					case 5:
						alert('商户后台余额有增加<br>点击确定更新为最新值', '请注意~', () => {
							balanceFronts[iMax].innerHTML = balanceBack;
							balanceFronts[iMax].classList.add('text-red');
							setTimeout(() => {
								balanceFronts[iMax].classList.remove('text-red');
							}, 1000);
							inputSettles[iMax].value = balanceBack;
							inputSettles[iMax].classList.add('text-red');
							setTimeout(() => {
								inputSettles[iMax].classList.remove('text-red');
							}, 1000);
						}, 30000);
						return;
					case 0:
						if (!nearlyEqual(inputSettles[iMax].value, balanceBack, 0.01)){
							inputSettles[iMax].value = balanceBack;
							inputSettles[iMax].classList.add('text-red');
							setTimeout(() => {
								inputSettles[iMax].classList.remove('text-red');
							}, 1000);
							return;
						}
						inputSettles[iMax].classList.add('text-green');
						setTimeout(() => {
							inputSettles[iMax].classList.remove('text-green');
						}, 1000);
						return;
					default:
						return;
					}
				}
			}
			let objSettleMax = {
				'pid': pid,
				'balance_front': balanceFrontValues[iMax]
			};
			xhrSettleMax.send(JSON.stringify(objSettleMax));
		});
	});
	btnSettles.forEach((btnSettle, iSettle) => {
		btnSettle.addEventListener('click', function(){
			let pid = memberCellDataPids[iSettle].innerHTML;
			let balanceFront = balanceFronts[iSettle];
			let inputSettle = inputSettles[iSettle];
			let inputFeeRate = inputFeeRates[iSettle];
			let fBalanceFront = parseFloat(balanceFront.innerHTML);
			let fInputSettle = parseFloat(inputSettle.value);
			let fInputFeeRate = parseFloat(inputFeeRate.value);
			if (fInputSettle >= 1000000){
				alert('每次提现金额不能大于 100W<br>P.S 此设置可以修改', '警告~');
				return;
			}
			else if (fInputSettle == 0){
				setTimeout(() => {
					inputSettle.classList.remove('text');
				}, 15000);
				inputSettles[iSettle].classList.remove('text');
				alert('<span class="text-red">结算金额</span>不能是 <span class="text-orange">0</span><br>P.S 可以是<span class="text-red">正数</span>: 正常结算<br>P.S 可以是<span class="text-red">负数</span>: 增加余额', '<span class="text-orange">警告~</span>', () => {}, 30000);
				return;
			}
			let xhrSettle = GetXmlHttpObject();
			xhrSettle.open('post', 'ajax/settle.php');
			xhrSettle.setRequestHeader("content-type", "application/json");
			xhrSettle.onload = function(){
				if (xhrSettle.status == 200){
					let res = null;
					try{
						res = JSON.parse(xhrSettle.response);
					}
					catch(err){
						console.log(err);
						return;
					}
					res = JSON.parse(xhrSettle.response);
					const flag = res.flag;
					switch(flag){
					case 1:
						return;
					case 2:
						return;
					case 3:
						return;
					case 4:
						return;
					case 5:
						return;	
					case 0:
						const new_balance = res.new_balance;
						balanceFront.innerHTML = new_balance;
						inputSettle.value = new_balance;
						balanceFront.classList.add('text-red');
							inputSettle.classList.add('text-red');
						setTimeout(() => {
							balanceFront.classList.remove('text-red');
							inputSettle.classList.remove('text-red');
						}, 1000);
						return;
					}
				}
			}
			let objSettle = {
				pid: pid,
				balance_front: numberFormat(balanceFronts[iSettle].innerHTML, 2, '.', ''),
				input_settle: numberFormat(inputSettles[iSettle].value, 2, '.', '')
			};
			// console.log(JSON.stringify(objSettle));
			xhrSettle.send(JSON.stringify(objSettle));
		})
	});
	// btn-all
	btnSettleResetAll.addEventListener('click', function(){
		inputSettles.forEach(inputSettle => {
			inputSettle.value = '0.00';
			inputSettle.classList.add('text-red');
			setTimeout(() => {
				inputSettle.classList.remove('text-red');
			}, 1000);
		});
	});
	btnSettleMaxAll.addEventListener('click', function(){
		let obj = [];
		memberCellDataPids.forEach((elemPid, iMerch) => {
			obj.push({
				pid: elemPid.innerHTML,
				balance_front: balanceFronts[iMerch].innerHTML,
				input_settle: inputSettles[iMerch].value
			});
		});
		let xhrSettleMaxAll = getXmlHttpObject();
		xhrSettleMaxAll.open('post', 'ajax/settle_max_all.php');
		xhrSettleMaxAll.setRequestHeader('content-type', 'application/json');
		xhrSettleMaxAll.onload = function(){
			let res = null;
			try{
				res = JSON.parse(xhrSettleMaxAll.response);
			}
			catch(err){
				console.log(res);
				return;
			}
			console.log(res)
			let flag = res.flag;
			switch(flag){
			case 1:
				alert('身份错误', '出错啦~');
				setTimeout(() => {
					location.replace('./');
				}, 5000);
				return;
			case 2:
				alert('输入内容错误', '出错啦~');
				setTimeout(() => {
					location.replace('./');
				}, 5000);
				return;
			case 3:
				alert('输入值格式不规范', '出错啦~');
				setTimeout(() => {
					location.replace('./');
				}, 5000);
				return;
			case 0:
				let data = res.data;
				let isVary = false;
				memberCellDataPids.forEach((elemPid, iMerch) => {
					let balanceFront = balanceFronts[iMerch];
					let inputSettle = inputSettles[iMerch];
					if (parseFloat(data[iMerch].balance) != parseFloat(balanceFront.innerHTML)){
						isVary = true;
					}
					balanceFront.innerHTML = data[iMerch].balance;
					inputSettle.value = data[iMerch].balance;
				});
				let elemNums = [...balanceFronts, ...inputSettles];
				if (isVary){
					elemNums.forEach(elemNum => {
						elemNum.classList.add('text-red');
						setTimeout(() => {
							elemNum.classList.remove('text-red');
						}, 6000);
					});
					alert('余额有变动<br>已将前端余额修改为最新余额', '通知~', function(){
						elemNums.forEach(elemNum => {
							elemNum.classList.add('text-red');
							setTimeout(() => {
								elemNum.classList.remove('text-red');
							}, 1000);
						});
					});
					return;
				}
				else{
					elemNums.forEach(elemNum => {
						elemNum.classList.add('text-green');
						setTimeout(() => {
							elemNum.classList.remove('text-green');
						}, 2000);
					});
				}
			}
		}
		xhrSettleMaxAll.send(JSON.stringify(obj));
	});
	btnSettleAll.addEventListener('click', function(){
		alert('暂未开放', '通知~');
	});
}
// disable mouseright
document.oncontextmenu = function(e){
	e.preventDefault();
}
// util
function GetXmlHttpObject(){
	var xmlHttpRequest = null;
	try{
		xmlHttpRequest = new XMLHttpRequest();
	}
	catch (e){
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
	btnPopupOk.innerHTML = '确定';
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
function fmtMoney(money){
	money = parseInt(money);
	if (money >= 0 && money < 10){
		return '0.' + '<span style="font-size:12px;color:rgba(255,255,255,0.6);">0' + money + '</span>';
	}
	if (money >= 10 && money < 100){
		return '0.' + '<span style="font-size:12px;color:rgba(255,255,255,0.6);">' + money + '</span>';
	}
	money = money.toString();
	let intPart = money.substring(0, money.length - 2);
	let frgPart = money.substring(money.length - 2);
	return intPart + '.' + '<span style="font-size:12px;color:rgba(255,255,255,0.6);">' + frgPart + '</span>';
}
function numberFormat(number, decimals = 0, decPoint = '.', thousandsSep = ',') {
	const parts = Number(number).toFixed(decimals).split('.');
	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);
	return parts.join(decPoint);
}
function bindDecimalInput(inputEl, userOptions = {}) {
  const defaultOptions = {
    decimalDigits: 2,
    padZerosOnBlur: true,
    max: null,
    min: null,
    allowNegative: false,
  };
  const options = { ...defaultOptions, ...userOptions };
  inputEl.addEventListener('input', function () {
    const oldValue = this.value;
    const caret = this.selectionStart;
    const regex = options.allowNegative ? /[^\d.\-]/g : /[^\d.]/g;
    let cleaned = oldValue.replace(regex, '');
    if (options.allowNegative) {
      const negative = cleaned.startsWith('-');
      cleaned = cleaned.replace(/-/g, '');
      if (negative) cleaned = '-' + cleaned;
    }
    const parts = cleaned.split('.');
    if (parts.length > 2) {
      cleaned = parts[0] + '.' + parts[1];
    }
    if (cleaned.includes('.')) {
      let [intPart, decimalPart] = cleaned.split('.');
      intPart = intPart === '' || intPart === '-' ? '0' : String(Number(intPart));
      decimalPart = decimalPart.slice(0, options.decimalDigits);
      cleaned = intPart + '.' + decimalPart;
      if (options.allowNegative && oldValue.startsWith('-')) cleaned = '-' + cleaned;
    }
		else {
      cleaned = cleaned.replace(/^0+(?=\d)/, '');
    }
    const offset = cleaned.length - oldValue.length;
		if (cleaned > 100.00){
			cleaned = '100.00';
		}
		if (cleaned < 0){
			cleaned = '0.00';
		}
    this.value = cleaned;
		if (this.classList.contains('input-fee-rate')){
			let feeRateList = Array.from(inputFeeRates);
			let i = feeRateList.indexOf(this);
			if (parseFloat(this.value) == globalFeeRates[i]){
				btnChangeFeeRates[i].classList.add('disabled');
			}
			else{
				btnChangeFeeRates[i].classList.remove('disabled');
			}
		}
    this.setSelectionRange(caret + offset, caret + offset);
  });
  inputEl.addEventListener('blur', function () {
    let val = this.value;
    if (!val){
			return;
		}
    if (val === '.' || val === '-.' || val === '-0.') {
      val = options.allowNegative && val.startsWith('-') ? '-0.00' : '0.00';
    }
    if (!val.includes('.')) {
      val += '.' + '0'.repeat(options.decimalDigits);
    }
		else {
      let [intPart, decimalPart] = val.split('.');
      decimalPart = (decimalPart + '0'.repeat(options.decimalDigits)).slice(0, options.decimalDigits);
      val = intPart + '.' + decimalPart;
    }
    let num = parseFloat(val);
    if (!isNaN(num)) {
      if (options.max !== null && num > options.max) num = options.max;
      if (options.min !== null && num < options.min) num = options.min;
      val = num.toFixed(options.decimalDigits);
    }
    this.value = val;
  });
}
/**
 * 判断两个浮点数是否在容错百分比内相等
 * @param {number} a - 第一个数
 * @param {number} b - 第二个数
 * @param {number} percent - 容错百分比（例如 0.01 表示 1%）
 * @returns {boolean} 是否近似相等
 */
function nearlyEqual(a, b, percent) {
	const absA = Math.abs(a);
	const absB = Math.abs(b);
	const diff = Math.abs(a - b);
	// 特殊情况：两个都是 0，认为相等
	if (a === b) return true;
	// 一个是 0，另一个不是，不能用百分比判断
	if (a === 0 || b === 0 || (absA + absB < Number.EPSILON)) {
			return diff < percent;
	}
	// 相对误差判断
	return diff / Math.max(absA, absB) <= percent;
}
function getXmlHttpObject(){
	var xhr = null;
	try{
		xhr = new XMLHttpRequest();
	}
	catch(e){
		try{
			xhr = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xhr;
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
});
// dev


