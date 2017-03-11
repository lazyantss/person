<?php
namespace app\models\logic;

use app\controllers\CommonController;
use app\models\dao\DUserConfig;

class LUser {
	public static function user_config_set($user = array(), $param = array()) {
    	$user_id = $user['id'];
    	$config = DUserConfig::find()->where(array('user_id' => $user_id))->asArray()->all();
    	if (!is_array($config)) {
    		return array('errno' => CommonController::AJAX_MYSQL_OPERATION_FAIL);
    	}

        if (empty($config)) {
            $user_con = new DUserConfig();
            $user_con->mode = $param['mode'];
            $user_con->user_id = $user_id;
            $user_con->save();
        } else {
            $user_con = DUserConfig::findOne($config[0]['id']);
            $user_con->mode = $param['mode'];
            $user_con->save();
        }

    	return array('errno' => '0');
    }
}