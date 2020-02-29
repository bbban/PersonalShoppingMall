<?php

namespace app\api\controller\v1;

use app\api\service\AppToken;
use app\api\service\Token as ServiceToken;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\ParameterException;

class Token
{
    public function getToken($code=''){
        (new TokenGet())->goCheck();
        $ut  = new UserToken($code);
        $token = $ut->get();
        return [
            'token' => $token
        ];
    }

    public function verifyToken($token=''){
        if(!$token){
            throw new ParameterException([
                'token不允许为空'
            ]);
        }
        $valid = ServiceToken::verifyToken($token);
    }

    public function getAppToken($ac='',$se=''){
        (new AppTokenGet())->goCheck();
        $app = new AppToken(1);
        $token = $app->gget($ac,$se);
        return [
            'token' => $token
        ];
    }
}