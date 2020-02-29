<?php

namespace app\api\controller\v1;

use app\api\service\Token;
use think\Controller;

class BaseController extends Controller
{
    protected function checkPrimaryScope(){
        Token::needPrimaryScope();
    }

    protected function checkExclusiveScope(){
        Token::needExclusiveScope();
    }
}