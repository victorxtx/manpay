// doms left
var listWrap = document.querySelector('.list-wrap');
var listHead = document.querySelector('.list-head');
var radioSortNew = document.querySelectorAll('.radio-sort')[0];
var radioSortAlpha = document.querySelectorAll('.radio-sort')[1];
var radioSortStop = document.querySelectorAll('.radio-sort')[2];
var listItemWrap = document.querySelector('.list-item-wrap');
var listItems = [];
var listItemSelects = [];
// doms right
var chatWrap = document.querySelector('.chat-wrap');
var chatHead = document.querySelector('.chat-head');
var inputWrap = document.querySelector('.input-wrap');
var chatInput = document.querySelector('.chat-input');
var btnExit = document.querySelector('.btn-exit');
var btnSend = document.querySelector('.btn-send');
var chatShows = [];
var chatShowActive; // reference chatShow
// control
var timerInitDone = null; // detect if init done
var timerGetRegular = null; // ADMIN GET REGULAR
var arrLastTime = {};
var sendReady = true;//发送冷却bool
var initDone = false;
var getReady = false;
// callback
window.onload = function(){
	// ADMIN GET INIT
	let xhrGetInit = GetXmlHttpObject();
	xhrGetInit.open('post', 'ajax/msg_handler.php', true);
	xhrGetInit.setRequestHeader("content-type", "application/json");
	xhrGetInit.setRequestHeader("cache-control", "no-cache");
	xhrGetInit.setRequestHeader("pragma", "no-cache");
	xhrGetInit.onreadystatechange = function(){
		if (xhrGetInit.readyState == 4 && xhrGetInit.status == 200){
			let res = null;
			try{
				res = JSON.parse(xhrGetInit.response);
			}
			catch(err){
				return;
			}
			// console.log(res)
			if (res.flag == 0){ // 初始化 chatShow
				return;
			}
			if (res.flag == 1){ // both
				location.replace('../who.php');
				return;
			}
			if (res.flag == 2){ // neither
				location.replace('../');
				return;
			}
			if (res.flag == 3){ // is_merchant && side >= 10
				location.replace('../merch.php');
				return;
			}
			if (res.flag == 4){ // INPUT ATK
				setTimeout(() => {
					location.replace('https://www.bing.com/');
				}, timeout);
				alert('检测到攻击数据包', '出错了哦~');
				return;
			}
			if (res.flag == 5){ // ACTION ERROR
				setTimeout(() => {
					location.replace('https://www.bing.com/');	
				}, 3000);
				alert('操作动作出错了哦', '出错了哦~');
				return;
			}
			if (res.flag == 6){
				alert('数据库错误，请联系管理员反馈', '出错了哦~');
				return;
			}
			if (res.flag == 7){
				setTimeout(() => {
					location.replace('https://www.bing.com/');
				}, 3000);
				alert('初始化参数 is_init 出错！', '出错了哦~');
				return;
			}
			for (let user in res){ // console loop
				let dialog = res[user]; // object
				// assemble list
				let objLast = dialog[Object.keys(dialog).pop()]
				let name = user;
				let lastTime = Object.keys(dialog).pop();
				let lastTime13 = lastTime.substring(0,13);
				let lastName = objLast.who;
				let lastMsg = '';
				if (objLast.type == 'text'){
					lastMsg = objLast.content;
				}
				else if (objLast.type == 'image'){
					lastMsg = '[图片]';
				}
				// let avatar = objLast.avatar;
				let avatar = 'client3.png';
				listAppend(name, lastTime13, lastName, lastMsg, avatar);
				// assemble content windows
				let chatShow = document.createElement('div');
				chatShow.classList.add('chat-show');
				chatShow.setAttribute('name', user);
				
				for (let time14 in dialog){ // message loop
					let value = dialog[time14];
					let type = value.type;
					let content = value.content;
					let user = value.who;
					let rank = value.rank;
					let avatar = value.avatar;
					if (rank == -1){
						youSay(content, type, user, rank, time14, avatar, chatShow);
					}
					else if (rank == 0){
						iSay(content, type, user, rank, time14, avatar, chatShow);
					}
				}
				chatWrap.insertBefore(chatShow, inputWrap); // append each chatShow
				setTimeout(() => {
					chatShow.scrollTop = chatShow.scrollHeight;	
				}, 50);
				arrLastTime[user] = lastTime; // update last time for a user
			}
			// listItem post-process init
			let lastLen = Object.keys(arrLastTime).length;
			if (lastLen == 0){
				return;
			}
			else if (lastLen == 1){
				let chatShowOnly = document.querySelector('.chat-show');
				chatShowOnly.style.display = "block";
				document.querySelector('.list-item-select').checked = true;
				let listItemOnly = document.querySelector('.list-item'); // acquire the only listItem
				listItemOnly.style = 'background-image: linear-gradient(135deg, transparent, rgb(214, 244, 214))';
				listItemOnly.firstElementChild.checked = true;
				let userFull = chatShowOnly.getAttribute('name');
				let names = userFull.split(':', 2);
				let merchName = names[0];
				let custName = names[1];
				chatHead.innerHTML = `来自商户 <span class="merchant">${merchName}</span> 的 <span class="customer">${custName}</span>`;
			}
			else{ // if listItem multiple init
				updateListSeqByTime(); // update listItem sequence
				activeChatShowAndCheckListItem(); //actvie the latest (only init)
			}
			initDone = true;
		}
		
	}
	let objGetInit = {
		"action": "get",
		"is_init": true,
	}
	xhrGetInit.send(JSON.stringify(objGetInit));
	getReady = false;
	// start regular
	timerInitDone = setInterval(() => {
		
		if (!initDone){
			return;
		}
		clearInterval(timerInitDone);
		timerInitDone = null;
		startRegular(1000, 86400);
	}, 500);
}
function startRegular(interval = 1000, times = 3600){
	if (timerGetRegular != null){ // prevent 
		return;
	}
	timerGetRegular = setInterval(() => {
		// console.log(times)
		// console.log(getReady);
		if (times <= 0){ // Time up => clear getRegular
			clearInterval(timerGetRegular);
			timerGetRegular = null;
			return;
		}
		// console.log(getReady)
		// if (!getReady){
		// 	return;
		// }
		let xhrGetRegular = GetXmlHttpObject();
		let tGet = new Date().getTime();
		xhrGetRegular.open('post', 'ajax/msg_handler.php?t=' + tGet, true);
		xhrGetRegular.setRequestHeader("cache-Control", "no-cache");
		xhrGetRegular.setRequestHeader("pragma", "no-cache");
		xhrGetRegular.setRequestHeader("expires", "0");
		xhrGetRegular.onreadystatechange = function(){
			if (xhrGetRegular.readyState == 4 && xhrGetRegular.status == 200){
				// console.log(getReady)
				getReady = true;
				
				let res = null;
				try{
					res = JSON.parse(xhrGetRegular.response);
				}
				catch(err){
					return;
				}
				if (res.flag == 0){
					return; // 无新消息，无操作，返回
				}
				if (res.flag == 1){ // both
					setTimeout(() => {
						location.replace('../who.php');	
					}, 3000);
					alert('双重身份，请先确定您的身份', '出错了哦~');
					return;
				}
				if (res.flag == 2){ // neither
					setTimeout(() => {
						location.replace('../');	
					}, 3000);
					alert('闲人免进', '抱歉~');
					return;
				}
				if (res.flag == 3){ // is_merchant && side >= 10
					setTimeout(() => {
						location.replace('../merch.php');	
					}, 3000);
					alert('商家暂不能使用本系统~', '出错了哦~');
					return;
				}
				if (res.flag == 4){ // INPUT ATK
					setTimeout(() => {
						location.replace('https://www.bing.com/');
					}, 3000);
					alert('输入内容格式错误！3 秒后将离开', '出错了哦~');
					return;
				}
				if (res.flag == 5){ // ACTION ERROR
					setTimeout(() => {
						location.replace('https://www.bing.com/');	
					}, 3000);
					alert('动作类型错误！3 秒后将离开', '出错了哦~');
					return;
				}
				if (res.flag == 8){ // last_names more than db_names
					setTimeout(() => {
						location.replace('../customer.php');
					}, 3000);
					alert('操作动作出错了哦', '出错了哦~');
				}
				for (let user in res){ // all users loop
					// msg contents for every certain user
					
					let dialog = res[user];
					if (dialog == null){
						continue;
					}
					// deal listItems
					let objLast = dialog[Object.keys(dialog).pop()]; // last message of one user getInit
					let name = user;
					let lastTime = Object.keys(dialog).pop();
					let lastTime13 = lastTime.substring(0, 13);
					let lastName = objLast.who;
					let lastMsg = '';
					if (objLast.type == 'text'){
						lastMsg = objLast.content;
					}
					else if (objLast.type == 'image'){
						lastMsg = '[图片]';
					}
					// let avatar = objLast.avatar;
					let avatar = 'client3.png';
					if (!arrLastTime.hasOwnProperty(user)){ // user 为新
						listAppend(name, lastTime13, lastName, lastMsg, avatar);
						arrLastTime[user] = lastTime;
						let newChatShow = document.createElement('div');
						newChatShow.classList.add('chat-show');
						newChatShow.setAttribute('name', name);
						chatWrap.insertBefore(newChatShow, inputWrap);
					}
					listItemUpdate(name, lastTime13, lastName, lastMsg, avatar);
					let thisChatShow = document.getElementsByName(user)[0];
					for (let time14 in dialog){ // messages loop for every certain user
						let value = dialog[time14];
						let type = value.type;
						let content = value.content;
						let user = value.who;
						let rank = value.rank;
						let avatar = value.avatar;
						if (rank == -1){ // if customer
							youSay(content, type, user, rank, time14, avatar, thisChatShow);
						}
						else if (rank == 0){ // if service
							iSay(content, type, user, rank, time14, avatar, thisChatShow);
						}
					}
					setTimeout(() => {
						thisChatShow.scrollTop = thisChatShow.scrollHeight;
					}, 50);
					arrLastTime[user] = lastTime;
					console.log(0)
				}
				// update listItemOrder regularly (after full loop)
				
				let lastLen = Object.keys(arrLastTime).length;
				// console.log(lastLen)
				if (lastLen == 0){
					return;
				}
				else if (lastLen == 1){ // init null, but regular gets some
					document.querySelector('.chat-show').style.display = "block";
					document.querySelector('.list-item-select').checked = true;
					return;
				}
				else{
					updateListSeqByTime();
					// console.log(1)
				}
			}
		}
		let last_times = arrLastTime;
		let objGetRegular = {
			"action": "get",
			"is_init": false,
			"last_times": last_times
		}
		xhrGetRegular.send(JSON.stringify(objGetRegular));
		getReady = false;
		times--;
	}, interval);
}
btnExit.addEventListener('click', doExit);
function doExit(){
	location.replace('../admin.php');
}
chatInput.addEventListener('keypress', (e) => {
	if (e.ctrlKey && e.keyCode == 10){ // Ctrl + Enter to send - OK
	// if (e.keyCode == 13){ // Enter to send - bugged
		e.preventDefault();
		textSend();
	}
});
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
		for (let i = 0; i < items.length; i++){ // find the first image in clipboard and break
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
						if (xhrPutPic.readyState == 4 && xhrPutPic == 200){
							getReady = true;
							let res = null;
							try{
								res = JSON.parse(xhrPutPic.response);
							}
							catch(err){
								return;
							}
							if (res.flag){
								console.log('Image sent OK!');
							}
							else{
								console.log('Image sent failed!');
							}
						}
					});
					let userSel = '';
					document.querySelectorAll('.list-item-select').forEach(liSel => {
						if (liSel.checked == true) {
							userSel = liSel.value;
						}
					});
					if (userSel == '') {
						alert('没有选中一个活动用户');
						return;
					}
					let objPutPic = {
						"action": "put",
						"type": "image",
						"talker_to": userSel,
						"content": eFr.target.result
					};
					xhrPutPic.send(JSON.stringify(objPutPic));
					getReady = false;
				}
				break; // needn't loop rest clipboard
			}
		}
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
	if (chatInput.innerHTML == ''){
		alert('消息不能为空哦亲');
		return;
	}
	let chatShows = document.querySelectorAll('.chat-show');
	if (chatShows.length == 0){
		alert('没有用户在与您对话');
		return;
	}
	let userSel = '';
	document.querySelectorAll('.list-item-select').forEach(liSel => {
		if (liSel.checked == true){
			userSel = liSel.value;
		}
	});
	if (userSel == ''){
		alert('没有选中一个活动用户');
		return;
	}
	let xhrPutText = GetXmlHttpObject();
	let tPutAdmin = new Date().getTime();
	xhrPutText.open('post', 'ajax/msg_handler.php?t=' + tPutAdmin, true);
	xhrPutText.setRequestHeader("content-type", "application/json");
	xhrPutText.setRequestHeader("cache-control", "no-cache");
	xhrPutText.setRequestHeader("pragma", "no-cache");
	xhrPutText.addEventListener('readystatechange', function(){
		if (xhrPutText.readyState == 4 && xhrPutText.status == 200){
			getReady = true;
			let res = null;
			try{
				res = JSON.parse(xhrPutText.response);
			}
			catch(err){
				return;
			}
			if (res.flag){
				// console.log('Text sent OK!');
			}
			else{
				alert('发送失败，请联系管理');
			}
		}
	});
	let content = chatInput.innerText;
	objPutAdminText = {
		"action": "put",
		"type": "text",
		"talker_to": userSel,
		"content": content
	}
	xhrPutText.send(JSON.stringify(objPutAdminText));
	getReady = false;
	chatInput.innerText = '';
	sendReady = false;
}
//util
function updateListSeqByTime(){
	listItems = document.querySelectorAll('.list-item');
	listItemSelects = document.querySelectorAll('.list-item-select');
	let listItemLen = listItems.length;
	for (let i = 0; i < listItemLen; i++){
		for (let j = i + 1; j < listItemLen; j++){
			let thisUser = listItems[i].getAttribute('for').substring(12);
			let nextUser = listItems[j].getAttribute('for').substring(12);
			if (parseInt(arrLastTime[thisUser]) < parseInt(arrLastTime[nextUser])){
				listItemWrap.insertBefore(listItems[j], listItems[i]);
				
			}
		}
	}
	initDone = true;
}
// document.querySelector('.btn-test').addEventListener('click', function(){
// 	listItems = document.querySelectorAll('.list-item');
// 	listItemSelects = document.querySelectorAll('.list-item-select');
// 	let listItemLen = listItems.length;
// 	for (let i = 0; i < listItemLen; i++){
// 		for (let j = i + 1; j < listItemLen; j++){
// 			let thisUser = listItems[i].getAttribute('for').substring(12);
// 			let nextUser = listItems[j].getAttribute('for').substring(12);
// 			if (parseInt(arrLastTime[thisUser]) < parseInt(arrLastTime[nextUser])){
// 				listItemWrap.insertBefore(listItems[j], listItems[i]);
				
