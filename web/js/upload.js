/**
 * 功能：二维码上传模块
 */
var html = document.querySelector('html');
html.addEventListener('dragenter', function (eEnter){
	eEnter.preventDefault();
	eEnter.stopPropagation();
	if (eEnter.target.classList.contains('up-area')){
		eEnter.target.classList.add('hover');
	}
}, false);
html.addEventListener('dragover', function (eOver){
	eOver.preventDefault();
	eOver.stopPropagation();
	if (eOver.target.classList.contains('up-area')){
		eOver.dataTransfer.dropEffect = 'copy';
	}
}, false);
html.addEventListener('dragleave', function (eLeave){
	eLeave.preventDefault();
	eLeave.stopPropagation();
	if (eLeave.target.classList.contains('up-area')){
		eLeave.target.classList.remove('hover');
	}
}, false);
var dropFiles = [];
html.addEventListener('drop', function (eDrop){
	eDrop.preventDefault();
	eDrop.stopPropagation();
	if (eDrop.target.classList.contains('up-area')){
		let method = eDrop.target.classList[1].substring(8);
		let dtf = eDrop.dataTransfer;
		var count = 0;
		var fileLen = dtf.files.length;
		if (dtf.items != undefined){
			for (let i = 0; i < dtf.items.length; i++){
				let item = dtf.items[i];
				if (item.kind == 'file' && item.webkitGetAsEntry().isFile){
					let file = item.getAsFile();
					let filename = file.name;
					let fileExt = getFileExtension(filename).toLowerCase();
					let allowExts = ['jpg', 'jpeg', 'jpe', 'png', 'gif', 'bmp'];
					console.log(allowExts.includes(fileExt))
					if (!allowExts.includes(fileExt)){
						alert(`<span class="text-blue">${filename}</span><br><span class="text-red">文件格式错误</span><br>仅支持以下文件格式:<br>jpg jpeg jpe png gif bmp`, '文件扩展名错误', () => {}, 10000);
						return;
					}
					let size = (file.size / 1024 / 1024).toFixed(2);
					if (size > 5){
						alert(`图片尺寸为 <span class="text-red">${size}</span> MB<br>超过了 <span class="text-blue">5M</span> 的限制<br>会导致进入页面卡顿`, '图片太大', () => {}, 10000);
						return;
					}
					let formData = new FormData();
					formData.append(method, file);
					let xhrFile = GetXmlHttpObject();
					xhrFile.open('post', 'ajax/qr_accept.php', false);
					xhrFile.onload = function (){
						if (xhrFile.status == 200){
							console.log(xhrFile.response)
							let res = null;
							try {
								res = JSON.parse(xhrFile.response);
							}
							catch (err){
								console.log(err);
								alert('未能成功传送文件', '出错啦~');
								return;
							}
							console.log(res)
							let code = res.code;
							if (code == 'customer'){
								alert('身份不对啦', '出错啦~');
								setTimeout(() => {
									location.replace('./customer.php');
								}, 3000);
								return;
							}
							else if (code == 'neither'){
								alert('身份不对啦', '出错啦~');
								setTimeout(() => {
									location.replace('./');
								}, 3000);
								return;
							}
							else if (code == 'both'){
								alert('身份不对啦', '出错啦~');
								setTimeout(() => {
									location.replace('./who.php');
								}, 3000);
								return;
							}
							else if (code == 'get_not_null') {
								alert('不要乱传参哦', '出错啦~');
								setTimeout(() => {
									location.replace('./index.php');
								}, 3000);
								return;
							}
							else if (code == 'no_files') {
								alert('只能拖文件进来哦', '出错啦~');
								return;
							}
							else if (code == 'file_not_uniq') {
								alert('如果你看到这个提示，说明这个虚拟世界正在重构', '出错啦~', () => {}, 8000);
								return;
							}
							else if (code == 'method_not_found') {
								alert('不要乱搞哦', '出错啦~');
								return;
							}
							else if (code == 'ext_not_exists'){
								alert('文件没有扩展名<br>扩展名只能是下面 4 种:<br> jpg png gif bmp', '没有扩展名', () => {}, 10000);
							}
							else if (code == 'unsupported_mime'){
								alert('扩展名不支持<br>扩展名只能是下面 4 种:<br> jpg png gif bmp', '扩展名错误', () => {}, 10000);
							}
							else if (code == 'ext_not_match'){
								let mime = res.mime;
								let ext = res.ext;
								alert(`文件的实际类型是 <span class="text-green">${mime}</span><br>但扩展名却是 <span class="text-red">${ext}</span><br>这会导致显示出错<br>更改图片扩展名为 <span class="text-blue">${mime}</span> 即可`, '内容与扩展名不匹配', () => {}, 10000);
								return;
							}
							else if (code == 'move_error') {
								alert('服务器内部错误，请反馈 QQ 15955965，暗号: move', () => {}, 60000);
								return;
							}
							else if (code == 'ok') {
								eDrop.target.classList.remove('hover');
								passByPrompt(filename, eDrop.target);
							}
						}
					}
					xhrFile.send(formData);
				}
			}
		}
		else{
			alert('暂时不支持非 Chrome 浏览器', '抱歉', () => {}, 5000);
			for (let i = 0; i < fileLen; i++) {
				let dropFile = dtf.files[i];
				if (dropFile.type) {
					dropFiles.push(dropFile);
					count++;
				}
				else{
					try {
						var fileReader = new FileReader();
						fileReader.readAsDataURL(dropFile);
						fileReader.addEventListener('load', function (e) {
							dropFiles.push(dropFile);
							count++;
						}, false);
						fileReader.addEventListener('error', function (e) {
							count++;
						}, false);
					}
					catch (err) {
						count++;
					}
				}
			}
		}

	}
}, false);
// util
function getFileExtension(filename) {
	var pos = filename.lastIndexOf('.');
	if (pos === -1) {
		return ''; // 没有找到点
	}
	return filename.substring(pos + 1);
}
function passByPrompt(filename, parent = document.body){
	let box = document.createElement('div');
	box.classList.add('pass-by-prompt');
	box.innerHTML = `${filename} <span class="text-green">成功上传</span>!`
	parent.append(box);
	setTimeout(() => {
		box.remove();
	}, 2000);
}