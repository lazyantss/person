<?php
namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
 
class CommonController extends Controller {
	const AJAX_COMMON_FAIL = 1;
    const AJAX_PARAM_ERROR = 2;
    const AJAX_DATA_NO_CHANGE = 3;
    const AJAX_UNKNOWN_ERROR = 4;
    const AJAX_TOKEN_EMPTY = 1001;
    const AJAX_TOKEN_ERROR = 1002;
    const AJAX_OTHER_USER_ERROR = 1003;
    //user
    const AJAX_UNAME_EMPTY = 100000000; //参数username值为空
    const AJAX_PASSWORD_EMPTY = 100000001; //参数password值为空
    const AJAX_CLIENTTYPE_EMPTY = 100000002; //参数clienttype值为空
    const AJAX_LOGIN_ILLEGAL = 100000003; //用户名或密码非法
    const AJAX_UINFO_SAVE_TO_MEMC_FAIL = 100000004; //用户信息保存到memcache失败
    const AJAX_TOKENID_MEMC_TO_CLEAR_FAIL = 100000006; //memcache清除tokenid失败
    const AJAX_ADD_PASSWORD_EMPTY = 100000007; //参数password值为空
    const AJAX_EMAIL_EMPTY = 100000008; //参数email值为空
    const AJAX_EMAIL_ERROR = 100000009; //email格式非法
    const AJAX_PASSWORD_ERROR = 100000010; //密码非法
    const AJAX_EMAIL_REPEAT = 100000011; //邮箱已被注册
    const AJAX_UNAME_ADD_FAIL = 100000012; //添加用户失败
    const AJAX_MODIFY_PASSWORD_EMPTY = 100000014; //password值为空
    const AJAX_OLD_PASSWORD_EMPTY = 100000015; //oldpassword值为空
    const AJAX_PASSWORD_NO_MATCH = 100000016; //password组合规则不满足条件
    const AJAX_OLD_PASSWORD_ERROR = 100000017; //原密码输入有误
    const AJAX_MODIFYUSERNAME_USERNAME_EMPTY = 100000020;
    const AJAX_UNAME_REPEAT = 100000021; //用户名已被他人使用
    const AJAX_UNAME_NO_MATCH = 100000022; //用户名不符合规则
    const AJAX_FORGETPWD_EMAIL_EMPTY = 100000023;
    const AJAX_EMAIL_NO_EXIST = 100000024; //邮箱不存在
    const AJAX_CAPTCHA_EMPTY = 100000025; //验证码为空
    const AJAX_CAPTCHA_ERROR = 100000026; //验证码错误
    const AJAX_EMAIL_EXIST = 100000027; //邮箱已存在
    const AJAX_PASSWORD_NO_CHANGE = 100000030; //重设的密码不能和原密码一致
    const AJAX_UPDATE_FAIL = 100000033; //更新失败
    const AJAX_EMAIL_SEND_FAIL = 100000034; //邮件发送失败
    const AJAX_EMAIL_SEND_EXCEPTION = 100000035; //发送异常
    const AJAX_RECEIVE_SHARE_FAIL = 100000036; //接受分享失败
    const AJAX_MODIFYUSERNAME_PASSWORD_ERROR = 100000037;
    const AJAX_MODIFYEMAIL_PASSWORD_ERROR = 100000038;
    const AJAX_CLIENTTYPE_ERROR = 100000039;
    const AJAX_MODIFYEMAIL_EMAIL_EMPTY = 100000040;
    const AJAX_MODIFYEMAIL_PASSWORD_EMPTY = 100000041;
    const AJAX_UPDATE_DEVICEOWNER_FAIL = 100000042;
    const AJAX_SAVE_COOKIE_FAIL = 100000043;
    //mysql
    const AJAX_MYSQL_OPERATION_FAIL = 100000147;
    const AJAX_REDIS_OPERATION_FAIL = 100000148;
    // oauth2
    const AJAX_INVALID_TOKEN = 100500100 ;
    const AJAX_INVALID_REQUEST = 100500101 ;
    const AJAX_INVALID_GRANT = 100500102 ;
    const AJAX_INVALID_CLIENT = 100500103 ;
    const AJAX_INVALID_URI = 100500104 ;
    const AJAX_INVALID_SCOPE = 100500105 ;
    const AJAX_INVALID_NOTICE = 100500106 ;
    const AJAX_MALFORMED_TOKEN = 100500107 ;
    const AJAX_REDIRECT_URI_MISMATCH = 100500108 ;
    const AJAX_INSUFFICIENT_SCOPE = 100500109;
    const AJAX_UNAUTHORIZED_CLIENT = 100500110;
    const AJAX_UNSUPPORTED_GRANT_TYPE = 100500111;