// 			}
// 		}
// 	}
// });
function activeChatShowAndCheckListItem(){
	// find the latest time & user
	let times = 0;
	let maxTime = 0;
	let latestUser = '';
	for (let user in arrLastTime){
		if (times == 0){
			maxTime = parseInt(arrLastTime[user]);
			latestUser = user;
		}
		else{
			if (parseInt(arrLastTime[user]) >= maxTime){
				maxTime = parseInt(arrLastTime[user]);
				latestUser = user;
			}
		}
		times++
	}
	chatShows = document.querySelectorAll('.chat-show');
	let chatShowLen = chatShows.length;
	for (let i = 0; i < chatShowLen; i++){ // loop chatShow and set active
		if (chatShows[i].getAttribute('name') == latestUser){
			chatShows[i].style.display = "block";
			namePieces = latestUser.split(':', 2);
			let merchName = namePieces[0];
			let custName = namePieces[1]
			chatHead.innerHTML = `来自商户 <span class="merchant">${merchName}</span> 的 <span class="customer">${custName}</span>`;
		}
	}
	document.querySelectorAll('.list-item').forEach(listItem => {
		if (listItem.firstElementChild.getAttribute('customer') == latestUser){
			listItem.style = 'background-image: linear-gradient(135deg, transparent, rgb(214, 244, 214))';
			listItem.firstElementChild.checked = true;
		}
	});
}
function shortTime(time13){
	let time13Int = parseInt(time13);
	let now = new Date();
	let oTime = new Date(time13Int);
	if (oTime.getFullYear() < now.getFullYear()){
		return oTime.getFullYear()
	}
	else{
		if (oTime.getMonth() < now.getMonth() || oTime.getDate() < now.getDate()){
			return oTime.getMonth() + 1 + '-' + oTime.getDate();
		}
		else /*if (oTime.getMonth() == now.getDate())*/{
			return (oTime.getHours() < 10 ? '0' + oTime.getHours() : oTime.getHours()) + ':' + (oTime.getMinutes() < 10 ? '0' + oTime.getMinutes() : oTime.getMinutes());
		}
	}
}
function listItemUpdate(name, time13, lastName, lastMsg, avatar){
	let thisInput = document.querySelector("input[customer='" + name + "'");
	thisInput.nextElementSibling.style.backgroundImage = `url("img/avatar/${avatar}")`;
	let vWrap = thisInput.nextElementSibling.nextElementSibling;
	let namePieces = name.split(':', 2);
	let merchName = namePieces[0];
	let custName = namePieces[1];
	vWrap.firstElementChild.firstElementChild.innerHTML = `[<span class="merchant">${merchName}</span>]${custName}`;
	vWrap.firstElementChild.firstElementChild.nextElementSibling.innerText = shortTime(time13);
	vWrap.lastElementChild.innerText = '[' + lastName + ']' + lastMsg;
}
function listAppend(name, time13, lastName, lastMsg, avatar){
	//0
	let listItemName = document.createElement('div');
	listItemName.classList.add('list-item-name');
	namePieces = name.split(':', 2);
	let merchName = namePieces[0];
	let custName = namePieces[1];
	listItemName.innerHTML = `[<span class="merchant">${merchName}</span>]<span class="customer">${custName}</span>`;
	let listItemTime = document.createElement('div');
	listItemTime.classList.add('list-item-time');
	listItemTime.innerText = shortTime(time13);
	//1
	let listItemLine1 = document.createElement('div');
	listItemLine1.classList.add('list-item-line-1');
	let listItemLast = document.createElement('div');
	listItemLast.classList.add('list-item-last');
	listItemLast.innerText = lastName + ':' + lastMsg;
	//0->1
	listItemLine1.appendChild(listItemName);
	listItemLine1.appendChild(listItemTime);
	//2
	let listItemSelect = document.createElement('input');
	listItemSelect.type = 'radio';
	listItemSelect.classList.add('list-item-select');
	listItemSelect.id = 'select-name-' + name;
	listItemSelect.setAttribute('customer', name);
	listItemSelect.name = 'list-item-select';
	listItemSelect.value = name;
	
	listItemSelect.addEventListener('change', updateList);
	listItemSelect.addEventListener('click', updateList);
	function updateList(){
		// reset list bgc
		document.querySelectorAll('.list-item-select').forEach(sel => {
			sel.parentElement.removeAttribute('style');
		});
		// chatShow visibility
		chatShows = document.querySelectorAll('.chat-show');
		let len = chatShows.length;
		for (let i = 0; i < len; i++){
			chatShows[i].removeAttribute('style');
		}
		if (this.checked){
			this.parentElement.style.backgroundImage = 'linear-gradient(135deg, transparent, rgba(214,244,214,1))';
			let namePieces = this.value.split(':', 2);
			let merchName = namePieces[0];
			let custName = namePieces[1];
			chatHead.innerHTML = `[<span class="merchant">${merchName}</span>]<span class="customer">${custName}</span>`;
			for (let j = 0; j < len; j++){ // set chatShow visible
				if (chatShows[j].getAttribute('name') == name){
					chatShows[j].style.display = "block";
					chatShowActive = chatShows[j];
					setTimeout(() => {
						chatShows[j].scrollTop = chatShows[j].scrollHeight;	
					}, 10);
				}
			}
		}
	}
	let listItemAvatar = document.createElement('div');
	listItemAvatar.classList.add('list-item-avatar');
	listItemAvatar.style.backgroundImage = `url("img/avatar/${avatar}")`;
	let listItemVerticalWrap = document.createElement('div');
	listItemVerticalWrap.classList.add('list-item-vertical-wrap');
	//1->2
	listItemVerticalWrap.appendChild(listItemLine1);
	listItemVerticalWrap.appendChild(listItemLast);
	//3
	let listItem = document.createElement('label');
	listItem.classList.add('list-item');
	listItem.setAttribute('for', 'select-name-' + name);
	//2->3
	listItem.appendChild(listItemSelect);
	listItem.appendChild(listItemAvatar);
	listItem.appendChild(listItemVerticalWrap);
	//3->4
	// listItemWrap.appendChild(listItemSelect);
	listItemWrap.appendChild(listItem);
}
function iSay(content, type, user, rank, timestamp, avatar, chatShow){
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
	msgMainWrap.classList.add('msg-main-wrap', 'msg-main-wrap-self');
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
function youSay(content, type, user, rank, timestamp, avatar, chatShow){
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
	msgMainWrap.classList.add('msg-main-wrap', 'msg-main-wrap-oppo');
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
function time2normal(time = +new Date()){
	time = time.toString().substring(0, 13);
	var date = new Date(parseInt(time) + 8 * 3600 * 1000); // 增加8小时
	return date.toJSON().substring(0, 19).replace('T', ' ');
}
function rank2Name(rank){
	switch (rank){
		case 0:
			return '官方';
		case -1:
			return '用户';
	}
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

// alert
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
