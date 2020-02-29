<?php

namespace app\api\model;

class ThridApp extends BaseModel
{
    public static function check($ac,$se){
        $app = self::where('app_id','=',$ac)->where('app_secret','=',$se)->find();
        return $app;
    }
}