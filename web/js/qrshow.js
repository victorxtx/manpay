/**
 * 最终向用户展示二维码的逻辑
 */
function secToString(seconds){
	if (isNaN(seconds) || seconds > 3599 || seconds < 0){
		return '00:00';
	}
	seconds = parseInt(seconds);
	let min = seconds >= 60 ? parseInt(seconds / 60).toString().padStart(2, '0') : '00';
	let sec = (seconds % 60).toString().padStart(2, '0');
	return min + ':' + sec;
}
function stringToSec(time_string){
	return parseInt(time_string.substring(0, 2)) * 60 + parseInt(time_string.substring(4, 5));
}
function parseCookie(cookie){
	let cookieTemp = document.cookie.split("; ");
	let cookies = [];
	for (let i = 0; i < cookieTemp.length; i++) {
		let keyValue = cookieTemp[i].split("=");
		cookies.push([keyValue[0], keyValue[1]]);
	}
	return cookies;
}
///////////////////////////
var countDown = document.querySelector('.count-down');
var second = parseInt(stringToSec(countDown.innerHTML));
var timer = setInterval(function(){
	if (second == false){
		clearInterval(timer);
		location.replace('./');
		return;
	}
	else{
		second -= 1;
		countDown.innerHTML = secToString(second);
	}
}, 1000);
