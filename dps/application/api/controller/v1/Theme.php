<?php

namespace app\api\controller\v1;

use app\api\model\Theme as ModelTheme;
use app\api\validate\IDCollection;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;

class Theme
{
    /**
     *
     *@url /theme?id=id1,id2,....
     *@return 一组theme信息
     * 
     */
    public function getSimpleList($ids = ''){
        (new IDCollection())->goCheck();
        $ids = explode(',',$ids);
        $result = ModelTheme::getThemesByIDs($ids);
        if(!$result){
            throw new ThemeException();
        }
        return $result;
    }

    public function getComplexOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $result = ModelTheme::getThemeWithProduct($id);
        if(!$result){
            throw new ThemeException();
        }
        return $result;
    }
}