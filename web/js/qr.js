/**
 * 收款码 / 管理模块逻辑
 */
// item expand & collapse
var btnToggleEdits = document.querySelectorAll('.btn-toggle-edit');
btnToggleEdits.forEach(bindEdit);
function bindEdit(btnToggleEdit){
	btnToggleEdit.onclick = showHideEdit;
}
function showHideEdit(){
	let qrInfoEdit = this.previousElementSibling;
	qrInfoEdit.classList.toggle('show');
	if (qrInfoEdit.classList.contains('show')){
		qrInfoEdit.querySelector('.input-text-out').focus();
		this.innerHTML = '收 起 ↑';
	}
	else{
		// 自动保存两者
		this.innerHTML = '展 开 ↓';
	}
}
// input text out
var inputTextOuts = tabQr.querySelectorAll('.input-text-out');
inputTextOuts.forEach(inputTextOut => {
	inputTextOut.addEventListener('input', function(){
		let item0 = this.closest('.qr-item');
		let filename = item0.firstElementChild.innerHTML;
		let method = item0.children[1].firstElementChild.firstElementChild.classList[0];
		let btnOut = this.previousElementSibling.lastElementChild;
		let textOrgOut = '';
		textOrigin.forEach(textOrg => {
			if (textOrg[0] == method && textOrg[1] == filename){
				textOrgOut = textOrg[2];
			}
		});
		if (this.value == textOrgOut){
			btnOut.classList.remove('clickable');
		}
		else{
			btnOut.classList.add('clickable');
		}
	});
});
// btn edit text
var btnTextOuts = tabQr.querySelectorAll('.btn-text-out');
btnTextOuts.forEach(btnTextOut => {
	btnTextOut.addEventListener('click', btnTextOutClick);
});
function btnTextOutClick(){
	if (!this.classList.contains('clickable')){
		return;
	}
	// data collect
	let filename = this.closest('.qr-info-edit').parentNode.firstElementChild.innerHTML;
	let method = this.closest('.qr-info-edit').previousElementSibling.firstElementChild.firstElementChild.classList[0];
	let text = this.parentNode.nextElementSibling.value;
	let btnOut = this;
	// xhr
	let xhrTextOut = GetXmlHttpObject();
	xhrTextOut.open('post', 'ajax/text_change.php?c=0');//c=0:out
	xhrTextOut.setRequestHeader('content-type', 'application/json');
	xhrTextOut.onload = function(){
		if (xhrTextOut.status == 200){
			let res = null;
			try{
				res = JSON.parse(xhrTextOut.response);
			}
			catch(err){
				console.log(res);
				return;
			}
			// console.log(res);
			let code = res.code;
			if (code == 'id_customer'){
				alert('身份错误，请重新登录', '出错了哦~');
				setTimeout(() => {
					location.replace('./')
				}, 3000);
				return;
			}
			else if (code == 'id_neither'){
				alert('身份错误，请重新登录', '出错了哦~');
				setTimeout(() => {
					location.replace('./')
				}, 3000);
				return;
			}
			else if (code == 'id_both'){
				alert('身份错误，请重新登录', '出错了哦~');
				setTimeout(() => {
					location.replace('./')
				}, 3000);
				return;
			}
			else if (code == 'arg_missing'){
				alert('参数异常，请谨慎操作', '出错了哦~');
				return;
			}
			else if (code == 'arg_error'){
				alert('参数错误，请谨慎操作', '出错了哦~');
				return;
			}
			else if (code == 'server_error'){
				alert('服务器出错，请反馈开发 QQ 15955965', '出错了哦~', () => {}, 30000);
				return;
			}
			else if (code == 'ok'){
				btnOut.classList.remove('clickable');
				let filename = res.filename;
				let method = res.method;
				let text = res.text;
				textOrigin.forEach(info => {
					if (info[0] == method && info[1] == filename){
						info[2] = text;
					}
				});
				alert('修改成功！', '成功啦~');
				return;
			}
			else{
				alert('未知错误', '出错啦~');
				return;
			}
		}
	}
	let objTextOut = {
		"filename": filename,
		"method": method,
		"text": text
	};
	xhrTextOut.send(JSON.stringify(objTextOut));
}
// input text in
var inputTextIns = tabQr.querySelectorAll('.input-text-in');
inputTextIns.forEach(inputTextIn => {
	inputTextIn.addEventListener('input', function(){
		let item0 = this.closest('.qr-item');
		let filename = item0.firstElementChild.innerHTML;
		let method = item0.children[1].firstElementChild.firstElementChild.classList[0];
		let btnIn = this.previousElementSibling.lastElementChild;
		let textOrgIn = '';
		textOrigin.forEach(textOrg => {
			if (textOrg[0] == method && textOrg[1] == filename){
				textOrgIn = textOrg[3];
			}
		});
		if (this.value == textOrgIn){
			btnIn.classList.remove('clickable');
		}
		else{
			btnIn.classList.add('clickable');
		}
	});
});
// btn edit text
var btnTextIns = tabQr.querySelectorAll('.btn-text-in');
btnTextIns.forEach(btnTextIn => {
	btnTextIn.addEventListener('click', btnTextInClick);
});
function btnTextInClick(){
	if (!this.classList.contains('clickable')){
		return;
	}
	let filename = this.closest('.qr-info-edit').parentNode.firstElementChild.innerHTML;
	let method = this.closest('.qr-info-edit').previousElementSibling.firstElementChild.firstElementChild.classList[0];
	let text = this.parentNode.nextElementSibling.value;
	let btnIn = this;
	// console.log(filename, method, text)
	let xhrTextIn = GetXmlHttpObject();
	xhrTextIn.open('post', 'ajax/text_change.php?c=1');//c=1:in
	xhrTextIn.setRequestHeader('content-type', 'application/json');
	xhrTextIn.onload = function(){
		if (xhrTextIn.status == 200){
			let res = null;
			try{
				res = JSON.parse(xhrTextIn.response);
			}
			catch(err){
				console.log(res);
				return;
			}
			let code = res.code;
			if (code == 'id_customer'){
				alert('身份错误，请重新登录', '出错了哦~');
				setTimeout(() => {
					location.replace('./')
				}, 3000);
				return;
			}
			else if (code == 'id_neither'){
				alert('身份错误，请重新登录', '出错了哦~');
				setTimeout(() => {
					location.replace('./')
				}, 3000);
				return;
			}
			else if (code == 'id_both'){
				alert('身份错误，请重新登录', '出错了哦~');
				setTimeout(() => {
					location.replace('./')
				}, 3000);
				return;
			}
			else if (code == 'arg_missing'){
				alert('参数异常，请谨慎操作', '出错了哦~');
				return;
			}
			else if (code == 'arg_error'){
				alert('参数错误，请谨慎操作', '出错了哦~');
				return;
			}
			else if (code == 'server_error'){
				alert('服务器出错，请反馈开发 QQ 15955965', '出错了哦~', () => {}, 30000);
				return;
			}
			else if (code == 'ok'){
				btnOut.classList.remove('clickable');
				let filename = res.filename;
				let method = res.method;
				let text = res.text;
				textOrigin.forEach(info => {
					if (info[0] == method && info[1] == filename){
						info[2] = text;
					}
				});
				alert('修改成功！', '成功啦~');
				return;
			}
			else{
				alert('未知错误', '出错啦~');
				return;
			}
		}
	}
	let objTextIn = {
		"filename": filename,
		"method": method,
		"text": text
	};
	xhrTextIn.send(JSON.stringify(objTextIn));
}

