<?php

namespace app\api\controller\v1;

use app\api\model\User;
use app\api\model\UserAddress;
use app\api\service\Token;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{
    protected $beforActionList = [
        'checkPrimaryScope' => [ 'only'=>'createOrUpdateAddress,getUserAddress' ]
    ];

    public function createOrUpdateAddress(){
        $validata = new AddressNew();
        $validata->goCheck();
        $uid = Token::getCurrentUid();
        $user = User::get($uid);
        if(!$user){
            throw new UserException();
        }
        $dataArray = $validata->getDataByRule(input('post.'));
        $userAddress = $user->address;
        if(!$userAddress){
            $user->address()->save($dataArray);
        }else{
            $user->address->save($dataArray);
        }

        return new SuccessMessage();
    }

    public function getUserAddress(){
        $uid =Token::getCurrentUid();
        $userAddress = UserAddress::where('user_id','=',$uid)->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户地址不存在',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }
}