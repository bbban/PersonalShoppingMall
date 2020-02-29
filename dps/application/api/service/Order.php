<?php

namespace app\api\service;

use app\api\model\Order as ModelOrder;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use Exception;
use think\Db;

class Order
{
    //从订单取出的商品信息
    protected $oproducts;
    //从数据库取出的商品信息
    protected $products;

    protected $uid;

    public function place($uid,$oproducts){
        $this->oproducts = $oproducts;
        $this->products = $this->getProductsByOrder($oproducts);
        $this->uid = $uid;
        $status = $this->getOrderStatus();
        if(!$status['pass']){
            $status['order_id'] = -1;
            return $status;
        }
        $orderSnap = $this->snapOrder($status);
        $order = $this->createorder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    private function createorder($snap){
        Db::startTrans();
        try{
            $orderNo = $this->makeOrderNo();
            $order = new ModelOrder();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->total_count = $snap['totalCount'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();

            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach($this->oproducts as &$p){
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oproducts);
            Db::commit();
            return[
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        }
        catch(Exception $e){
            Db::rollback();
            throw $e;
        }
    }

    public static function makeOrderNo(){
        $yCode = array('A','B','C','D','E','F');
        $orderSn = $yCode[intval(date('Y') - 2019)].strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        return $orderSn;
    }

    private function snapOrder($status){
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => ''
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        if(count($this->products) > 1){
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    private function getUserAddress(){
        $userAddress = UserAddress::where('user_id','=',$this->uid)->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => '没有收获地址，无法下单',
                'errorCode' => 60001
            ]);
        }
        return $userAddress->toArray();
    }

    private function getOrderStatus(){
        $status = [
            'pass' => true,
            'orderPrice' =>0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];

        foreach($this->oproducts as $oproduct){
            $pStatus = $this->getProductStatus($oproduct['product_id'],$oproduct['count'],$this->products);
            if(!$pStatus['haveStock']){
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['counts'];
            array_push($status['pStatusArray'], $pStatus);

            
        }
        return $status;
    }

    private function getProductStatus($oPID,$ocount,$products){
        $pIndex = -1;

        $pStatus = [
            'id' =>null,
            'haveStock' => false,
            'counts' => 0,
            'price' =>0,
            'name' => '',
            'totalPrice' => 0,
            'main_img_url' =>null
        ];

        for($i = 0;$i<count($products);$i++){
            if($oPID == $products[$i]['id']){
                $pIndex = $i;
            }
        }
        if($pIndex == -1){
            throw new OrderException([
                'msg' => 'id为'.$oPID.'的商品不存在，创建订单失败'
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['counts'] = $ocount;
            $pStatus['price'] = $product['price'];
            $pStatus['main_img_url'] = $product['main_img_url'];
            $pStatus['totalPrice'] = $product['price']*$ocount;
            if($product['stock'] >= $ocount){
                $pStatus['haveStock'] =true;
            }
        }
        return $pStatus;

    }

    private function getProductsByOrder($oproducts){
        $oPIDs = [];
        foreach($oproducts as $item){
            array_push($oPIDs,$item['product_id']);
        }
        $products = Product::all($oPIDs)->visible(['id','price','stock','name','main_img_url'])->toArray();
        return $products;
    }

    public function checkOrderStock($orderID){
        $oproducts = OrderProduct::where('order_id','=',$orderID)->select();
        $this->oproducts = $oproducts;
        $this->products = $this->getProductsByOrder($oproducts);
        $status = $this->getOrderStatus();
        return $status;
    }
}