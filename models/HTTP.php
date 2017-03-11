<?php
namespace app\models;

use Yii;

class HTTP {
    /*
     * 参数获取
     */
    public static function request($key, $def = false) {
        $result = $def;
        if(isset($key)) {
            $request = Yii::$app->request;
            if($request->isGet) {
                $result = $request->get($key, $def);
            }else if($request->isPost) {
                $result = $request->post($key, $def);
            }
        }
        return $result;
    }
}