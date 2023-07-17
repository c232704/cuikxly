<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;
class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        return $this->view->fetch();
    }
    public function login()
    {
        $id=input('id',1);
        if(!$id){
            $this->error(__('邀请码不对')); 
        }
        $appid = 'wx2a2d24fbe2383604';
        $cmurl = "jy.chengwuwa.cn/index/index/gzhop/id/".$id;//接口地址
        #你的公众号appid
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=https%3a%2f%2f".$cmurl."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        //$url='/login.html';
        #redirect_uri改为你的网页授权域名和刚刚跳转到的显示页面，比如我的是getinfoDetail.php
        header('location:'.$url);
    }
    public function gzhop()
    {
        $ids=input('id',1);
        if(!$ids){
            $this->error(__('邀请码不对')); 
        }
        $appid = "wx2a2d24fbe2383604"; 
    	$secret = "07b59b4bac2f17edc9e121e3dba6d0b3"; 
    	$cmurl = "https://jy.chengwuwa.cn/d/index.html";//前端地址
    	$code = $_GET["code"]; 
    	$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
    	$ch = curl_init();
    	curl_setopt($ch,CURLOPT_URL,$get_token_url); 
    	curl_setopt($ch,CURLOPT_HEADER,0); 
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
    	$res = curl_exec($ch); 
    	curl_close($ch); 
    	$json_obj = json_decode($res,true); 
    	//根据openid和access_token查询用户信息 
    	$access_token = $json_obj['access_token']; 
    	$openid = $json_obj['openid']; 
    	$get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN'; 
    	 
    	$ch = curl_init(); 
    	curl_setopt($ch,CURLOPT_URL,$get_user_info_url); 
    	curl_setopt($ch,CURLOPT_HEADER,0); 
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
    	$res = curl_exec($ch); 
    	curl_close($ch); 
    	 
    	//解析json 
    	$user_obj = json_decode($res,true);
    	$openid=isset($user_obj['openid'])?$user_obj['openid']:null;
    	$unionid=isset($user_obj['unionid'])?$user_obj['unionid']:null;
    	$nikname=isset($user_obj['nickname'])?$user_obj['nickname']:null;
    	$headimgurl=isset($user_obj['headimgurl'])?$user_obj['headimgurl']:null;
    	$userfxid=Db::name('userfxid')->where('unionid',$unionid)->find();
    	if($userfxid){
    	    Db::name('userfxid')->where('unionid',$unionid)->update(['fid'=>$ids,'openid'=>$openid,'unionid'=>$unionid]);
    	}else{
    	    Db::name('userfxid')->insertGetId(['fid'=>$ids,'openid'=>$openid,'unionid'=>$unionid]);
    	}
    	$urls=$cmurl.'?iswx=1';
    	header('location:'.$urls);
    }
    public function pay()
    {
        $site=config('site');
        require_once $_SERVER['DOCUMENT_ROOT']."/wxpay/lib/WxPay.Api.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/wxpay/example/WxPay.JsApiPay.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/wxpay/example/WxPay.Config.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/wxpay/example/log.php";

//初始化日志
        $logHandler= new \CLogFileHandler($_SERVER['DOCUMENT_ROOT']."/wxpay/logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

//打印输出数组信息
        function printf_info($data)
        {
            foreach($data as $key=>$value){
                echo "<font color='#00ff55;'>$key</font> :  ".htmlspecialchars($value, ENT_QUOTES)." <br/>";
            }
        }
        $data=[];
        $jsApiParameters=[];
        $editAddress=[];
        if(input('id',0)>0){
            $data=Db::name('paylog')->where('id',input('id'))->find();
            if($data){
                if($data['type']==2){
                    $this->error(__('已经购买')); 
                }
            }else{
                $this->error(__('查询补单订单信息')); 
            }
            $data['member']=Db::name('user')->where('id',$data['uid'])->find();
        }
        $notifyurl=$site['imgurl'].'/api/paywx/notifwxh5';
        $Total=isset($data['amount'])?$data['amount']*100:0.01;
        $data['type']=isset($data['type'])?$data['type']:0;
        if($data['type']==1){
            try{
                $tools = new \JsApiPay();
                $openId = $tools->GetOpenid();
                $jsApiParameters=[];
                $editAddress=[];
                if($openId){
                    //②、统一下单
                    $input = new \WxPayUnifiedOrder();
                    $input->SetBody("商城订单");
                    $input->SetAttach("商城订单");
                    $input->SetOut_trade_no($data['out_trade_no']);
                    $input->SetTotal_fee($Total);
                    $input->SetTime_start(date("YmdHis"));
                    $input->SetTime_expire(date("YmdHis", time() + 600));
                    $input->SetGoods_tag("商城订单");
                    $input->SetNotify_url($notifyurl);
                    $input->SetTrade_type("JSAPI");
                    $input->SetOpenid($openId);
                    $config = new \WxPayConfig();
                    $order = \WxPayApi::unifiedOrder($config, $input);
                    //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
                    //printf_info($order);
                    $jsApiParameters = $tools->GetJsApiParameters($order);
                    //获取共享收货地址js函数参数
                    $editAddress = $tools->GetEditAddressParameters();
                }
            } catch(Exception $e) {
                //Log::ERROR(json_encode($e));
            }
        }else{
            
        }
        $zt=['1'=>'未付款','2'=>'已付款'];
        $this->assign('jsApiParameters', $jsApiParameters);
        $this->assign('editAddress', $editAddress);
        $this->assign('data', $data);
        $this->assign('url',$site['imgurl']);
        $this->assign('zt', $zt);
        //var_dump($data);
        if($jsApiParameters and $data){
            return $this->fetch();
        }else{
            //header('Location: '.$data['url'].'/index.php/shop/pay/index?id='.input('id').'&');
        }
    }
}