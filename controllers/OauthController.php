<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\HTTP;
use app\models\LoginForm;
use app\models\User;
use OAuth2;
use Predis;

class OauthController extends Controller {
    public  $layout = 'login';
    private $_server = null;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [''],
                'rules' => [
                    [
                        'actions' => [''],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
     public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            
        ];
    }

    public function init() {
        $dsn = Yii::$app->db->dsn;
        $username = Yii::$app->db->username;
        $password = Yii::$app->db->password;
        // error reporting (this is a demo, after all!)
        ini_set('display_errors',1);
    

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $clientStorage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
        //connection redis
        $predis = new Predis\Client('tcp://'.Yii::$app->params['redis_addr0'].':'.Yii::$app->params['redis_port0']);
        $tokenStorage = new OAuth2\Storage\Redis($predis);
        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->_server = new OAuth2\Server(array(
            'access_token'          => $tokenStorage,
            'authorization_code'    => $tokenStorage,
            'client_credentials'    => $clientStorage,
            'client'                => $clientStorage,
            'refresh_token'         => $tokenStorage,
            'scope'                 => $clientStorage,
        ));

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->_server->addGrantType(new \OAuth2\GrantType\ClientCredentials($clientStorage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->_server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($tokenStorage));
        // create the grant type
        // add the grant type to your OAuth server
        $this->_server->addGrantType(new \OAuth2\GrantType\RefreshToken($tokenStorage));   
    }

    /**
     * 权限验证
     * @return [type] [description]
     */
    public function actionAuthorize() {
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();

        // validate the authorize request
        if (!$this->_server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            die;
        }
        $authparams = $request->query;
        Yii::$app->Fmt->debugLog('oauth/authorize',$authparams);
        if (Yii::$app->request->isPost){

            if (Yii::$app->request->post('authorized') && Yii::$app->request->post('authorized') === 'authorize') {
                //验证用户名密码
                $model = new LoginForm();
                // var_dump(Yii::$app->request->post());die;
                if ($model->load(array('form' => Yii::$app->request->post()), 'form') && $model->login()) {
                    //这个地方验证成功后要把用户的uid加进来
                    $is_authorized = true;
                    $user_id = Yii::$app->user->identity['id'];
                    Yii::$app->Fmt->debugLog('oauth/authorize','',$user_id);
                    $this->_server->handleAuthorizeRequest($request, $response, $is_authorized,$user_id);
                    Yii::$app->Fmt->debugLog('oauth/authorize',$response);
                    $response->getHttpHeader('Location');
                    $response->send();
                }
             
            }
        }
        //登录并授权页面
        $username = isset( $_COOKIE['username'] ) ? $_COOKIE['username'] :'';
        $email = HTTP::request('email');
        if(!empty($email)) {
            $username = $email;
        }
        $phone_num = isset( $_COOKIE['phone_num'] ) ? $_COOKIE['phone_num'] :'';
        $phone_region = isset( $_COOKIE['phone_region'] ) ? $_COOKIE['phone_region'] :'1';

        return $this->render('authorize',array('username' => $username, 'phone_num' => $phone_num, 'phone_region' => $phone_region, 'authparams' => $authparams));
    }
    /**
     * code换取accesstoken
     * @return [type] [description]
     */
    public function actionAccesstoken() {

        //这个方法里有得写所有的验证及生成access_token的操作。（会有数据库的写入）
        $this->_server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    }

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function actionUserinfo() {
        if (!$this->_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->_server->getResponse()->send();
            die;
        }

        $token = $this->_server->getAccessTokenData(OAuth2\Request::createFromGlobals());

        $user_id = $token['user_id'];
        $res = User::findOne($user_id)->toArray();
        unset($res['password']);
        var_dump($res);
        
    }
    
}