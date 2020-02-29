<?php

namespace app\api\model;

class Category extends BaseModel
{
    protected $hidden = ['delete_time','create_time','update_time'];

    public function img(){
        return $this->belongsTo('Image','topic_img_id','id');    
    }

    public static function getCategories(){
        $categories = self::all([],'img');
        return $categories;
    }
}