var chatWrap = document.querySelector('.chat-wrap');
var chatShow = document.querySelector('.chat-show')
var chatInput = document.querySelector('.chat-input');
var btnExit = document.querySelector('.btn-exit');
var btnImg = document.querySelector('.btn-img');
var btnSend = document.querySelector('.btn-send');
var timerGetRegular = null;
var last_time = null;
var sendReady = true; // send frequency limit
var initDone = false;
var timerInitDone = null; // loop to check if init get done
var getReady = true; // when getting regular check if receive done
function rank2Name(rank){
	switch (rank){
		case 0:
			return '官方';
		case -1:
			return '勇士';
	}
}
function time2normal(time = +new Date()){
	time = time.substring(0, 13);
	var date = new Date(parseInt(time) + 8 * 3600 * 1000); // 增加8小时
	return date.toJSON().substring(0, 19).replace('T', ' ');
}
function iSay(content, type, user, rank, timestamp, avatar){
	// 0
	let msgRank = document.createElement('div');
	msgRank.classList.add('msg-rank');
	msgRank.innerText = '【' + rank2Name(rank) + '】';
	let msgName = document.createElement('div');
	msgName.classList.add('msg-name');
	msgName.innerText = user;
	let msgTime = document.createElement('div');
	msgTime.classList.add('msg-time');
	msgTime.innerText = time2normal(timestamp);
	let msgContent = document.createElement('div');
	msgContent.classList.add('msg-content', 'msg-content-self');
	if (type == 'text'){
		msgContent.innerHTML = content;
	}
	else if (type == 'image'){
		let imgContent = document.createElement('img');
		imgContent.classList.add('img-content');
		imgContent.src = content;
		msgContent.appendChild(imgContent);
	}
	// 1
	let msgHeadWrap = document.createElement('div');
	msgHeadWrap.classList.add('msg-head-wrap', 'msg-head-wrap-self');
	let msgBubbleWrap = document.createElement('div');
	msgBubbleWrap.classList.add('msg-bubble-wrap', 'msg-bubble-wrap-self');
	// 0->1
	msgHeadWrap.appendChild(msgRank);
	msgHeadWrap.appendChild(msgName);
	msgHeadWrap.appendChild(msgTime);
	msgBubbleWrap.appendChild(msgContent);
	// 2
	let msgAvatarWrap = document.createElement('div');
	msgAvatarWrap.classList.add('msg-avatar-wrap', 'msg-avatar-wrap-self')
	msgAvatarWrap.style.backgroundImage = 'url(img/avatar/' + avatar + ')';
	let msgMainWrap = document.createElement('div');
	msgMainWrap.classList.add('msg-main-wrap', 'msg-main-wrap-self')
	// 1->2
	msgMainWrap.appendChild(msgHeadWrap);
	msgMainWrap.appendChild(msgBubbleWrap);
	// 3
	let chatLine = document.createElement('div');
	chatLine.classList.add('chat-line', 'chat-line-self');
	chatLine.appendChild(msgAvatarWrap);
	chatLine.appendChild(msgMainWrap);
	// 3->show
	chatShow.appendChild(chatLine);
}
function youSay(content, type, user, rank, timestamp, avatar){
	// 0
	let msgRank = document.createElement('div');
	msgRank.classList.add('msg-rank');
	msgRank.innerText = '【' + rank2Name(rank) + '】';
	let msgName = document.createElement('div');
	msgName.classList.add('msg-name');
	msgName.innerText = user;
	let msgTime = document.createElement('div');
	msgTime.classList.add('msg-time');
	msgTime.innerText = time2normal(timestamp);
	let msgContent = document.createElement('div');
	msgContent.classList.add('msg-content', 'msg-content-oppo');
	if (type == 'text'){
		msgContent.innerHTML = content;
	}
	else if (type == 'image'){
		let imgContent = document.createElement('img');
		imgContent.classList.add('img-content');
		imgContent.src = content;
		msgContent.appendChild(imgContent);
	}
	// 1
	let msgHeadWrap = document.createElement('div');
	msgHeadWrap.classList.add('msg-head-wrap', 'msg-head-wrap-oppo');
	let msgBubbleWrap = document.createElement('div');
	msgBubbleWrap.classList.add('msg-bubble-wrap', 'msg-bubble-wrap-oppo');
	// 0->1
	msgHeadWrap.appendChild(msgRank);
	msgHeadWrap.appendChild(msgName);
	msgHeadWrap.appendChild(msgTime);
	msgBubbleWrap.appendChild(msgContent);
	// 2
	let msgAvatarWrap = document.createElement('div');
	msgAvatarWrap.classList.add('msg-avatar-wrap', 'msg-avatar-wrap-oppo')
	msgAvatarWrap.style.backgroundImage = 'url(img/avatar/' + avatar + ')';
	let msgMainWrap = document.createElement('div');
	msgMainWrap.classList.add('msg-main-wrap', 'msg-main-wrap-oppo')
	// 1->2
	msgMainWrap.appendChild(msgHeadWrap);
	msgMainWrap.appendChild(msgBubbleWrap);
	// 3
	let chatLine = document.createElement('div');
	chatLine.classList.add('chat-line', 'chat-line-oppo');
	chatLine.appendChild(msgAvatarWrap);
	chatLine.appendChild(msgMainWrap);
	// 3->show
	chatShow.appendChild(chatLine);
}
window.addEventListener('paste', async (e) => {
	btnSend.classList.add('disabled');
	setTimeout(() => {
		btnSend.classList.remove('disabled');
		sendReady = true;
	}, 2000);
	if (sendReady == false){
		alert('太快了，请慢一点');
		return;
	}
	let file = null;
	let items = (e.clipboardData || window.clipboardData).items;
	if (items && items.length){
		for (let i = 0; i < items.length; i++){
			if (items[i].type.indexOf('image') !== -1){
				file = items[i].getAsFile();
				let fr = new FileReader();
				fr.readAsDataURL(file);
				fr.onload = function(eFr){
					let xhrPutPic = GetXmlHttpObject();
					let tPutPic = new Date().getTime();
					xhrPutPic.open('post', 'ajax/msg_handler.php?t=' + tPutPic, true);
					xhrPutPic.setRequestHeader("content-type", "application/json");
					xhrPutPic.setRequestHeader("cache-Control", "no-cache");
					xhrPutPic.setRequestHeader("pragma", "no-cache");
					xhrPutPic.addEventListener('readystatechange', () => {
						if (xhrPutPic.readyState == 4 && xhrPutPic.status == 200){
							getReady = true;
							let res = null;
							try{
								res = JSON.parse(xhrPutPic.response);
							}
							catch(err){
								return;
							}
							if (resPic.flag){
								// 发送成功
							}
							else{
								// 发送失败
							}
						}
					});
					let objPutPic = {
						"action": "put",
						"type": "image",
						"content": eFr.target.result
					};
					xhrPutPic.send(JSON.stringify(objPutPic));
					getReady = false;
				}
				break;
			}
		}
	}
});
chatInput.addEventListener('keypress', (e) => {
	if (e.ctrlKey && e.keyCode == 10){
	// if (e.keyCode == 13){
		e.preventDefault();
		textSend();
	}
});
btnExit.addEventListener('click', doExit);
function doExit(){
	location.replace('logout.php');
}
btnImg.addEventListener('change', function(){
	if (!this.files || !this.files[0]){
		return;
	}
	let reader = new FileReader();
	reader.readAsDataURL(this.files[0]);
	reader.onload = function(e){
		let picData = e.target.result;
		let time = new Date().getTime();
		let xhrSelImg = GetXmlHttpObject();
		xhrSelImg.open('post', 'ajax/msg_handler.php?t=' + time, true);
		xhrSelImg.setRequestHeader("content-type", "application/json");
		xhrSelImg.setRequestHeader("cache-Control", "no-cache");
		xhrSelImg.setRequestHeader("pragma", "no-cache");
		xhrSelImg.addEventListener('readystatechange', () => {
			if (xhrSelImg.readyState == 4 && xhrSelImg.status == 200){
				getReady = true;
				let res = null;
				try{
					res = JSON.parse(xhrSelImg);
				}
				catch(err){
					return;
				}
				if (res.flag){
					// 发送成功
					console.log('图片发送成功');
				}
				else{
					// 发送失败
					console.log('图片发送失败');
				}
			}
		});
		let objSelImg = {
			"action": "put",
			"type": "image",
			"content": picData
		};
		xhrSelImg.send(JSON.stringify(objSelImg));
		getReady = false;
	}
});
btnSend.addEventListener('click', textSend);
function textSend(){
	btnSend.classList.add('disabled');
	setTimeout(() => {
		btnSend.classList.remove('disabled');
		sendReady = true;
	}, 2000);
	if (sendReady == false){
		alert('太快了，请慢一点');
		return;
	}
	if (chatInput.innerHTML == "") {
		alert("消息不能为空哦亲");
		return;
	}
	let xhrPutText = GetXmlHttpObject();
	let tPut = new Date().getTime();
	xhrPutText.open("post", "ajax/msg_handler.php?t=" + tPut, true);
	xhrPutText.setRequestHeader("content-type", "application/json");
	xhrPutText.setRequestHeader("cache-Control", "no-cache");
	xhrPutText.setRequestHeader("pragma", "no-cache");
	xhrPutText.addEventListener('readystatechange', () => {
		if (xhrPutText.readyState == 4 && xhrPutText.status == 200){
			if (xhrPutText.responseText == ''){
				return;
			}
			let res = JSON.parse(xhrPutText.response);
			if (res.flag == true){
				console.log('Text sent OK!');
			}
		}		
	}); // put need not scroll
	let content = chatInput.innerText;
	let obj = {
		"action": "put",
		"type": "text",
		"content": content
	};
	xhrPutText.send(JSON.stringify(obj));
	chatInput.innerHTML = '';
	sendReady = false;
}
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
window.onload = function(){
	// USER GET INIT
	let xhrGetInit = GetXmlHttpObject();
	xhrGetInit.open('post', 'ajax/msg_handler.php', true);
	xhrGetInit.setRequestHeader("content-type", "application/json");
	xhrGetInit.setRequestHeader("cache-Control", "no-cache");
	xhrGetInit.setRequestHeader("pragma", "no-cache");
	xhrGetInit.onreadystatechange = function(){
		if (xhrGetInit.readyState == 4 && xhrGetInit.status == 200){
			getReady = true;
			let resInit = null;
			try{
				resInit = JSON.parse(xhrGetInit.response);
			}
			catch(err){
				return;
			}
			if (resInit.flag == 1){ // both
				location.replace('../who.php');
				return;
			}
			if (resInit.flag == 2){ // neither
				location.replace('../');
				return;
			}
			if (resInit.flag == 3){ // is_merchant && side >= 10
				location.replace('../merch.php');
				return;
			}
			if (resInit.flag == 4){ // INPUT ATK
				alert('INPUT data error');
				setTimeout(() => {
					location.replace('https://www.bing.com/');
				}, 3000);
				return;
			}
			if (resInit.flag == 5){ // ACTION ERROR
				alert('Action error');
				setTimeout(() => {
					location.replace('https://www.bing.com/');
				}, 3000);
				return;
			}
			// cust
			for (let key in resInit){
				let value = resInit[key];
				let type = value.type;
				let content = value.content;
				let user = value.who;
				let rank = value.rank;
				let timestamp = key;
				let avatar = value.avatar;
				if (rank == -1){
					iSay(content, type, user, rank, timestamp, avatar);
				}
				else if (rank == 0){
					youSay(content, type, user, rank, timestamp, avatar);
				}
				setTimeout(() => {
					chatShow.scrollTop = chatShow.scrollHeight;	
				}, 50);
				last_time = key;
			}
			initDone = true;
		}
	}
	let objGetInit = {
		"action": "get",
		"is_init": true
	};
	xhrGetInit.send(JSON.stringify(objGetInit));
	getReady = false;
	// HeartBeat
	timerInitDone = setInterval(() => {
		if (!initDone){
			return;
		}
		clearInterval(timerInitDone);
		timerInitDone = null;
		startRegular(1000, 86400);
	}, 500);
	
}
/**
 * 启动到服务器获取新信息的定时器。默认每秒取一次，总共取 3600 次（1 小时）
 * @param {number} interval 每次到服务器获取新信息的间隔毫秒数 (整数，默认值: 1000)。
 * @param {number} times 执行多少次 (整数，默认值: 3600)。
 */
