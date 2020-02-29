<?php

namespace app\api\controller\v1;

use app\api\model\Product as ModelProduct;
use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product
{
    public function getRecent($count = 15){
        (new Count())->goCheck();
        $products = ModelProduct::getMostRecent($count);
        if(!$products){
            throw new ProductException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }

    public function getAllInCategory($id){
        (new IDMustBePositiveInt())->goCheck();
        $products = ModelProduct::getProductByCategoryID($id);
        if(!$products){
            throw new ProductException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }

    public function getOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $product = ModelProduct::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }
}