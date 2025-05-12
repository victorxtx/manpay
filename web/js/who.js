var btnMerch = document.querySelector('.btn-merch');
var btnCust = document.querySelector('.btn-cust');
var btnNone = document.querySelector('.btn-none');
btnMerch.addEventListener('click', function(){ // 登出玩家，进入merch
	let xhrCustOut = GetXmlHttpObject();
	xhrCustOut.open('get', 'ajax/logout_either.php?who=1');
	xhrCustOut.addEventListener('readystatechange', function(){
		if (xhrCustOut.readyState == 4 && xhrCustOut.status == 200){
			res = xhrCustOut.response;
			if (res == 1 || res == 2 || res == 3){
				location.replace('https://www.bing.com');
			}
			else{
				location.replace('merch.php');
			}
		}
	});
	xhrCustOut.send(null);
});
btnCust.addEventListener('click', function(){ // 登出商户，进入cust
	let xhrMerchOut = GetXmlHttpObject();
	xhrMerchOut.open('get', 'ajax/logout_either.php?who=0');
	xhrMerchOut.addEventListener('readystatechange', function(){
		if (xhrMerchOut.readyState == 4 && xhrMerchOut.status == 200){
			res = xhrMerchOut.response;
			if (res == 1 || res == 2 || res == 3){
				location.replace('https://www.bing.com');
			}
			else{
				location.replace('customer.php');
			}
		}
	});
	xhrMerchOut.send(null);
});
btnNone.addEventListener('click', function(){ // 全部登出，进入 index
	let xhrNone = GetXmlHttpObject();
	xhrNone.open('get', 'ajax/logout_either.php?who=2');
	xhrMerchOut.addEventListener('readystatechange', function(){
		if (xhrMerchOut.readyState == 4 && xhrMerchOut.status == 200){
			res = xhrMerchOut.response;
			if (res == 1 || res == 2 || res == 3){
				location.replace('https://www.bing.com');
			}
			else{
				location.replace('./');
			}
		}
	});
});
//common
function GetXmlHttpObject(){
	var xmlHttp = null;
	try{
		xmlHttp = new XMLHttpRequest();
	}
	catch(e){
		try{
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
window.alert = function(
	msg,
	title = '成功',
	parent = document.body,
	width = 'fit-content',
	height = 'fit-content',
	timeout = 5000000
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