// label=>input ui-optimizing
var qrTextLabels = document.querySelectorAll('.qr-text-label');
qrTextLabels.forEach(qrTextLabel => {
	qrTextLabel.addEventListener('click', function(){
		qrTextLabel.parentElement.nextElementSibling.focus();
	});
});
// 
var textOrigin = [];
var items = tabQr.querySelectorAll('.qr-item');
items.forEach(item => {
	let method = item.children[1].firstElementChild.firstElementChild.classList[0];
	let filename = item.children[0].innerHTML;
	let textOut = item.children[2].firstElementChild.children[1].value;
	let textIn = item.children[2].lastElementChild.children[1].value;
	textOrigin.push([method, filename, textOut, textIn]);
});
// item dragging
var qrContainers = tabQr.querySelectorAll('.qr-container');
var iContainer = null; // 当前目标 container 序号
var posStart = null;
var posTarget = null;
var itemStart = null;
var editStart = null;
var btnStart = null;
var isDragging = false;
var qrPositions = [];
// delegation
tabQr.onmousedown = qrDown;
function qrDown(eDown){
	// console.log(this) // 此块中 this == tabQr
	// console.log(eDown)
	let downTarget = eDown.target;
	if (downTarget.classList.contains('qr-filename') || downTarget.closest('.qr-img-wrap')){ // target match
		if (eDown.button == 2){ // button mismatch
			return;
		}
		isDragging = true;
		// init related elems
		qrContainers = document.querySelectorAll('.qr-container');
		itemStart = downTarget.closest('.qr-item');
		posStart = downTarget.closest('.qr-position');
		posStart.children[1].classList.add('docker-occupied')
		editStart = itemStart.children[2];
		btnStart = itemStart.children[3];
		let itemPosChanged = false; // 拖动时 pos->item 是否有变更
		// 记录起始位置
		let originX = itemStart.offsetLeft;
		let originY = itemStart.offsetTop;
		// 转移item，设置拖动标记，
		tabQr.append(itemStart);
		itemStart.classList.add('drag');
		// 获取 item insetPos
		let x = eDown.pageX - originX;
		let y = eDown.pageY - originY;
		// 设置初始位置
		itemStart.style.left = originX + 'px';
		itemStart.style.top = originY + 'px';
		// 收 edit
		editStart.classList.remove('show');
		btnStart.innerHTML = '展 开 ↓';
		// 获取当前左栏，上栏尺寸
		let widthLeft = left.offsetWidth;
		let heightHeader = header.offsetHeight;
		// 获取 mousedown 时所有 position、对应坐标、所属容器
		qrPositions = document.querySelectorAll('.qr-position');
		let posInfos = [];
		qrPositions.forEach(qrPosition => {
			// posInfo
			let iContainer = null;
			qrContainers.forEach((container, index) => {
				if (qrPosition.parentNode == container){
					iContainer = index;
				}
			})
			posInfos.push([qrPosition, qrPosition.getBoundingClientRect(), iContainer]);
		});
		// Origin 保存 mousedown 后，mousemove 前 pos->item 模样
		let itemOrigin = [];
		qrContainers.forEach((container, iContainer) => {
			let poses = Array.from(container.children);
			itemOrigin[iContainer] = [];
			poses.forEach((pos, iPos) => {
				itemOrigin[iContainer][iPos] = pos.children[1].firstElementChild;
			});
		});
		document.onmousemove = itemDrag;
		let hoverType = [
			null, // inSlot: Target is a slot
			null, // isOther: Target is Other slot
			null, // isSameline Target is in Same Line
			null // isVacant: vacant slot
		]
		function itemDrag(eMove){
			if (eMove.pageX < x + widthLeft){
				itemStart.style.left = widthLeft + 'px';
			}
			else if (eMove.pageX >= window.innerWidth - itemStart.offsetWidth + x){
				itemStart.style.left = window.innerWidth - itemStart.offsetWidth + 'px';
			}
			else{
				itemStart.style.left = (eMove.pageX - x) + 'px';
			}
			if (eMove.pageY < y + heightHeader){
				itemStart.style.top = heightHeader + 'px';
			}
			else if (eMove.pageY >= window.innerHeight - itemStart.offsetHeight + y){
				itemStart.style.top = window.innerHeight - itemStart.offsetHeight + 'px';
			}
			else{
				itemStart.style.top = (eMove.pageY - y) + 'px';
			}
			for (const arr of posInfos){ // posTarget Type
				if (eMove.pageX >= arr[1].left && eMove.pageX <= arr[1].right && eMove.pageY >= arr[1].top && eMove.pageY <= arr[1].bottom){ // state-set: inside
					hoverType[0] = true;
					hoverType[1] = arr[0] != posStart ? true : false;
					hoverType[2] = arr[0].closest('.qr-container') == posStart.closest('.qr-container') ? true : false;
					hoverType[3] = arr[0].querySelector('.docker-add') ? true : false;
					posTarget = arr[0]; // search
					iContainer = arr[2];
					break;
				}
				else{ // state-set: outside
					hoverType[0] = false;
					hoverType[1] = false;
					hoverType[2] = false;
					hoverType[3] = false;
					posTarget = null;
					
				}
			}
			if (!hoverType[0]){ // hover out: restore
				if (itemPosChanged){
					itemOrigin.forEach((arrPos, iContainer) => {
						arrPos.forEach((orgItem, iPos) => {
							if (orgItem != null){
								qrContainers[iContainer].children[iPos].children[1].append(orgItem);
							}
						});
					})
				}
				itemPosChanged = false;
				return;
			}
			if (hoverType[1]){ // 别人
				if (hoverType[2]){ // 同行
					if (hoverType[3]){ // [1,1,1] 非自己, 同行, 空位
						itemPosChanged = false;
						return;
					}
					else{ // [1,1,0] 他slot, 同line, 占位
						let intervals = siblingIntervals(posStart, posTarget);
						if (intervals < 0){
							intervals = -intervals;
							if (posTarget.children[1].firstElementChild != null){ // 防重复触发
								let posPrevSibs = previousElementSiblings(posStart).reverse(); // 起始 pos 的左边
								for (let i = 0; i < intervals; i++){ // 执行挤压
									posPrevSibs[i].nextElementSibling.children[1].append(posPrevSibs[i].children[1].firstElementChild);
								}
							}
						}
						else{
							
							if (posTarget.children[1].firstElementChild != null){
								let posFollSibs = followingElementSiblings(posStart);
								for (let i = 0; i < intervals; i++){
									posFollSibs[i].previousElementSibling.children[1].append(posFollSibs[i].children[1].firstElementChild);
								}
							}
						}
						itemPosChanged = true;
					}
				}
				else{ // 他line
					if (hoverType[3]){ // [1, 0, 1] 他slot, 他line, 空位
						itemPosChanged = false;
					}
					else{ // [1, 0, 0] 他slot, 他line, 占位，执行挤压
						if (posTarget.children[1].firstElementChild != null){ // 防止执行挤压
							let posFollSibs = followingElementSiblings(posTarget).reverse();
							posFollSibs.forEach(posFollSib => {
								posFollSib.children[1].append(posFollSib.previousElementSibling.children[1].firstElementChild);
							});
						}
						itemPosChanged = true; // 无论
					}
				}
			}
		}
		document.onmouseup = function(){
			document.onmousemove = null;
			if (isDragging){
				// console.log(hoverType)
				itemStart.classList.remove('drag');
				itemStart.removeAttribute('style');
				// console.log(hoverType[1])
				// determine where itemStart to dock
				if (!hoverType[0]){
					// not a slot:
					// put itemStart back (other items already restored before while hovering)
					// No communication with backend
					posStart.children[1].append(itemStart);
				}
				else{ // is a slot [1, ?, ?, ?]
					if (hoverType[1]){ // isSlot, isOther
						if (hoverType[2]){ // isSlot, isOther, sameline
							if (hoverType[3]){ // isSlot, isOther, sameline, isVacant
								posStart.children[1].append(itemStart);
							}
							else{ // // isSlot, isOther, isSameline, ocupied
								posTarget.children[1].append(itemStart);
							}
						}
						else{ // isSlot, isOther, notSameline
							if (hoverType[3]){ // isSlot, isOther, otherline, isVacant
								posTarget.children[1].append(itemStart);
								posTarget.children[1].classList.remove('docker-add');
							}
							else{ // isSlot, isOther, otherline, occupied
								posTarget.children[1].append(itemStart);
								posTarget.children[1].classList.remove('docker-add');
							}
							// new docker-add
							let newOrder = null;
							if (iContainer == 3){
								newOrder = '拖到这里备用';
							}
							else{
								newOrder = parseInt(posTarget.parentNode.lastElementChild.children[0].innerHTML) + 1;
							}
							let qrPositionOrder = document.createElement('div');
							qrPositionOrder.classList.add('qr-position-order');
							qrPositionOrder.innerHTML = newOrder;
							let qrItemDocker = document.createElement('div');
							qrItemDocker.classList.add('qr-item-docker', 'docker-add');
							let newPos = document.createElement('div');
							newPos.classList.add('qr-position');
							newPos.append(qrPositionOrder, qrItemDocker);
							qrContainers[iContainer].append(newPos);
							newPos.previousElementSibling.children[1].classList.remove('docker-add');
							if (iContainer == 3){
								newPos.previousElementSibling.children[0].innerHTML = '拖上去启用';
							}
							// shrink origin container (otherline)
							let posFollSibs = followingElementSiblings(posStart);
							posFollSibs.forEach(posFollSib => {
								if (posFollSib.children[1].firstElementChild != null){
									posFollSib.previousElementSibling.children[1].append(posFollSib.children[1].firstElementChild);
								}
								else{
									posFollSib.previousElementSibling.children[1].classList.add('docker-add');
									posFollSib.remove();
								}
							});
						}
					}
					else{ // self
						posStart.children[1].append(itemStart);
					}
				}
				// goback
				if (hoverType[0] && hoverType[1] && !(hoverType[2] && hoverType[3])){
					itemOrigin = [];
					qrContainers.forEach((container, iContainer) => {
						let poses = Array.from(container.children);
						itemOrigin[iContainer] = [];
						poses.forEach((pos, iPos) => {
							itemOrigin[iContainer][iPos] = pos.children[1].firstElementChild;
						});
					});
					// console.log(itemOrigin)
					let dataLayout = [];
					itemOrigin.forEach((items, iRange) => {
						items.forEach((item, iPos) => {
							if (item){
								dataLayout.push({
									"sequence": iPos + 1,
									"filename": item.firstElementChild.innerHTML,
									"method": item.children[1].firstElementChild.firstElementChild.classList[0],
									"text": item.children[2].firstElementChild.lastElementChild.value,
									"comment": item.children[2].lastElementChild.lastElementChild.value,
									"range": (iRange == 3 ? 0 : iRange + 1)
								});
							}
						});
					});
					// console.log(itemOrigin)
					// console.log(dataLayout)
					// console.log(JSON.stringify(dataLayout))
					let xhrItemLayout = GetXmlHttpObject();
					xhrItemLayout.open('post', 'ajax/qr_item_layout.php', false);
					xhrItemLayout.setRequestHeader("content-type", "application/json");
					xhrItemLayout.onload = function(){
						if (xhrItemLayout.status == 200){
							let res = null;
							try{
								res = JSON.parse(xhrItemLayout.response);
							}
							catch(err){
								console.log(err);
								return;
							}
							let code = res.code;
							switch (code){
							case 'id_customer':
							case 'id_neither':
							case 'id_both':
							case 'permission_deny':
								setTimeout(() => {
									location.replace('./');
								}, 3000);
								alert('权限错误哦~', '出错啦~');
								break;
							case 'input_empty':
							case 'input_error':
								setTimeout(() => {
									location.replace('./');
								}, 3000);
								alert('数据填写有错哦~', '出错啦~');
								break;
							case 'db_error':
								alert('服务器出错了<br>请反馈 QQ 15955965<br>注明: agwow_bug<br>谢谢！', '出错啦~');
								break;
							case 'ok':
								alert('<span class="text-green">操作成功！</span>', '成功啦！', ()=>{}, 8000);
								break;
							}
						}
					}
					xhrItemLayout.send(JSON.stringify(dataLayout));
				}
				itemStart = null;
				posStart = null;
				editStart = null;
				btnStart = null;
				isDragging = false;
			}
		}
	}
}
/* inputHeadingNums*/
var rangeOrigin = [];
var inputHeadingNums = tabQr.querySelectorAll('.qr-heading-num');
inputHeadingNums.forEach(inputHedingNum => {
	// rangeNum init
	rangeOrigin.push([inputHedingNum, parseInt(inputHedingNum.value)]);
	inputHedingNum.addEventListener('keydown', function(eKey){
		if (eKey.keyCode == 13){
			btnRangeDo(eKey);
		}
	});
	inputHedingNum.addEventListener('input', function(){
		this.value = this.value.replace(/[^0-9]/g, '');
		this.value = this.value.replace(/\b(0+)/gi,'');
		let rangeOriginValueThis = null;
		rangeOrigin.forEach((arr) => {
			if (arr[0] == this){
				rangeOriginValueThis = parseInt(arr[1]);
			}
		});
		if (parseInt(this.value) == rangeOriginValueThis){
			this.parentNode.querySelector('.btn-range-ok').classList.remove('clickable');
		}
		else{
			this.parentNode.querySelector('.btn-range-ok').classList.add('clickable');
		}
	});
});
// btnRange
var btnRangeOks = document.querySelectorAll('.btn-range-ok');
btnRangeOks.forEach(btnRangeEach);
function btnRangeEach(btnRangeOk){
	btnRangeOk.addEventListener('click', btnRangeDo);
}
function btnRangeDo(e){
	t = e.target; // Nomatter Keypress or btnClick
	let qrHeading = t.parentNode;
	let btnRgOk = qrHeading.querySelector('.btn-range-ok');
	if (!btnRgOk.classList.contains('clickable')){
		// console.log('value unchanged');
		return;
	}
	let inputMin = qrHeading.querySelector('.qr-heading-num-min');
	let inputMax = qrHeading.querySelector('.qr-heading-num-max');
	let valueMin = parseInt(inputMin.value);
	let valueMax = parseInt(inputMax.value);
	let indexMin = -1;
	let indexMax = -1;
	if (isNaN(valueMin) || isNaN(valueMax)){
		alert('请输入数字', '出错啦');
		return;
	}
	if (valueMax < valueMin){
		alert(`${valueMin} 不能大于 ${valueMax} 哦...`, '出错啦~');
		return;
	}
	if (valueMax > 99999 || valueMax < 1 || valueMin > 99999 || valueMin < 1){
		alert('金额范围 1 ~ 99999');
		return;
	}
	rangeOrigin.forEach((arr, index) =>{
		if (arr[0] == inputMin){
			indexMin = index;
		}
		if (arr[0] == inputMax){
			indexMax = index;
			return;
		}
	});
	let xhrRange = GetXmlHttpObject();
	xhrRange.open('post', 'ajax/qr_range_handler.php');
	xhrRange.setRequestHeader("content-type", "application/json");
	xhrRange.onload = function(){
		if (xhrRange.status == 200){
			// xhrRange return
			// console.log(xhrRange.response)
			let res = null;
			try{
				res = JSON.parse(xhrRange.response);
			}
			catch(err){
				console.log(err);
				return;
			}
			let code = res.code;
			if (code == 'id_customer'){
				location.replace('customer.php');
				return;
			}
			else if (code == 'id_neither'){
				location.replace('https://www.baidu.com/');
				return;
			}
			else if (code == 'id_both'){
				location.replace('who.php');
				return;
			}
			else if (code == 'permission_deny'){
				location.replace('index.php');
				return;
			}
			else if (code == 'input_empty'){
				alert('没有检测到输入~', '出错啦~');
				return;
			}
			else if (code == 'input_illegal'){
				alert('输入内容不合法哦~', '出错啦~');
				return;
			}
			else if (code == 'elem_count_error'){
				alert('输入内容不合法哦~', '出错啦~');
				return;
			}
			else if (code == 'index_illegal'){
				alert('输入内容不合法哦~', '出错啦~');
				return;
			}
			else if (code == 'db_error'){
				alert('服务器故障，请反馈管理员 QQ 15955965', '出错啦~', ()=>{}, 100001);
				return;
			}
			else if (code == 'ok'){
				alert('<span class="text-green">成功更新</span>金额范围！', '操作成功~');
				let data = res.data;
				for (const key in data){
					// console.log(key, data[key])
					rangeOrigin[key][1] = data[key];
				}
				btnRgOk.classList.remove('clickable'); // 这个居然能获取到，很神奇
				return;
			}
		}
	}
	indexMin = indexMin.toString();
	indexMax = indexMax.toString();
	let objRange = {
		[indexMin]: valueMin,
		[indexMax]: valueMax
	};
	xhrRange.send(JSON.stringify(objRange));
}
// qr Deletion
var qrDeletes = document.querySelectorAll('.qr-delete');
qrDeletes.forEach(qrDelete => {
	qrDelete.addEventListener('click', function(){
		// this == <div class="qr-delete"></div>
		let item = qrDelete.closest('.qr-item');
		let filename = item.firstElementChild.innerHTML;
		let method = item.children[1].firstElementChild.firstElementChild.classList[0];
		item.closest('.qr-position').remove();
		let xhrDel = GetXmlHttpObject();
		xhrDel.open('post', 'ajax/qr_delete.php');
		xhrDel.setRequestHeader("content-type", "application/json");
		xhrDel.onload = function(){
			if (xhrDel.status == 200){
				let res = null;
				try{
					res = JSON.parse(xhrDel.response);
				}
				catch(err){
					console.log(err);
					return;
				}
				console.log(res);
				let code = res.code;
				if (code == 'customer'){
					alert('权限错误', '出错啦~');
					setTimeout(() => {
						location.replace('./customer.php');	
					}, 3000);
					return;
				}
				else if (code == 'neither'){
					alert('权限错误', '出错啦~');
					setTimeout(() => {
						location.replace('./');	
					}, 3000);
					return;
				}
				else if (code == 'both'){
					alert('权限错误', '出错啦~');
					setTimeout(() => {
						location.replace('./who.php');	
					}, 3000);
					return;
				}
				else if (code == 'get_not_null'){
					alert('参数非法', '出错啦~');
					setTimeout(() => {
						location.replace('./');	
					}, 3000);
					return;
				}
				else if (code == 'post_null'){
					alert('参数非法', '出错啦~');
					setTimeout(() => {
						location.replace('./');	
					}, 3000);
					return;
				}
				else if (code == 'post_malformat'){
					alert('参数非法', '出错啦~');
					setTimeout(() => {
						location.replace('./');	
					}, 3000);
					return;
				}
				else if (code == 'arg_count_error'){
					alert('参数错误', '出错啦~');
					setTimeout(() => {
						location.replace('./');	
					}, 3000);
					return;
				}
				else if (code == 'arg_name_error'){
					alert('参数非法', '出错啦~');
					setTimeout(() => {
						location.replace('./');	
					}, 3000);
					return;
				}
				else if (code == 'file_not_exists'){
					alert('文件读取错误', '出错啦~');
					setTimeout(() => {
						location.replace('./');	
					}, 3000);
					return;
				}
				else if (code == 'server_error'){
					alert('服务器内部错误，请反馈 QQ 15955965', '出错啦~', () => {}, 30000);
					setTimeout(() => {
						location.replace('./');	
					}, 3000);
					return;
				}
				else if (code == 'del_error'){
					alert('数据库未能移除该文件<br>请反馈 QQ 15955965', () => {}, 30000);
				}
				else if (code == 'del_ok'){
					let delText = `成功删除文件 <span class="text-blue">${filename}</span>！`;
					let dbStatus = res.db_status;
					let dbText = '';
					let usecStay = 5000;
					if (dbStatus == 'db_ok'){
						dbText = `数据库同步<span class="text-green">成功</span>！`;
					}
					else if (code == 'db_error'){
						dbText = `数据库同步<span class="text-red">失败</span>！<br>请反馈 QQ 159559065`;
						usecStay = 30000;
					}
					alert(delText + '<br>' + dbText, '消息~', () => {}, usecStay);
					location.reload();
					return;
				}
			}
		}
		let itemOrigin = [];
		qrContainers.forEach((container, iContainer) => {
			let poses = Array.from(container.children);
			itemOrigin[iContainer] = [];
			poses.forEach((pos, iPos) => {
				itemOrigin[iContainer][iPos] = pos.children[1].firstElementChild;
			})
		});
		let dataLayout = [];
		itemOrigin.forEach((items, iRange) => {
			items.forEach((item, iPos) => {
				if (item){
					dataLayout.push({
						"sequence": iPos + 1,
						"filename": item.firstElementChild.innerHTML,
						"method": item.children[1].firstElementChild.firstElementChild.classList[0],
						"text": item.children[2].firstElementChild.lastElementChild.value,
						"comment": item.children[2].lastElementChild.lastElementChild.value,
						"range": (iRange == 3 ? 0 : iRange + 1)
					});
				}
			});
		})
		console.log(dataLayout);
		let objDel = {
			"filename": filename,
			"method": method,
			"layout": dataLayout
		};
		xhrDel.send(JSON.stringify(objDel));
	});
});
/* util */
function followingElementSiblings(element){
	let siblings = [];
	let sibling = element.nextElementSibling;
	while (sibling){
		siblings.push(sibling);
		sibling = sibling.nextElementSibling;
	}
	return siblings;
}
function previousElementSiblings(element){
	let prevSibs = [];
	let parent = element.parentNode;
	for (let i = 0; i < parent.children.length; i++) {
		if (parent.children[i] === element){
			break;
		}
		prevSibs.push(parent.children[i]);
	}
	return prevSibs;
}
function siblingIntervals(elementStart, elementEnd){
	// children 不是数组，不能遍历，必须先转成数组，操！
	let siblings = Array.from(elementStart.parentNode.children);
	if (!siblings.includes(elementEnd)){
		return false;
	}
	let startIndex = null;
	let endIndex = null;
	siblings.forEach((sib, index) => {

		if (sib == elementStart){
			startIndex = index;
		}
		else if (sib == elementEnd){
			endIndex = index;
		}
	});
	return endIndex - startIndex;
}
// var test = document.querySelector('.test');
// test.onclick = function(){
// 	console.log(this)
// }
// console.log(qrContainers)
// let itemOrigin = [];
// qrContainers.forEach((container, iContainer) => {
// 	let poses = Array.from(container.children);
// 	itemOrigin[iContainer] = [];
// 	poses.forEach((pos, iPos) => {
// 		itemOrigin[iContainer][iPos] = pos.children[1].firstElementChild;
// 	});
// });
// console.log(itemOrigin)