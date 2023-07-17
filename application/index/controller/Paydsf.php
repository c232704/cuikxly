<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;
class Paydsf extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        $site=config('site');
        $out_trade_no=input('out_trade_no');
        if(!$out_trade_no){
            $this->error(__('找不到数据'));
        }
        $paylog=Db::name('paylog')->where('out_trade_no',$out_trade_no)->find();
        //dump($paylog);
        if(!$paylog){
            $this->error(__('找不到数据'));
        }
        
        // $number = date('ymdh', time()) . rand(10000, 99999);//订单编
        // Db::name('paylog')->where('out_trade_no',$out_trade_no)->update(['out_trade_no'=>$number]);
        $bankcode=!empty($paylog['tdnum'])?$paylog['tdnum']:$site['dsfzf']['bankcode'];//通道编码
        $pay_memberid = $site['dsfzf']['memberid'];   //商户ID
        $pay_orderid = $paylog['out_trade_no'];    //订单号
        $pay_amount =$paylog['amount'];    //交易金额
        $pay_applydate = date("Y-m-d H:i:s");  //订单时间
        $pay_notifyurl = $site['dsfzf']['notifyurl'];   //服务端返回地址
        $pay_callbackurl = $site['dsfzf']['callbackurl'];  //页面跳转返回地址
        $Md5key = $site['dsfzf']['key'];   //密钥
        $tjurl = $site['dsfzf']['tjurl'];   //提交地址
        $pay_bankcode = $bankcode;   //通道编码
        //扫码
        $native = array(
            "pay_memberid" => $pay_memberid,
            "pay_orderid" => $pay_orderid,
            "pay_amount" => $pay_amount,
            "pay_applydate" => $pay_applydate,
            "pay_bankcode" => $pay_bankcode,
            "pay_notifyurl" => $pay_notifyurl,
            "pay_callbackurl" => $pay_callbackurl,
        );
      
        ksort($native);
        $md5str = "";
        foreach ($native as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        
        $sign = strtoupper(md5($md5str . "key=" . $Md5key));
        $native["pay_md5sign"] = $sign;
        $native['pay_attach'] = "1234|456";
        $native['pay_productname'] = '用户充值';
        $native['type'] = "json"; //json  或  html
		//die();
        $postData = http_build_query($native);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $tjurl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded;charset:utf-8;'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($data,true);
        //var_dump($data);
         if($json['status'] =='1'){
			 $url = $json['payUrl'];
			 header("Location:$url");die;
		 }else{
			 exit($data);
		 }
    }
    public function wxh5(){
        $site=config('site');

        $out_trade_no=input('out_trade_no');
        if(!$out_trade_no){
            $this->error(__('找不到数据'));
        }
        $paylog=Db::name('paylog')->where('out_trade_no',$out_trade_no)->find();
        //dump($paylog);
        if(!$paylog){
            $this->error(__('找不到数据'));
        }
        
        $key=$site['wxpay']['key'];
        $data=[
            'appid'=>$site['weixinh5']['appid'],
            'mch_id'=>$site['wxpay']['mch_id'],//账户号
            'nonce_str'=>$this->getNonceStr(),//随机字符串，不长于32位
            //'sign'=>'',//签名
            'body'=>'测试商品',//商品描述
            'out_trade_no'=>$paylog['out_trade_no'],//商户订单号，不长于32位
            'total_fee'=>$paylog['amount']*100,//总金额，以分为单位
            'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],//用户端请求支付时的IP
            'notify_url'=>$site['imgurl'].'/api/paywx/notifwxh5',//异步通知回调地址，必须是可直接访问地址，不能携带参数
            'trade_type'=>'MWEB',
            ];
        $data['sign']=$this->genSign($data, $key);
        $params=$this->array_to_xml($data);
        $request=$this->postda($params);
        
        //$objectxml = simplexml_load_string($request);//将文件转换成 对象
        $objectxml = simplexml_load_string($request,"SimpleXMLElement", LIBXML_NOCDATA);
        $xmljson= json_encode($objectxml );//将对象转换个JSON
        $xmlarray=json_decode($xmljson,true);//将json转换成数组
        $mweb_url=isset($xmlarray['mweb_url'])?$xmlarray['mweb_url']:'';
       
        if($mweb_url){
            $redirect_url=$site['imgurl'].'/index/paydsf/zfcg?out_trade_no='.$paylog['out_trade_no'];
            $this->view->assign('mweb_url', $mweb_url.'&redirect_url='.$redirect_url);
            return $this->view->fetch();
        }else{
            $return_msg=isset($xmlarray['return_msg'])?$xmlarray['return_msg']:'';
            echo $return_msg;
            //$this->error($return_msg);
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
    public function zfcg(){
        
        if ($this->request->isPost()) {
            $paylogs=Db::name('paylog')->where('id',input('id'))->find();
            $ispia=0;
            if($paylogs){
                echo json_encode($paylogs);
                die;
            }
        }
        
        $site=config('site');
        $out_trade_no=input('out_trade_no');
        if(!$out_trade_no){
            $this->error(__('找不到数据1'));
        }
        $paylog=Db::name('paylog')->where('out_trade_no',$out_trade_no)->find();
        //dump($paylog);
        if(!$paylog){
            $this->error(__('找不到数据2'));
        }
        $this->assign('paydata', $paylog);
        return $this->fetch();
 
        
    }

}