<?php

namespace app\api\service;

use app\api\model\ThridApp;
use app\lib\exception\TokenException;

class AppToken extends UserToken{
    
    public function gget($ac,$se){
        $app = ThridApp::check($ac,$se);
        if(!$app){
            throw new TokenException([
                'msg' => '授权失败',
                'errorCode' => 10004
            ]);
        }else{
            $scope = $app->scope;
            $uid = $app->id;
            $value = [
                'scope' => $scope,
                'uid' => $uid
            ];
            $token = $this->saveToCache($value);
            return $token;
        }

    }
}