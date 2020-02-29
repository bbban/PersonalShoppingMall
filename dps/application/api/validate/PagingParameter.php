<?php

namespace app\api\validate;

class PagingParameter extends BaseValidate
{
    protected $rule = [
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger'
    ];

    protected $message = [
        'page' =>'页数必须为正整数',
        'size' =>'分页参数必须为正整数'
    ];
}