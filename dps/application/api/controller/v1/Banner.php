<?php

namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as ModelBanner;
use app\lib\exception\BannerMissException;


class Banner
{
    /**
     * 
     *获取指定id的banner信息
     * @url /banner/:id
     * @id banner的id号
     * 
     */
    public function getBanner($id){
        (new IDMustBePositiveInt())->goCheck();
        $banner = ModelBanner::getBannerByID($id);
        if(!$banner){
            throw new BannerMissException();
        }
        return $banner;


    }
}