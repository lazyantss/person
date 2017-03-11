<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\HTTP;
use app\models\User;
use app\models\logic\LUser;
use OAuth2;
use Predis;



class AlexaController extends CommonController {
    private $_server = null;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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

        $this->_server->addGrantType(new \OAuth2\GrantType\RefreshToken($tokenStorage));
        if (!$this->_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->_server->getResponse()->send();
            die;
        }  
    }
    
    //获取用户信息
    public function actionUsr_info() {
        $token = $this->_server->getAccessTokenData(OAuth2\Request::createFromGlobals());
        // var_dump($token);die;
        $user_id = $token['user_id'];
        $data['applianceId'] = $user_id;
        $data['manufacturerName'] = 'zmodo';
        $data['modelName'] = '1';
        $data['verdion'] = '1.0';
        $data['friendlyName'] = 'zmodo mode';
        $data['friendlyDescription'] = '';
        $data['idReachable'] = '1';
        $data['actions'] = ['home', 'leave', 'sleep'];
        $this->ajaxOK($data);
    }

    /**
     * 更改用户模式
     * @return [type] [description]
     */
    public function actionChange_usr_mode() {
     
        $data = Yii::$app->request->post();    
        return $res = self::httpsRequest($data);
        
    }
    public static function httpsRequest($data = array(), $method = 'POST'){
        $url = Yii::$app->params['alexaUrl'] . Yii::$app->requestedRoute;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        switch ($method){
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                $query = http_build_query($data);
                if (strpos($url, '?') !== FALSE)
                {
                    $url .= '&' . $query;
                }
                else
                {
                    $url .= '?' . $query;
                }
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, TRUE);
                if (!empty($data))
                {
                    $post_data = http_build_query($data);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                }
                break;
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        curl_close ($curl);
        return $result;
    }
}