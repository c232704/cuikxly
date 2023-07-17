<?php

namespace app\api\controller;

use app\common\controller\Api;
use fast\Random;
use think\Validate;
use think\Db;
use app\common\model\MoneyLog;
/**
 * 会员接口
 */
class Pay extends Api
{
    protected $noNeedLogin = ['notify','notifycz','notifydsf'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->Usermodel = model('User');
    }
    public function index()
    {
        // // 启动事务
        // Db::startTrans();
        // try {
        
            if(input('id',0)>0){
                $id=input('id');
                $data=Db::name('task')->where('id',$id)->find();
                if($data){
                    $number = date('ymdh', time()) . rand(10000, 99999);//订单编号
                    Db::name('task')->where('id',$id)->update(['out_trade_no'=>$number,'buytype'=>input('buytype')]);
                    $data=Db::name('task')->where('id',$id)->find();
                }else{
                    $this->error(__('订单数据不对'));
                }
            }else {
                $number = date('ymdh', time()) . rand(10000, 99999);//订单编号
                if(!input('price')){
                     $this->error(__('金额不对'));
                }
                $datapost =input('param.');//订单数据
                $datapost['buzs']=htmlspecialchars_decode($datapost['buzs']);
                if(input('endtime')=='数量完成自动结束'){
                    $endtimesjc=100000000000;
                }else{
                    $endtimesjc=strtotime(input('endtime'));
                }
                $up=[
                    'type'=>input('type'),
                    'uid'=>input('uid'),
                    'name'=>input('name'),
                    'rwurl'=>input('rwurl'),
                    'buz'=>$datapost['buzs'],
                    'price'=>input('price'),
                    'sum'=>input('sum'),
                    'sumsy'=>input('sum'),
                    'endtime'=>input('endtime'),
                    'subtime'=>input('subtime'),
                    'shetime'=>input('shetime'),
                    'endtimesjc'=>$endtimesjc,
                    'buytype'=>input('buytype'),
                    'zprice'=>input('price')*input('sum'),
                    'out_trade_no'=>$number,
                    'createtime'=>time(),
                    ];
                $id=Db::name('task')->insertGetId($up);
                $data=$up;
            }
            $site=config('site');
            $amount=$data['zprice'];
            if(!$amount){
                     $this->error(__('金额不对'));
                }
            // 异步通知地址
            $notify_url = $site['zfb']['notifyurl'];
            // 订单标题
            $subject = 'wool订单';
            // 订单详情
            $body = 'wool致力于打造最好的移动服务平台'; 
            // 订单号，示例代码使用时间值作为唯一的订单ID号
            if($data['buytype']=='支付宝'){
                $this->allpay($amount,$subject,$body,$number,$notify_url);
            }else if($data['buytype']=='余额'){
                $this->yuepay($amount,input('uid'),$id);
            }
        //     // 提交事务
        //     Db::commit();
        // } catch (\Exception $e) {
        //     // 回滚事务
        //     var_dump($e);
        //     Db::rollback();
        // }
    }
    public function videopay()
    {
                
                $datavideo_order=Db::name('video_order')->where(['sid'=>input('id'),'uid'=>input('uid'),'lx'=>2])->find();
                $datavideo_order1=Db::name('video_order')->where(['sid'=>input('id'),'uid'=>input('uid'),'lx'=>1])->find();
                $sad=1;
                $lx=input('lx');
                if($datavideo_order1){
                    if($datavideo_order1['type']==2){
                        $sad=2;
                        $this->error(__('已经全集购买')); 
                    }
                }
                if($datavideo_order and $lx==2){
                    if($datavideo_order['type']==2){
                        $this->error(__('已经单集购买')); 
                    }
                    $number = date('ymdh', time()) . rand(10000, 99999);//订单编号
                    $price=input('priceq');
                    $pricebuy=input('pricebuy');
                    if(input('lx')==2){
                        $price=input('priced');
                        $pricebuy=input('pricebuy');
                    }
                    $up=[
                        'type'=>1,
                        'lx'=>$lx,//1 全章购买 2单集购买	
                        'uid'=>input('uid'),
                        'name'=>input('namet'),
                        'img'=>input('img'),
                        'price'=>$price,
                        'sum'=>input('sum'),
                        'sid'=>input('pid'),
                        'smid'=>input('smid'),
                        'paytype'=>input('buytype'),
                        'zprice'=>$pricebuy*input('sum'),
                        'out_trade_no'=>$number,
                        ];
                    Db::name('video_order')->where(['id'=>$datavideo_order['id']])->update($up);    
                    $id=$datavideo_order['id'];
                    $data=$up;
                }else{
                    $number = date('ymdh', time()) . rand(10000, 99999);//订单编号
                    if(!input('pricebuy')){
                         $this->error(__('金额不对'));
                    }
                    $price=input('priceq');
                    $pricebuy=input('pricebuy');
                    if(input('lx')==2){
                        $price=input('priced');
                        $pricebuy=input('pricebuy');
                    }
                    $up=[
                        'type'=>1,
                        'lx'=>$lx,//1 全章购买 2单集购买	
                        'uid'=>input('uid'),
                        'name'=>input('namet'),
                        'img'=>input('img'),
                        'price'=>$price,
                        'sum'=>input('sum'),
                        'sid'=>input('pid'),
                        'smid'=>input('smid'),
                        'paytype'=>input('buytype'),
                        'zprice'=>$pricebuy*input('sum'),
                        'out_trade_no'=>$number,
                        'createtime'=>time(),
                        'updatetime'=>time(),
                        ];
                        //$this->error($up);
                    $id=Db::name('video_order')->insertGetId($up);
                    $data=$up;
                }
                
            $site=config('site');
            $amount=$data['zprice'];
            if(!$amount){
                     $this->error(__('金额不对'));
                }
            // 异步通知地址
            $notify_url = $site['zfb']['notifyurl'];
            // 订单标题
            $subject = 'wool订单';
            // 订单详情
            $body = 'wool致力于打造最好的移动服务平台'; 
            // 订单号，示例代码使用时间值作为唯一的订单ID号
            //var_dump($data['paytype']);
            if($data['paytype']=='支付宝'){
                $this->allpay($amount,$subject,$body,$number,$notify_url);
            }else if($data['paytype']=='余额'){
                $this->videoyuepay($amount,input('uid'),$id);
            }else if($data['paytype']=='微信'){
                $wxlx=input('wxlx');
                if($wxlx=='wxxcx'){
                    $openid=input('openid');
                    if(!$openid){
                        $this->error(__('openid不对'));  
                    }
                    $this->paywxxcx($id,$number,$amount,$openid);
                }else if($wxlx=='wxh5'){
                    $buytype='微信h5';
                    $this->paywxh5($id,$number,$amount,$buytype);
                }else if($wxlx=='wxgzh'){
                    $buytype='微信公众号';
                    $this->paywxh5($id,$number,$amount,$buytype);
                }
                
            }
    }
    public function paywxh5($oid,$number,$amount,$buytype){
        $site=config('site');
        $user = $this->auth->getUser();
        if(!$user){
           $this->error(__('user不对'));  
        }
        if($amount<=0){
            $this->error(__('金额不对')); 
        }
        $up=[
                'type'=>1,
                'uid'=>$user['id'],
                'tdnum'=>input('tdnum'),//支付通道
                'amount'=>$amount,
                'out_trade_no'=>$number,
                'createtime'=>time(),
                'buytype'=>$buytype,
                'oid'=>$oid,
            ];
        $id=Db::name('paylog')->insertGetId($up);
        $urls=$site['imgurl'].'/index/paydsf/wxh5?out_trade_no='.$number;
        if($buytype=='微信公众号'){
            $urls=$site['imgurl'].'/index/index/pay?id='.$id;
        }
        $this->success('订单提交成功 正在跳转支付',$urls);
    }
    public function paywxxcx($oid,$number,$amount,$openid){
        $site=config('site');
        $user = $this->auth->getUser();
        if(!$user){
           $this->error(__('user不对'));  
        }
        if($amount<=0){
            $this->error(__('金额不对')); 
        }
        $up=[
                'type'=>1,
                'uid'=>$user['id'],
                'amount'=>$amount,
                'out_trade_no'=>$number,
                'createtime'=>time(),
                'buytype'=>'微信小程序',
                'oid'=>$oid,
            ];
        $id=Db::name('paylog')->insertGetId($up);
        $modelxcx = new \app\api\controller\Paywx;
        $trs=$modelxcx->wxxcx($number,$amount,$openid);
        $this->success('提交成功',$trs);
    }
    public function videoyuepay($money,$user_id,$id)//余额支付
    {
        $user = $this->Usermodel::get($user_id);
        if ($user && $money != 0) {
            $data=Db::name('video_order')->where('id',$id)->find();
            if($user->money<$money){
                $this->error(__('余额不足')); 
            }
            $before = $user->money;
            $after = $user->money - $money;
            if($data['type']==1){
                Db::startTrans();
                try {
                    //更新会员信息
                    $user->save(['money' => $after]);
                    //写入日志
                    MoneyLog::create(['user_id' => $user_id, 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => '用户消费']);
                    Db::name('video_order')->where('id',$id)->update(['type'=>2,'paytype'=>'余额','ftime'=>time()]);
                    
                    $this->yongjin($id,$user_id,$money,'级消费佣金结算');//佣金结算
                    $this->dailiyongjin($id,$data['sid'],$data['smid'],$user_id,$money,'佣金结算');//后台代理佣金结算
                    
                    Db::commit();
                    $this->success(__('操作成功'));
                }catch (Exception $e){
                    Db::rollback();
                    $this->error($e->getMessage());
                }   
            }else if($data['type']>1){
                    $this->error(__('已经支付')); 
            }
                
        }else{
           $this->error(__('金额不对')); 
        }
    }
    public function yuepay($money,$user_id,$id)//余额支付
    {
        $user = $this->Usermodel::get($user_id);
        if ($user && $money != 0) {
            $data=Db::name('task')->where('id',$id)->find();
            if($user->money<$money){
                $this->error(__('余额不足')); 
            }
            $before = $user->money;
            $after = $user->money - $money;
            //更新会员信息
            $user->save(['money' => $after]);
            //写入日志
            MoneyLog::create(['user_id' => $user_id, 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => '用户消费']);
            if($data['paytype']==1){
                   $up=[
                        'paytype'=>2,
                        'buytype'=>'余额',
                        'ftime'=>time()
                    ];
                if (Db::name('task')->where('id',$id)->update($up)){
                   $this->success(__('操作成功'));
               }    
            }else if($data['paytype']>1){
                $this->error(__('已经支付')); 
            }
        }else{
           $this->error(__('金额不对')); 
        }
    }
    public function czpay(){
        $site=config('site');
        $user = $this->auth->getUser();
        if(!$user){
           $this->error(__('user不对'));  
        }
        $number = date('ymdh', time()) . rand(10000, 99999);//订单编
        $amount=input('total',0);
        $notify_url = $site['zfb']['notifyurlcz'];
        // 订单标题
        $subject = 'wool充值';
        // 订单详情
        $body = 'wool致力于打造最好的移动服务平台'; 
        if($amount<=0){
            $this->error(__('金额不对')); 
        }
        $up=[
                    'type'=>1,
                    'uid'=>$user['id'],
                    'amount'=>input('total'),
                    'out_trade_no'=>$number,
                    'createtime'=>time(),
                    ];
        $id=Db::name('paylog')->insertGetId($up);
        if(input('type','allpay')=='iap'){
            $this->success('请求成功',$number);
        }else{
            $this->allpay($amount,$subject,$body,$number,$notify_url);  
        }
    }
    public function dsfczpay(){
        $site=config('site');
        $user = $this->auth->getUser();
        if(!$user){
           $this->error(__('user不对'));  
        }
        $number = date('ymdh', time()) . rand(10000, 99999);//订单编
        $amount=input('total',0);
        if($amount<=0){
            $this->error(__('金额不对')); 
        }
        $up=[
                'type'=>1,
                'uid'=>$user['id'],
                'tdnum'=>input('tdnum'),
                'amount'=>$amount,
                'out_trade_no'=>$number,
                'createtime'=>time(),
                'buytype'=>isset($site['dsfzf']['zfname'])?$site['dsfzf']['zfname']:'',
            ];
        $id=Db::name('paylog')->insertGetId($up);
        $urls=$site['imgurl'].'/index/paydsf?out_trade_no='.$number;
        $this->success('订单提交成功 正在跳转支付',$urls);
    }
    public function czpayiaptz(){
        $out_trade_no=input('out_trade_no');
        $order=Db::name('paylog')->where('out_trade_no',$out_trade_no)->find();
                    //$this->dingding_log($order);
                    if($order['type']==1){
                        $up=[
                            'type'=>2,
                            'payment'=>null,
                            'trade_no'=>null,
                            'buyer'=>null,
                            'buytype'=>'苹果',
                            'ftime'=>time()
                            ];
                            Db::startTrans();
                            try {
                                $this->money($order['amount'],$order['uid'],0,$sxf=0);
                                Db::name('paylog')->where('out_trade_no',$out_trade_no)->update($up);
                                Db::commit();
                                $this->success('支付成功');
                            }catch (Exception $e){
                                Db::rollback();
                                $this->error($e->getMessage());
                            }
                    }else{
                        $this->success('已经支付');
                    }   
    }
    public function allpay($amount,$subject,$body,$out_trade_no,$notify_url)//支付宝支付
    {
        $total = floatval($amount);
        if(!$total){
             $this->error(__('金额不对'));
        }
        //require_once('/vendor/alipayrsa2/AopSdk.php');
        //Vendor('AopSdk.alipayrsa2');
        //  require_once './vendor/alipayrsa2/aop/AopClient.php';
        //  require_once './vendor/alipayrsa2/aop/request/AlipayTradeAppPayRequest.php';
        vendor('alipayrsa2.AopSdk');
        $site=config('site');
        $aop = new \AopClient;
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $site['zfb']['appId'];
        //开发者私钥去头去尾去回车，一行字符串
        $aop->rsaPrivateKey = $site['zfb']['rsaPrivateKey'];
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        //请填写支付宝公钥，一行字符串
        $aop->alipayrsaPublicKey = $site['zfb']['alipayrsaPublicKey'];
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new \AlipayTradeAppPayRequest();
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
        //$this->success(__('获取成功'),$response);
        echo $response;
    }
    public function notify()
    {
        if ($_POST){
            //Db::transaction(function () {
                if($_POST['trade_status'] == 'TRADE_SUCCESS' or $_POST['trade_status'] == 'TRADE_FINISHED'){//处理交易完成或者支付成功的通知 //获取订单号
                    $out_trade_no = $_POST['out_trade_no'];//交易号
                    $trade_no = $_POST['trade_no'];//订单支付时间
                    $gmt_payment = $_POST['gmt_payment'];//转换为时间戳
                    $order=Db::name('task')->where('out_trade_no',$out_trade_no)->find();
                    if($order['paytype']==1){
                        $up=[
                            'paytype'=>2,
                            'payment'=>$gmt_payment,
                            'trade_no'=>$trade_no,
                            'buyer'=>$_POST['buyer_logon_id'],
                            'buytype'=>'支付宝',
                            'ftime'=>time()
                            ];
                        if (Db::name('task')->where('out_trade_no',$out_trade_no)->update($up)){
                            echo 'success';
                        }    
                    }else{
                        echo 'success';
                    }
                }
            //});
        }
    }
    public function notifycz()
    {
        if ($_POST){
            //$this->dingding_log($_POST);
            //Db::transaction(function () {
                if($_POST['trade_status'] == 'TRADE_SUCCESS' or $_POST['trade_status'] == 'TRADE_FINISHED'){//处理交易完成或者支付成功的通知 //获取订单号
                    $out_trade_no = $_POST['out_trade_no'];//交易号
                    $trade_no = $_POST['trade_no'];//订单支付时间
                    $gmt_payment = $_POST['gmt_payment'];//转换为时间戳
                    $order=Db::name('paylog')->where('out_trade_no',$out_trade_no)->find();
                    //$this->dingding_log($order);
                    if($order['type']==1){
                        $up=[
                            'type'=>2,
                            'payment'=>$gmt_payment,
                            'trade_no'=>$trade_no,
                            'buyer'=>$_POST['buyer_logon_id'],
                            'buytype'=>'支付宝',
                            'ftime'=>time()
                            ];
                            Db::startTrans();
                            try {
                                $this->money($order['amount'],$order['uid'],0,$sxf=0);
                                Db::name('paylog')->where('out_trade_no',$out_trade_no)->update($up);
                                Db::commit();
                                echo 'success';
                            }catch (Exception $e){
                                Db::rollback();
                                $this->error($e->getMessage());
                            }
                    }else{
                        echo 'success';
                    }
                }
            //});
        }
    }
    public function notifydsf()//第三方充值回调
    {
            $this->dingding_log($_REQUEST);
            //Db::transaction(function () {
                $returncode=isset($_REQUEST["returncode"])?$_REQUEST["returncode"]:1;
                if($returncode == "00"){//处理交易完成或者支付成功的通知 //获取订单号
                    $out_trade_no = isset($_REQUEST["orderid"])?$_REQUEST["orderid"]:'';
                    $order=Db::name('paylog')->where('out_trade_no',$out_trade_no)->find();
                    $this->dingding_log($order);
                    if($order['type']==1){
                        $up=[
                            'type'=>2,
                            'ftime'=>time()
                            ];
                            Db::startTrans();
                            try {
                                $this->money($order['amount'],$order['uid'],0,$sxf=0);
                                Db::name('paylog')->where('out_trade_no',$out_trade_no)->update($up);
                                Db::commit();
                                echo 'OK';
                            }catch (Exception $e){
                                Db::rollback();
                                $this->error($e->getMessage());
                            }
                    }else{
                        echo 'OK';
                    }
                }
            //});
    }
    public function money($money,$user_id,$id,$sxf=0){
        $user = $this->Usermodel::get($user_id);
        if ($user && $money != 0) {
            $before = $user->money;
            $after = $user->money + $money;
            //更新会员信息
            $user->save(['money' => $after]);
            //写入日志
            MoneyLog::create(['user_id' => $user_id, 'money' => $money, 'before' => $before, 'after' => $after,'fid' => $id, 'sxf' =>$sxf,'memo' => '用户充值']);
        }else{
           $this->error(__('金额不对')); 
        }
    }
    public function dingding_log($content)
    {
        $r = $_SERVER['DOCUMENT_ROOT'] . '/api_log/' . date('Y-m-d_H-i-s', time()) . '.txt';
        $fp = fopen($r, 'w+');
        fwrite($fp, "执行日期：" . date('Y-m-d H:i:s', time()) . ' ' . var_export($content, true));
        fclose($fp);
    }
    public function FromXml($xml)
    {
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
}
