<?php

namespace app\api\service;

use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;

class Token
{
    public static function generateToken(){
         $randChars = getRandChar(32);//common.php中
         $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
         $salt = config('secure.token_salt');
         return md5($randChars.$timestamp.$salt);
    }

    public static function getCurrentTokenVar($key){
        $token = Request::instance()
            ->header('token');
        $vars = Cache::get($token);
        if(!$vars){
            throw new TokenException();
        }else{
            if(!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取Token变量不存在');
            }
        }
    }

    public static function getCurrentUid(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    public static function needPrimaryScope(){
        $scope = self::getCurrentTokenVar('scope');
        if(!$scope){
            if($scope >= ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    public static function needExclusiveScope(){
        $scope = self::getCurrentTokenVar('scope');
        if(!$scope){
            if($scope == ScopeEnum::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }
    
    public static function isValidateOperate($checkedUID){
        $result = false;
        if(!$checkedUID){
            throw new Exception('被检测id不能为空');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkedUID){
            $result = true;
        }
        return $result;
    }

    public static function verifyToken($token){
        $result = false;
        $exist = Cache::get($token);
        if($exist){
            $result =  true;
        }
        return $result;
        
    }
        
}