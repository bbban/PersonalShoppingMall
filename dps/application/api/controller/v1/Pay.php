<?php

namespace app\api\controller\v1;

use app\api\service\Pay as ServicePay;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\extra\WxPayConfig;

class Pay extends BaseController
{
    protected $beforActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    public function getPreOrder($id=''){
        (new IDMustBePositiveInt())->goCheck();
        $pay = new ServicePay($id);
        return $pay->pay();
    }

    public function receiveNotify(){
        // $wxPayConfig = new WxPayConfig();//这里需要实现这个类然后进行配置，
        $notify = new WxNotify();
        $notify->Handle();
    }
}