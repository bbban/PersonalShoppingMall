<?php

namespace app\api\validate;

class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];

    protected $message = [
        'ids' => 'ids参数必须是以逗号分隔的多个正整数'
    ];

    protected function checkIDs($value){
        $result = true;
        $value = explode(',',$value);
        if(empty($value)){
            $result = false;
        }
        foreach ($value as $id){
            if(!$this->isPositiveInteger($id)){
                $result = false;
            }
        }
        return $result;
    }

}