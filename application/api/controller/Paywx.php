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
class Paywx extends Api
{
    protected $noNeedLogin = ['notifwxh5','wxh5','wxxcx'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->Usermodel = model('User');
    }
    public function paywxh5(){
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
        $iswx=input('iswx');
        $buytype='微信h5';
        if($iswx=='wxgzh'){
            $buytype='微信公众号';
        }
        $up=[
                'type'=>1,
                'uid'=>$user['id'],
                'tdnum'=>input('tdnum'),//支付通道
                'amount'=>$amount,
                'out_trade_no'=>$number,
                'createtime'=>time(),
                'buytype'=>$buytype,
            ];
        $id=Db::name('paylog')->insertGetId($up);
        $urls=$site['imgurl'].'/index/paydsf/wxh5?out_trade_no='.$number;
        if($iswx=='wxgzh'){
            $urls=$site['imgurl'].'/index/index/pay?id='.$id;
        }
        $this->success('订单提交成功 正在跳转支付',$urls);
    }
    
    public function notifwxh5(){
        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');
        $xmlData = file_get_contents('php://input');
        
        $data = $this->FromXml($xmlData);
        $this->dingding_log($data);
        $result_code=isset($data['result_code'])?$data['result_code']:'1';
        if($result_code == "SUCCESS"){//处理交易完成或者支付成功的通知 //获取订单号
                    $out_trade_no = isset($data["out_trade_no"])?$data["out_trade_no"]:'';
                    $trade_no = isset($data["transaction_id"])?$data["transaction_id"]:'';
                    $order=Db::name('paylog')->where('out_trade_no',$out_trade_no)->find();
                    //$this->dingding_log($order);
                    if($order['type']==1){
                            Db::startTrans();
                            try {
                                
                                if($order['oid']>0){
                                    $ordervd=Db::name('video_order')->where('id',$order['oid'])->find();
                                    if($ordervd){
                                        Db::name('video_order')->where('id',$order['oid'])->update(['type'=>2,'paytype'=>'微信','ftime'=>time()]);
                                        $this->yongjin($order['oid'],$order['uid'],$order['amount'],'级消费佣金结算');//佣金结算
                                        $this->dailiyongjin($order['oid'],$ordervd['sid'],$ordervd['smid'],$order['uid'],$order['amount'],'佣金结算');//后台代理佣金结算
                                    }
                                }else{
                                    $this->money($order['amount'],$order['uid'],0,$sxf=0);
                                }
                                $up=['type'=>2,'trade_no'=>$trade_no,'ftime'=>time()];
                                Db::name('paylog')->where('out_trade_no',$out_trade_no)->update($up);
                                Db::commit();
                                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                            }catch (Exception $e){
                                Db::rollback();
                                $this->error($e->getMessage());
                            }
                    }else{
                        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    }
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
    public function paywxxcx(){
        $site=config('site');
        $user = $this->auth->getUser();
        if(!$user){
           $this->error(__('user不对'));  
        }
        $number = date('ymdh', time()) . rand(10000, 99999);//订单编
        $amount=input('total',0);
        $openid=input('openid',0);
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
            ];
        $id=Db::name('paylog')->insertGetId($up);
        $trs=$this->wxxcx($number,$amount,$openid);
        $this->success('提交成功',$trs);
    }
    public function wxxcx($number,$amount,$openid){
        $site=config('site');
        $appid =$site['weixinxcx']['appid'];//小程序的appid
        $key=$site['wxpay']['key'];
        $data=[
            'appid'=>$appid,
            'mch_id'=>$site['wxpay']['mch_id'],//账户号
            'nonce_str'=>$this->getNonceStr(),//随机字符串，不长于32位
            'body'=>'测试商品',//商品描述
            'out_trade_no'=>$number,//商户订单号，不长于32位
            'total_fee'=>$amount*100,//总金额，以分为单位
            'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],//用户端请求支付时的IP
            'notify_url'=>$site['imgurl'].'/api/paywx/notifwxh5',//异步通知回调地址，必须是可直接访问地址，不能携带参数
            // 'timeStamp'=> time(),
            'openid' => $openid, //用户id
            'trade_type'=>'JSAPI',

            ];
            
        $data['sign']=$this->genSign($data, $key);
        $params=$this->array_to_xml($data);
        //var_dump($params);
        $request=$this->postda($params);
        
        //$objectxml = simplexml_load_string($request);//将文件转换成 对象
        $objectxml = simplexml_load_string($request,"SimpleXMLElement", LIBXML_NOCDATA);
        $xmljson= json_encode($objectxml );//将对象转换个JSON
        $xmlarray=json_decode($xmljson,true);//将json转换成数组
        $prepay_id=isset($xmlarray['prepay_id'])?$xmlarray['prepay_id']:'';
        
        $parameters1 = array(
            'appId' => $appid, //小程序 ID
            'timeStamp' => '' . time() . '', //时间戳
            'nonceStr' => $this->getNonceStr(), //随机串
            'package' => 'prepay_id=' . $prepay_id, //数据包
            'signType' => 'MD5'//签名方式
        );
        $parameters1['sign'] = $this->genSign($parameters1,$key);//签名
        $parameters1['timeStamp']=$parameters1['timeStamp'];
        if($prepay_id){
            return $parameters1;
        }else{
            $this->error($xmlarray);
        }
        
    }
    
    /**
	 * 
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public function getNonceStr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
	}
      /**
   * 组建签名
   * @param array $params 请求参数
   * @param string $key 秘钥
   */
    public function genSign($params, $key)
      {
        foreach ($params as $k=>$v) {
          if (!$v) {
            unset($params[$k]);
          }
        }
        ksort($params);
        $paramStr = '';
        foreach ($params as $k => $v) {
          $paramStr = $paramStr . $k . '=' . $v . '&';
        }
        $paramStr = $paramStr . 'key='.$key;
        $sign = strtoupper(md5($paramStr));
        return $sign;
      }

      /**
   * 将数组转为XML
   * @param array $params 支付请求参数
   */
  public function array_to_xml($params)
  {
    if(!is_array($params)|| count($params) <= 0) {
      return false;
    }
    $xml = "<xml>";
    foreach ($params as $key=>$val) {
      if (is_numeric($val)) {
        $xml.="<".$key.">".$val."</".$key.">";
      } else {
        $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
      }
    }
    $xml.="</xml>";
    return $xml;
  }
    public function postda($params){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.mch.weixin.qq.com/pay/unifiedorder');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;

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
