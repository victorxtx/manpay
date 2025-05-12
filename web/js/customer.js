var btnDiscard = document.querySelector('.btn-discard');
var btnChat = document.querySelector('.btn-chat');
btnDiscard.addEventListener('click', function(){
	let xhrDiscard = GetXmlHttpObject();
	let t = new Date().getTime();
	xhrDiscard.open('get', 'ajax/logout.php?t=' + t, true);
	xhrDiscard.setRequestHeader("cache-Control", "no-cache");
	xhrDiscard.setRequestHeader("pragma", "no-cache");
	xhrDiscard.onreadystatechange = function(){
		if (xhrDiscard.readyState == 4 && xhrDiscard.status == 200){
			let res = null;
			try{
				res = JSON.parse(xhrDiscard.response);
			}
			catch(err){
				return;
			}
			console.log(res);
			if (res.flag == 2){
				setTimeout(() => {
					location.replace('./');
				}, 3000);
				alert('出错', '出错啦~', function(){location.replace('./');});
			}
			else if (res.flag == 1){
				setTimeout(() => {
					location.replace('https://www.bing.com');
				}, 3000);
				alert('出错', '出错啦~', function(){location.replace('./');});
			}
			else if (res.flag == 0){
				setTimeout(() => {
					location.replace('./');
				}, 3000);
				alert('操作成功！再会。', '成功~', function(){location.replace('./');});
			}
		}
	}
	xhrDiscard.send(null);
});
btnChat.addEventListener('click', function(){
	location.replace('ch/chat.php');
});
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
	btnGo,
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
		btnGo();
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