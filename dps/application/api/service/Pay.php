<?php

namespace app\api\service;

use app\api\model\Order;
use app\api\service\Order as ServiceOrder;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use Exception;
use think\Loader;
use think\Log;
use WxPayApi;
use WxPayJsApiPay;
use WxPayUnifiedOrder;

Loader::import('WxPay.Wxpay',EXTEND_PATH,'.Api.php');

class Pay
{
    private $orderID;
    private $orderNO;

    function __construct($orderID){
        if(!$orderID){
            throw new Exception('OrderID不能为空');
        }
        $this -> orderID = $orderID;
    }

    public function pay(){
        $this->checkOrderValidate();
        $orderService = new ServiceOrder();
        $status = $orderService->checkOrderStock($this->orderID);
        if(!$status['pass']){
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    public function checkOrderValidate(){
        $order = Order::where('id','=',$this->orderID)->find();
        if(!$order){
            throw new OrderException();
        }
        if(!Token::isValidateOperate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }
        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'code' => 400,
                'msg' => '订单已被支付',
                'errorCode' => 80003
            ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }
    private function makeWxPreOrder($totalPrice){
        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }
        $wxOrderData = new WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }

    private function getPaySignature($wxOrderData){
        // $wxPayConfigInterface = new WxPayConfig();//由于没有微信的商户号，所以不做配置，直接传入
        $wxOrder = WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS'||$wxOrder['result_code' != 'SUCCESS']){
            Log::record($wxOrder,'error');
            Log::record('获取与支付订单失败','error');
        }
        $this->recordPreOrder($wxOrder);
        return $this->sign($wxOrder);;
    }

    private function sign($wxOrder){
        $jsApiPayData = new WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $values = $jsApiPayData->GetValues();
        $values['paySign'] = $sign;
        unset($values['appId']);
        return $values;
    }

    private function recordPreOrder($wxOrder){
        Order::where('id','=',$this->orderID)->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }
}