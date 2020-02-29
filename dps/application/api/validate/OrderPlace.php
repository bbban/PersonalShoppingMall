<?php

namespace app\api\validate;

use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
    protected $rule = [
        'product' => 'checkProducts'
    ];

    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];

    protected function checkProducts($value){
        if(!is_array($value)){
            throw new ParameterException([
                'msg' => '商品参数不正确'
            ]);
        }
        if(empty($value)){
            throw new ParameterException([
                'msg' => '商品列表不能为空'
            ]);
        }
        foreach($value as $value){
            $this->checkProduct($value);
        }
    }

    protected function checkProduct($value){
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if(!$result){
            throw new ParameterException([
                'msg' => '商品参数错误'
            ]);
        }
    }
}