	public static $ERROR_MESSAGE = array(
	    self::AJAX_COMMON_FAIL => 'server busy',
        self::AJAX_PARAM_ERROR => 'parameter error',
        self::AJAX_DATA_NO_CHANGE => 'data does not change',
        self::AJAX_UNKNOWN_ERROR => 'unknown error',
        self::AJAX_TOKEN_EMPTY => 'tokenid is empty',
        self::AJAX_TOKEN_ERROR => 'tokenid is invalid',
    	self::AJAX_OTHER_USER_ERROR => 'other user logined',
	    //user
        self::AJAX_UNAME_EMPTY => 'username is not set value',
        self::AJAX_PASSWORD_EMPTY => 'password is not set value',
        self::AJAX_CLIENTTYPE_EMPTY => 'clienttype is not set value',
        self::AJAX_LOGIN_ILLEGAL => 'The username and password do not match',
        self::AJAX_UINFO_SAVE_TO_MEMC_FAIL => 'error:memcache save obj fail',
        self::AJAX_TOKENID_MEMC_TO_CLEAR_FAIL => 'error:memcache delete obj fail',
        self::AJAX_ADD_PASSWORD_EMPTY => 'password is not set value',
        self::AJAX_EMAIL_EMPTY => 'email is not set value',
        self::AJAX_EMAIL_ERROR => 'Invalid Email address',
        self::AJAX_PASSWORD_ERROR => 'The passwords do not match',
        self::AJAX_EMAIL_REPEAT => 'This Email has already been taken',
        self::AJAX_UNAME_ADD_FAIL => 'add user fail',
        self::AJAX_MODIFY_PASSWORD_EMPTY => 'password is not set value',
        self::AJAX_OLD_PASSWORD_EMPTY => 'oldpassword is not set value',
        self::AJAX_PASSWORD_NO_MATCH => 'The passwords do not match',
        self::AJAX_OLD_PASSWORD_ERROR => 'Wrong password',
        self::AJAX_UNAME_REPEAT => 'User name already exists',
        self::AJAX_UNAME_NO_MATCH => 'The length of the username must be between 6 to 50 characters(letter,number,underscore)',
        self::AJAX_EMAIL_NO_EXIST => 'email does not exist',
        self::AJAX_CAPTCHA_EMPTY => 'Do not leave the verification code field empty/blank',
        self::AJAX_CAPTCHA_ERROR => 'Wrong verification',
        self::AJAX_EMAIL_EXIST => 'This Email address has already been taken.<br/><a href="/web/login/retrievepwdui" style="color:#A20705;text-decoration:underline;" target="_blank">Forgot your password?</a>',
        self::AJAX_PASSWORD_NO_CHANGE => 'The new password and the current password cannot be the same',
        self::AJAX_UPDATE_FAIL => 'error:mysql update user operation fail',
        self::AJAX_EMAIL_SEND_FAIL => 'send fail',
        self::AJAX_EMAIL_SEND_EXCEPTION => 'send exception',
        self::AJAX_RECEIVE_SHARE_FAIL => 'receive share fail',
        self::AJAX_MODIFYUSERNAME_PASSWORD_ERROR => 'wrong password',
        self::AJAX_FORGETPWD_EMAIL_EMPTY => 'email is not set value',
        self::AJAX_MODIFYEMAIL_PASSWORD_ERROR => 'wrong password',
        self::AJAX_CLIENTTYPE_ERROR => 'clienttype is error',
        self::AJAX_MODIFYUSERNAME_USERNAME_EMPTY => 'username is not set value',
        self::AJAX_MODIFYEMAIL_EMAIL_EMPTY => 'email is not set value',
        self::AJAX_MODIFYEMAIL_PASSWORD_EMPTY => 'password is empty',
        self::AJAX_UPDATE_DEVICEOWNER_FAIL => 'failed to update userinfo',
        self::AJAX_SAVE_COOKIE_FAIL => 'failed to save to cookie',
        //mysql
   		self::AJAX_MYSQL_OPERATION_FAIL => 'failed to operate mysql',	
    	self::AJAX_REDIS_OPERATION_FAIL => 'failed to operate redis',
   );
    public function ajaxOk($data = '', $addition = false) {
        $result = array(
            'result'    => 'ok',
            'data'      => $data
        );
        if(is_array($addition)) {
            $result = array_merge($result, $addition);
        }
        
        $response = Yii::$app->response;
        if(isset($_GET['callback'])) {
            $response->format = Response::FORMAT_JSONP;
            $result = ['data' => $result, 'callback' => $_GET['callback']];
        }else {
            $response->format = Response::FORMAT_JSON;
        }
        $response->data = $result;
        Yii::$app->end();
    }

    public function ajaxError($code = '0', $msg = '') {
        $result = array(
            'result'    => strval($code),
            'message'   => is_string($msg) && strlen($msg)>0 ? $msg : self::getMessage($code)
        );

        $response = Yii::$app->response;
        if(isset($_GET['callback'])) {
            $response->format = Response::FORMAT_JSONP;
            $result = ['data' => $result, 'callback' => $_GET['callback']];
        }else {
            $response->format = Response::FORMAT_JSON;
        }
        $response->data = $result;
        Yii::$app->end();
    }

    public function ajaxJson($data = '') {
        $response = Yii::$app->response;
        if(isset($_GET['callback'])) {
            $response->format = Response::FORMAT_JSONP;
            $result = ['data' => $data, 'callback' => $_GET['callback']];
        }else {
            $response->format = Response::FORMAT_JSON;
        }
        $response->data = $data;
        Yii::$app->end();
    }


    public function getMsg($code = '0') {
        return $code .' - '. self::getMessage($code);
    }

    private function getMessage($code = '0') {
        $map = array(
            '0' => 'Unknow Error',
            '1' => 'Username or Password Incorrect', 
            '10'=> 'Parameter Error',
            '11'=> 'Parameter Format Error',
            '20'=> 'Data Error',
            '21'=> 'Matching Error',
            '22'=> 'Request Method Error',
            '30'=> 'File Format Error',
            '31'=> 'File Upload Error',
            '32'=> 'File Hash Error',
            '40'=> 'Operation Failed',
            '41'=> 'Operation Successful',
            '42'=> 'Operation Not Permitted',
            '99'=> 'Unauthorized',
        );
        return array_key_exists($code, $map) ? $map[$code] : $map['0'];
    }
}