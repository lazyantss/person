$(function() {
	// 进场动画
	$('.effe').transition({rotateY: '0deg'}, 250, function() {
		$(this).removeClass('effe').removeAttr('style');
		$('.grlist li').each(function(i, t) {
			setTimeout(function() {
				$(t).transition({y: 0, opacity: 1});
			}, i * 100);
		});
	});

	// 转场动画
	$('body').on('click', 'a[href]:not([href^=javascript]), .trans', function() {
		trans($(this).attr('href'));
		return false;
	});

	// 图片懒惰加载
	if('object' == typeof(LazyLoad)) {
		LazyLoad.init({
			offset: 100,
			throttle: 250,
			unload: false,
			callback: function (element, op) {}
		});
	}

	// 文件选择事件
	$('body').on('change', ':file', function() {
		var t = $(this);
		var name = t.val();
		t.parent().find('span').text(name?name.substr(name.lastIndexOf('\\') + 1):t.attr('placeholder')?t.attr('placeholder'):'Please select file');
	});

	// 开关按钮事件
	$('body').on('click', '.sw:not([disabled]):not([auto])', function() {
		var t = $(this);
		if(t.hasClass('act')) {
			t.val('0');
			t.removeClass('act');
		}else {
			t.val('1');
			t.addClass('act');
		}
	});

	// 平滑滚动
	$.scrollTo = function(expr) {
		$('html, body').stop().animate({scrollTop: (expr?$(expr).offset().top:0)}, 500, 'swing');
	}

	// HTML5全屏化
	$.fn.fullscreen = function(st) {
		var el = $(this);
		var dom = el[0];
		if(st) {
			//el.addClass('fullscreen');
			if(dom.requestFullscreen) {
				dom.requestFullscreen();
			}else if(dom.msRequestFullscreen) {
				dom.msRequestFullscreen();
			}else if(dom.mozRequestFullScreen) {
				dom.mozRequestFullScreen();
			}else if(dom.webkitRequestFullScreen) {
				dom.webkitRequestFullScreen();
			}
		}else {
			//el.removeClass('fullscreen');
			if(document.exitFullscreen) {
				document.exitFullscreen();
			}else if(document.msExitFullscreen) {
				document.msExitFullscreen();
			}else if(document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			}else if(document.webkitCancelFullScreen) {
				document.webkitCancelFullScreen();
			}
		}
		return el;
	}
	$.fn.onFullscreenChange = function(cb) {
		var el = $(this);
		var dom = el[0];
		var fullscreenEvent = document.fullscreenEnabled && 'fullscreenchange' || document.mozFullScreenEnabled && 'mozfullscreenchange' || document.webkitFullscreenEnabled && 'webkitfullscreenchange' || document.msFullscreenEnabled && 'MSFullscreenChange';
		document.addEventListener(fullscreenEvent, function () {
			var fullscreenStatus = document.fullscreen || document.mozFullScreen || document.webkitIsFullScreen || document.msFullscreenElement;
			if('function' == typeof(cb)) {
				cb(!!fullscreenStatus);
			}
		});
		/*
		$(document).on('fullscreenchange, MSFullscreenChange, mozfullscreenchangee, webkitfullscreenchange', function(e) {
			console.log('EVENT');
			var fs = false;
			if('fullscreenchange' == e.type) {
				fs = document.fullscreen;
			}else if('MSFullscreenChange' == e.type) {
				fs = document.msFullScreen;
			}else if('mozfullscreenchange' == e.type) {
				fs = document.mozFullScreen;
			}else {
				fs = document.webkitIsFullScreen;
			}
			if('function' == typeof(cb)) {
				cb(fs);
			}
		});*/
	}
});
// 场景切换
function trans(url) {
	$('html').mask(0, false, 'wave');
	$('body').transition({rotateY: '90deg'}, 250, function() {
		location.href = url;
	});
	return false;
}
// 翻转移除
function flipRemove(tg, cb, hv) {
	tg.transition({rotateX: '90deg', opacity: 0}, 250, function() {
		if(hv) {
			tg.transition({width: 0}, 250, function() {
				tg.remove();
				if('function' == typeof(cb)) {
					cb();
				}
			});
		}else {
			tg.transition({height: 0}, 250, function() {
				tg.remove();
				if('function' == typeof(cb)) {
					cb();
				}
			});
		}
	});
}
// Code检测(登录超时跳转)
function checkCode(data, tourl) {
	if('object' != typeof(data) || false == !!data['result']) {
		return false;
	}
	var code = data.result;
	if(-1 != ['1001', '1002', '1003'].indexOf(code)) {
		if('boolean' != typeof(tourl) || tourl) {
			//trans(tourl?tourl:'/');
		}
		return 'login';
	}else if('ok' == code) {
		return true;
	}else {
		return code;
	}
}
// Session状态保持
var _ksinvt = false;
function keepSession(st, url, time) {
	if(!st || (st &&_ksinvt)) {
		_ksinvt = clearInterval(_ksinvt);
	}else {
		_ksinvt = setInterval(function() {
			$.getJSON(url, {random: Math.random()}, function(data) {
				checkCode(data);
			});
		}, time);
	}
	return _ksinvt;
}
window.onunload = function(){};
Date.prototype.format=function(fmt){var o={"M+":this.getMonth()+1,"d+":this.getDate(),"H+":this.getHours(),"m+":this.getMinutes(),"h+":this.getHours()%12==0?12:this.getHours()%12,"s+":this.getSeconds(),"q+":Math.floor((this.getMonth()+3)/3),"S":this.getMilliseconds(),"tt":this.getHours()<12?"am":"pm","TT":this.getHours()<12?"AM":"PM"};if(/(y+)/.test(fmt)){fmt=fmt.replace(RegExp.$1,(this.getFullYear()+"").substr(4-RegExp.$1.length))}for(var k in o){if(new RegExp("("+k+")").test(fmt)){fmt=fmt.replace(RegExp.$1,(RegExp.$1.length==1)?(o[k]):(("00"+o[k]).substr((""+o[k]).length)))}}return fmt};