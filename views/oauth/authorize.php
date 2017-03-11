<?php
use yii\helpers\Url;
$this->title="meshare";

?>
<main>
    <div class="window" style="height: 440px;">
        <h3>Authorize</h3>
        <div class="viewtab">
            <button class="login act"></button>
            <button class="register"></button>
        </div>

        <div class="spview">
            <!-- 邮箱登录 -->
            <form id="form1" action="<?= Url::current($authparams); ?>" method="post" autocomplete="off" onsubmit="return commit1(this)">
                <input type="email" name="username" placeholder="Email" maxlength="64" value="<?= $username ?>" />
                <input type="password" placeholder="Password" maxlength="32" class="pwd" />
                
                <label style="display: inline-block; height: 40px; line-height: 40px; vertical-align: top; margin-bottom: 20px;"><input type="checkbox" name="authorized" value="authorize" checked="checked" onclick="return false" />&nbsp;Authorize access basic account info</label>
                <input type="hidden" name="password" class="md5pwd" />

				<input type="submit" value="Authorization & Login" class="btn" />
            </form>
            <!-- 手机登录 -->
            <form id="form2" action="<?= Url::current($authparams); ?>" method="post" style="display: none;" onsubmit="return commit2(this)">
                <div>
                    <input type="text" id="zonecode" name="phone_region" value="<?= $phone_region ?>" readonly="readonly" style="width:120px;text-align:center;cursor:pointer;" />
                    <input type="text" name="phone_number" placeholder="Phone Number" maxlength="11" autocomplete="off" value="<?= $phone_num ?>" style="width: 65%; float: right;" />
                </div>
                <input type="password" placeholder="Password" maxlength="32" class="pwd" />
                
                <label style="display: inline-block; height: 40px; line-height: 40px; vertical-align: top; margin-bottom: 20px;"><input type="checkbox" name="authorized" value="authorize" checked="checked" onclick="return false" />&nbsp;Authorize access basic account info</label>
                <input type="hidden" name="password" class="md5pwd" />

				<input type="submit" value="Authorization & Login" class="btn bc2" />
            </form>
        </div>
    </div>
</main>
<script src="/js/jquery.form.min.js"></script>
<script src="/js/config.zonecode.js"></script>
<script src="/js/md5.min.js"></script>
<script>

    $(function() {
        $('.login').click(function() {
            var view = $('#form1');
            if(view.is(':hidden')) {
                $('.register').removeClass('act');
                $('.login').animate({marginLeft: '210px'}, 500, 'swing').addClass('act');
                view.show().animate({marginLeft: 0}, 500, function() {$('#fomr2').hide();});
            }
        });
        $('.register').click(function() {
            var view = $('#form1');
            if(view.is(':visible')) {
                $('.register').addClass('act');
                $('.login').animate({marginLeft: '15px'}, 500, 'swing').removeClass('act');
                $('#form2').show();
                view.animate({marginLeft: -460}, 500, function() {$(this).hide();});
            }
        });
        $('#zonecode').click(function() {
            zoneCodeSelect();
            $(this).blur();
            return false;
            });
        
    });
    function commit1(form) {
        var f = $(form);
        var item = f.find('[name=username]');
        if(!$.trim(item.val()).length) {
            item.focus();
            $.bubble({tg: item, text: 'Please enter email'});
            return false;
        }
        var reg = /^([a-zA-Z0-9]+[_|\_|\.|-]?)*[a-zA-Z0-9_]+@([a-zA-Z0-9]+[-|_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,5}$/;
        if(!reg.test(item.val())) {
            item.focus();
            $.bubble({tg: item, text: 'Please enter valid email'});
            return false;
        }

        item = f.find('.pwd');
        if(!$.trim(item.val()).length) {
            item.focus();
            $.bubble({tg: item, text: 'Please enter password'});
            return false;
        }

        $('.md5pwd').val(md5($('#form1').find('.pwd').val()));
        return true;
    }
    function commit2(form) {
        var f = $(form);
        var item = f.find('[name=phone_number]');
        if(!$.trim(item.val()).length) {
            item.focus();
            $.bubble({tg: item, text: 'Please enter phone number'});
            return false;
        }
        var reg = /^\d{7,11}$/;
        if(!reg.test(item.val())) {
            item.focus();
            $.bubble({tg: item, text: 'Please enter valid phone number'});
            return false;
        }

        item = f.find('.pwd');
        if(!$.trim(item.val()).length) {
            item.focus();
            $.bubble({tg: item, text: 'Please enter password'});
            return false;
        }

        $('.md5pwd').val(md5($('#form2').find('.pwd').val()));
        return true;
    }
   
    function zoneCodeSelect() {
        var zcv = $('.zcv');
        var tgs = $('#zonecode');
        var ofs = tgs.offset();
        if(!zcv.length) {
            zcv = $('<div class="zcv" style="display: none;"><div></div></div>').appendTo('body');
            zcv.css({left: ofs.left, top: ofs.top});
            var tmp = [], ix = 0;
            $.each(_zonecode, function(k, v) {
                ++ ix;
                tmp.push('<button value="'+ v +'">'+ v +'.'+ k +'</button>');
            });
            zcv.find('div').append(tmp.join(''));
            zcv.on('click', 'button', function() {
                tgs.val($(this).attr('value'));
                $(document).click();
            });
            $(window).resize(function() {
                ofs = tgs.offset();
                zcv.css({left: ofs.left, top: ofs.top});
            });
            $(document).keyup(function(e) {
                var code = e.keyCode?e.keyCode:e.which;
                if(code == 27 || code == 96) {
                    $(document).click();
                }
            }).click(function(e) {
                if(zcv.is(':visible')) {
                    var tg = e.target || e.srcElement;
                    if('INPUT' != tg.tagName) {
                        $('#zonecode').focus();
                    }
                    zcv.stop().animate({height: 0, opacity: 0}, 500, function() {
                        zcv.hide();
                    });
                }
            });
        }
        zcv.css({height: 0, opacity: 0, display: 'block'}).stop().animate({height: 221, opacity: 1}, 500);
        zcv.find('div').animate({scrollTop: 0}, 1000);
        zcv.find('button:eq(0)').focus();
    }
</script>