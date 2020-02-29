<?php

namespace app\api\validate;

use app\lib\exception\ParameterException;
use think\Exception;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck(){
        

        $request = Request::instance();
        $params = $request->param();

        $result = $this->batch()->check($params);
        if(!$result){
            $e = new ParameterException([
                'msg' => $this->error
            ]);
            throw $e;
        }else{
            return true;
        }
    }

    protected function isPositiveInteger( $value, $rule = '', 
        $data = '', $field = ''){
        $result = false;
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0){
            $result =  true;
        }
        return $result;
    }

    public function isNotEmpty($value, $rule = '', 
    $data = '', $field = ''){
        $result = true;
        if(empty($value)){
            $result =false;
        }
        return $result;
    }

    public function getDataByRule($arrays){
        if(array_key_exists('user_id',$arrays)|array_key_exists('uid',$arrays)){
            throw new ParameterException([
                'msg' => '参数内拥有非法信息'
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value){
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }

}