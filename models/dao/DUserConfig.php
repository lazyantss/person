<?php
namespace app\models\dao;
use Yii;

class DUserConfig extends \yii\db\ActiveRecord {
	public static function tableName() {
        return 'user_config';
    }
    public function rules() {
        return [
            ['id', 'integer'],
            ['user_id', 'integer'],
            ['mode', 'integer'],
            ['lastest_app_version', 'string', 'length' => [0, 64]],
            ['mode_schedule', 'integer'],
        ];
    }
}