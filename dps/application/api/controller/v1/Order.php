<?php

namespace app\api\controller\v1;

use app\api\controller\v1\BaseController;
use app\api\model\Order as ModelOrder;
use app\api\service\Order as ServiceOrder;
use app\api\service\Token;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;

class Order extends BaseController
{
    protected $beforActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser']
    ];

    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');
        $uid = Token::getCurrentUid();

        $order = new ServiceOrder();
        $status = $order->place($uid,$products);
        return $status;
    }

    public function getSummaryByUser($page=1,$size=15){
        (new PagingParameter())->goCheck();
        $uid = Token::getCurrentUid();
        $pageOrders = ModelOrder::getSummaryByUser($uid,$page,$size);
        if($pageOrders->isEmpty()){
            return [
                'data' => [],
                'current_page' =>$pageOrders->getCurrentPage()
            ];
        }
        $data = $pageOrders->hidden(['snap_item','snap_address','prepay_di'])->toArray();
        return [
            'data' => $data,
            'current_page' =>$pageOrders->getCurrentPage()
        ];
    }

    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = ModelOrder::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail;
    }
}