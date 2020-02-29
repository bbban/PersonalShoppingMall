<?php

namespace app\api\model;

class Theme extends BaseModel
{

    protected $hidden = ['delete_time','update_time','topic_img_id','head_img_id'];

    public function topicImg(){
        return $this->belongsTo('Image','topic_img_id','id');
    }

    public function headImg(){
        return $this->belongsTo('Image','head_img_id','id');
    }

    public function products(){
        return $this->belongsToMany('product','theme_product','product_id','theme_id');
    }

    public static function getThemesByIDs($ids){
        $result = self::with('topicImg,headImg')->select($ids);
        return $result;
    }

    public static function getThemeWithProduct($id){
        $theme = self::with('products,topicImg,HeadImg')->find($id);
        return $theme;
    }
}