function startRegular(interval = 1000, times = 3600){
	timerGetRegular = setInterval(() => {
		// console.log(times);
		if (times <= 0){
			clearInterval(timerGetRegular);
			return;
		}
		if (!getReady){
			return;
		}
		// console.log(times)
		let xhrGetRegular = GetXmlHttpObject();
		let tGet = new Date().getTime();
		xhrGetRegular.open('post', 'ajax/msg_handler.php?t=' + tGet, true);
		xhrGetRegular.setRequestHeader("content-type", "application/json");
		xhrGetRegular.setRequestHeader("cache-Control", "no-cache");
		xhrGetRegular.setRequestHeader("pragma", "no-cache");
		xhrGetRegular.onreadystatechange = function(){
			if (xhrGetRegular.readyState == 4 && xhrGetRegular.status == 200){
				getReady = true;
				let resRegular = null;
				try{
					resRegular = JSON.parse(xhrGetRegular.response);
				}
				catch(err){
					return;
				}
				// console.log(resRegular);return;
				if (resRegular.flag == 0){
					return; // 无新消息，无操作，返回
				}
				if (resRegular.flag == 1){ // both
					location.replace('../who.php');
					return;
				}
				if (resRegular.flag == 2){ // neither
					location.replace('../');
					return;
				}
				if (resRegular.flag == 3){ // is_merchant && side >= 10
					location.replace('../merch.php');
					return;
				}
				if (resRegular.flag == 4){ // INPUT ATK
					location.replace('https://www.bing.com/');
					return;
				}
				if (resRegular.flag == 5){ // ACTION ERROR
					location.replace('https://www.bing.com/');
					return;
				}
				if (resRegular.flag == 6){
					alert('数据库错误，请联系管理员反馈', '出错了哦~');
					return;
				}
				for (let key in resRegular){
					let value = resRegular[key];
					let type = value.type;
					let content = value.content;
					let user = value.who;
					let rank = value.rank;
					let timestamp = key;
					let avatar = value.avatar;
					if (rank == -1){
						iSay(content, type, user, rank, timestamp, avatar);
					}
					else if (rank == 0){
						youSay(content, type, user, rank, timestamp, avatar);
					}
					setTimeout(() => {
						chatShow.scrollTop = chatShow.scrollHeight;	
					}, 10);
					last_time = key;
				}
			}
		}
		let objGetRegular = {
			"action": "get",
			"is_init": false,
			"last_time": last_time
		};
		xhrGetRegular.send(JSON.stringify(objGetRegular));
		getReady = false;
		times--;
	}, interval);
}
// alert
window.alert = function(
	msg,
	title = '消息',
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
