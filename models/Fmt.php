<?php
namespace app\models;
use Yii;
use yii\web\Response;
 
class Fmt extends \yii\base\Component {
    public function debugLog($msg, $param = '', $user_id = '', $force_log = FALSE) {
        if (!$force_log && !Yii::$app->params['debug_all_user_id'] ) {
            return;
        }
        
        Yii::info("user debug info $user_id ". $msg .' '.json_encode((''===$param) ? $_REQUEST : $param), 'system.info');
    }
    
}