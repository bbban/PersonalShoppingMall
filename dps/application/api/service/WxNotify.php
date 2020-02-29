<?php

namespace app\api\service;

use app\api\model\Order as ModelOrder;
use app\api\model\Product;
use app\api\service\Order as ServiceOrder;
use app\lib\enum\OrderStatusEnum;
use Exception;
use think\Db;
use think\Loader;

Loader::import('WxPay.Wxpay',EXTEND_PATH,'.Api.php');
use WxPayNotify;

class WxNotify extends WxPayNotify
{
    public function NotifyProcess($objData, $config, $msg)
    {
        if($objData['result_code' == 'SUCCESS']){
            $orderNo = $objData['out_trade_no'];
            Db::startTrans();
            try{
                $order = ModelOrder::where('order_no','=',$orderNo)->find();
                if($order->status == OrderStatusEnum::UNPAID){
                    $service = new ServiceOrder();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if($stockStatus['pass']){
                        $this->updateOrderStatus($order->id,true);
                        $this->reduceStock($stockStatus);
                    }else{
                        $this->updateOrderStatus($order->id,false);
                    }
                    Db::commit();
                    return true;
                }
            }catch(Exception $e){
                Db::rollback();
                return false;
            }
        }else{
            return true;
        }
    }

    private function updateOrderStatus($orderID,$success){
        $status = $success?
            OrderStatusEnum::PAID : 
            OrderStatusEnum::PAID_BUT_OUT_OF;
        ModelOrder::where('id','=',$orderID)->update(['status' => '$status']);
    }

    private function reduceStock($stockStatus){
        foreach($stockStatus['pStatusArray'] as $singlePStatus){
            Product::where('id','=',$singlePStatus['id'])->setDec('stock',$singlePStatus['count']);
        }
    }
}