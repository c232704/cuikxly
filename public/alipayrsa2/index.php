<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: text/plain');

require_once 'aop/AopClient.php';
require_once 'aop/request/AlipayTradeAppPayRequest.php';

// 获取支付金额
$amount='';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $amount=$_POST['total'];
}else{
    $amount=$_GET['total'];
}

$total = floatval($amount);
if(!$total){
    $total = 1;
}

$aop = new AopClient;
$aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
$aop->appId = "2016012701123321";
$aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEAvz2jY/2FLOb9BiTyzLXcD6A56LgbPBBkxs3HxlVpoDewZGARhUfLf19uHdIKlTqMqP2sJM+wWd7Z7z1aTg6ohznw+GHjpGbh05a2qnB40NssNlZqfGsXpqvLlAqS5wNTrqMppvoZ/EDBXn/f7JQybEUwxHR3JvusRDPcE1U6Bk7cJqlcKLhCSe7RvoGq9XlmDCV+3c6RAZxReZ+un2Xg+aXq/AhEPIcHlWXYkqRmA/xt4w3mQ5pjX9mgMyven+IvilyeyaE0o2w8fU2F5sgGTLEO4QemKa6em6CxDXML4745xr981C3jIrdeKkhb7AtaltJtqS7jYNjm5CAiLG5NyQIDAQABAoIBAE0QIIxFd+ntNt7H+tNFIWVmko9VyRu+G24FNFCW0JaQelMoZ0cG9FjicrQvlLYnvtDGUB0RWCYO6FFArugvfffoAFOG1r3D+5JQ9FDgO78l4r4OuqBiY0h1h/ajACl5LLp942X2+Iuu6H0VGH30BhGdHBa+O9mIpxVgtHlR6azqXbxOXvHplfldLB1evwIkD2RR8DZ/Am8ohTLdVAGXH4TWK5acOAWF9Txyyv7Vt9trN/k+nJpK98TTJv0R+jX8j01LJvlPXeygWBh3/3SdTep078Jhoux+80KMok7Qvxt41E0sQ0zcOapJ7Dn4prqJVB8mhr73w0vmeUS8ACrHRwECgYEA6MvrLdpy9ws6Sf4ePxKqgd9w2z3TA15XGdA9Ox1cOJhq3XI/haeVlsoFVQT+DDv48FcQvNPDcsNzbIEj1Dd1ntuss4GtFoQRGadpE1ix1zqbVmhzZVZV75GxuxUlQENuOb6NQYW9srdaqT9wOny55mI/Wyx8CgZZ0Utc3wdKLWkCgYEA0k1f0f/6wDCW/THYKJf47/Bl6+MSASVXJbIQo07nAvK3WcL6iOGjvQ9tHvwy+s0rgIcqXWpMivcVj+i7q9Y8c+TDhF5fKZwLAsy9mw8VJDnKoaExFYXO2hVh98TDG+DBsIs/xOWngXuGhSraS5JF4gyhXJcjVO596OgrrvNSMWECgYBJ6bTZ8ineVipDo/Nmgz6vzYm/knmU8Dcncv597myBa6NCfCu++9566dmXjOY8bC414O7nhTThEz1qjfYaQyGNNqI2vE2AgJ4NTyIFinusomb/oFpDKo5OaivMAoK5TK+Klf2zeAsq2xxl8EZpy4Aarcb/47feVvXdhdgO1/mtQQKBgQCrViKlIxqWDoBVVJC5sec9BOzk3CFFEbkImyR3CMepXToIRZR8hOk0FSdgQTT/pLy2gGkBpqMp7NiCYKjV/TtPaKfqZkC908Ycx9YKSKV+2B7AxGULFfNLNnSUupgDVIA5Z5nSvxBTq+ITCLDFNSz+WC/3EaPVhGDMqqs8ZD0owQKBgQCjwxKeODSsqht9ebQn6DoyJFsi1X5hv1gcg8lsMXP9qn4XYrIxPswz851+pJTmu3r894FxHGckqJt4t1U08qA+ZthwBt/NlprSYxfrB9nSnq7pZXTxJ9dqkvnw/jk7U03ipHLOflEaYMXp30qF8PKYboZBnc/VNytHZdPsxyxmbw==';
$aop->format = "json";
$aop->charset = "UTF-8";
$aop->signType = "RSA2";
$aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsAg9f7w+FYU991p0tHJFlwxdzSmNZKpoIIvuA/luEbEb09PAQgbOvPYJa5dqzsMiMDeM78peXcBj7Sw23HKv4qfinJKKJJEPqzO9ChTy2Pp8P5W3VLk94HdrOHZ1bTAU9Cz65PxImLrE4M36H7a22onLj8m+/tl7sEllcXLYrpcyNiad/h3c7bPUx8NHRhev74FR5Jxw4dyDVxKTPZLuwmSRvcubI7Dbkid3oU8wHsyWdheU5EhF5CEvlqKPT8RyZqU7uiE8USBugtnjSJnvJWlHE6BjGASGGTurxxqBKfbw87dpu/d+g0RQWGNzgGJvhx3JetAM3V9vYKhKbud+AQIDAQAB';
//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
$request = new AlipayTradeAppPayRequest();

// 异步通知地址
$notify_url = urlencode('https://rw.gzzsw.cn');
// 订单标题
$subject = '444';
// 订单详情
$body = '111'; 
// 订单号，示例代码使用时间值作为唯一的订单ID号
$out_trade_no = date('YmdHis', time());

//SDK已经封装掉了公共参数，这里只需要传入业务参数
$bizcontent = "{\"body\":\"".$body."\","
                . "\"subject\": \"".$subject."\","
                . "\"out_trade_no\": \"".$out_trade_no."\","
                . "\"timeout_express\": \"30m\","
                . "\"total_amount\": \"".$total."\","
                . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                . "}";
$request->setNotifyUrl($notify_url);
$request->setBizContent($bizcontent);
//这里和普通的接口调用不同，使用的是sdkExecute
$response = $aop->sdkExecute($request);

// 注意：这里不需要使用htmlspecialchars进行转义，直接返回即可
echo $response;
?>