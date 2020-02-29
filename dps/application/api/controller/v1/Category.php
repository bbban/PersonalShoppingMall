<?php

namespace app\api\controller\v1;

use app\api\model\Category as ModelCategory;
use app\lib\exception\CategoryException;

class Category
{
    public function getAllCategories(){
        $categories = ModelCategory::getCategories();
        if(!$categories){
            throw new CategoryException();
        }
        return $categories;
    }
}