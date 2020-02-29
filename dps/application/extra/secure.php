<?php

return[
    'token_salt' => 'RKGzN9RJMjXTF9dd',
    'pay_back_url' => 'http://localhost/dps/public/index.php/api/v1/pay/notify'//这里配置回调url此处是本地地址是无效的，等有服务器再配置
    //Ngrok等反向代理软件可以把你的本地地址用他的服务器给转换成一个公网地址，但是不安全所以暂且不用
];