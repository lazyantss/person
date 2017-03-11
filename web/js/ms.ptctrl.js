;(function($){
	$.ptctrl = function(expr, opt) {
		var view = $(expr);
		var set = {
			init:	false,	// 初始化状态
			dire:	'lr',	// 方向urdl
			icon:	['&#xe603;', '&#xe602;', '&#xe600;', '&#xe601;'],	// 按钮图标
			cmds:	{'s': 0, 'l': 1, 'r': 2, 'u': 3, 'd': 4},	// 指令集
			log:	true,	// 开启日志
			isclick:false,
			host:	'ws://172.18.33.6:8989/ws',	// 连接地址
			tokenid:'',		// 用户tokenid
			physical_id:''	// 设备ID
		};
		$.extend(set, opt);
		
		var cbtn = false;

		// 输出消息到控制台
		function message(msg) {
			if(set.log) {
				console.log('PT Ctrl > '+ msg);
			}
		}
		
		// 显示控制按钮
		function showContral() {
			if(cbtn) {
				cbtn.css({opacity: 1});
			}
		}

		// 隐藏控制按钮
		function hideContral() {
			if(cbtn && !set.isclick) {
				cbtn.css({opacity: 0});
			}
		}

		// 发送指令
		function send(cmd, redir) {
			if(set.init) {
				var req = {
					tokenid:		set.tokenid,
					physical_id:	set.physical_id,
					ptz_cmd:		'undefined' != typeof(set.cmds[cmd]) ? set.cmds[cmd] : set.cmds['s'],
					para0:			0,
					para1:			0
				};
				message('Socket Send CMD: '+ req.ptz_cmd);
				$.getJSON(set.host, req, function(data) {
					if('ok' == data.result) {
						
					}else if('redirect' == data.result) {
						message('Request Redirect');
						set.host = 'http://'+ data.ip +':'+ data.port +'/';
						set.tokenid = data.token;
						message('URL '+ set.host +' Token '+ set.tokenid);
						send(cmd, true);
					}else {
						message('Request Error');
						console.log(data);
					}
				});
				return true;
			}
			return false;
		}

		// 初始化
		function init() {
			// 添加控制钮至视图
			cbtn = [];
			$.each(['u', 'r', 'd', 'l'], function(i, d) {
				if(-1 < set.dire.indexOf(d)) {
					cbtn.push('<button cmd="'+ d +'" class="ptctrl btn'+ d.toUpperCase() +'">'+ set.icon[i] +'</button>');
				}
			});
			view.append(cbtn.join(''));
			cbtn = view.find('.ptctrl');
			
			setTimeout(showContral, 1000);

			// 事件绑定 - 鼠标移入移出事件
			var _invcont = false;
			view.mousemove(function() {
				showContral();
				if(_invcont) {
					clearTimeout(_invcont);
				}
				_invcont = setTimeout(hideContral, 5000);
			}).mousemove();

			// 按钮按下
			cbtn.mousedown(function() {
				set.isclick = true;
				send($(this).attr('cmd'));
			}).mouseup(function() {
				set.isclick = false;
				send('s');
			}).mouseout(function() {
				if(set.isclick) {
					set.isclick = false;
					send('s');
				}
			});

			set.init = true;
		}
		
		// 定时检测 当播放器就绪时初始化控制组件
		var checkintv = false;
		checkintv = setInterval(function() {
			if(swfobj) {
				clearInterval(checkintv);
				init();
			}
		}, 500);

		return {
			get:	function(k) {
				return set[k];
			},
			send:	function(cmd) {
				send(cmd);
			},
			destroy: function() {
				set.init = false;
				if(cbtn) {
					cbtn.remove();
					cbtn = false;
				}
				message('PT Ctrl Destroyed');
			}
		};
	};
})(jQuery);