<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems as Emslib;
use app\common\model\User;

/**
 * 邮箱验证码接口
 */
class Ems extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        \think\Hook::add('ems_send', function ($params) {
            $obj = \app\common\library\Email::instance();
            $result = $obj
                ->to($params->email)
                ->subject('验证码')
                ->message("你的验证码是：" . $params->code)
                ->send();
            return $result;
        });
    }
    public function index()
    {
        if($_POST){
            
            
            
            $mobile=input('phone');
            $code=input('code',rand(1000, 9999)) ;
            
            $site=config('site');
            if(!$site['dxjk']['key']){
                echo json_encode(['ok' => '1','data'=>$code,'msg' => '短信模拟发送成功 验证码：'.$code]);
                exit();
            }
            $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
            $smsConf = array(
                'key'   => $site['dxjk']['key'],//'f5cd3c466161918a823ed1c5d58cec58', //您申请的APPKEY
                'mobile'    => $mobile, //接受短信的用户手机号码
                'tpl_id'    => $site['dxjk']['tpl_id'], //您申请的短信模板ID，根据实际情况修改
                'tpl_value' =>'#code#='.$code //您设置的模板变量，根据实际情况修改
            );
            $content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
            if($content){
                $result = json_decode($content,true);
                $error_code = $result['error_code'];
                if($error_code == 0){
                    //状态为0，说明短信发送成功
                    //Cookie::set('yzmcode',md5($code));//验证码保存到SESSION中
                    $sms = \app\common\model\Sms::where(['mobile' => $mobile])->delete();
                    $sms = \app\common\model\Sms::create(['event' => 'register', 'mobile' => $mobile, 'code' => $code, 'ip' => request()->ip(), 'createtime' => time()]);
                    echo json_encode(['ok' => '1','data'=>$code,'msg' => '短信发送成功']);
                    //echo json_encode(['ok' => '1','data'=>md5($code.'6db'),'msg' => '短信发送成功,短信ID：'.$result['result']['sid']]);
                }else{
                    //状态非0，说明失败
                    $msg = $result['reason'];
                    echo json_encode(['ok' => '2','data'=>md5($code), 'msg' => '短信发送失败('.$error_code.')：'.$msg]);
                }
            }else{
                //返回内容异常，以下可根据业务逻辑自行修改
                echo json_encode(['ok' => '2','data'=>md5($code), 'msg' => '请求发送短信失败']);
            }
        }

    }
    public function dxb(){
        $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
        );
        
        $mobile=input('phone');
        $code=input('code',rand(1000, 9999)) ;
        
        $smsapi = "http://api.smsbao.com/";
        $user = "zhangbin1"; //短信平台帐号
        $pass = md5("zhangbin12"); //短信平台密码
        $content="【咕咕网络】您的验证码是".$code."。如非本人操作，请忽略本短信";//要发送的短信内容
        $phone = $mobile;//要发送短信的手机号码
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl) ;
        if($result==0){
            $sms = \app\common\model\Sms::where(['mobile' => $mobile])->delete();
            $sms = \app\common\model\Sms::create(['event' => 'register', 'mobile' => $mobile, 'code' => $code, 'ip' => request()->ip(), 'createtime' => time()]);
            echo json_encode(['ok' => '1','data'=>$code,'msg' => '短信发送成功']);
        }else{
            echo json_encode(['ok' => '1','data'=>$code,'msg' => $statusStr[$result]]);
        }
    }
    
    /**
     * 发送验证码
     *
     * @param string $email 邮箱
     * @param string $event 事件名称
     */
    public function send()
    {
        $email = $this->request->request("email");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';

        $last = Emslib::get($email, $event);
        if ($last && time() - $last['createtime'] < 60) {
            $this->error(__('发送频繁'));
        }
        if ($event) {
            $userinfo = User::getByEmail($email);
            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error(__('已被注册'));
            } elseif (in_array($event, ['changeemail']) && $userinfo) {
                //被占用
                $this->error(__('已被占用'));
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error(__('未注册'));
            }
        }
        $ret = Emslib::send($email, null, $event);
        if ($ret) {
            $this->success(__('发送成功'));
        } else {
            $this->error(__('发送失败'));
        }
    }

    /**
     * 检测验证码
     *
     * @param string $email   邮箱
     * @param string $event   事件名称
     * @param string $captcha 验证码
     */
    public function check()
    {
        $email = $this->request->request("email");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';
        $captcha = $this->request->request("captcha");

        if ($event) {
            $userinfo = User::getByEmail($email);
            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error(__('已被注册'));
            } elseif (in_array($event, ['changeemail']) && $userinfo) {
                //被占用
                $this->error(__('已被占用'));
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error(__('未注册'));
            }
        }
        $ret = Emslib::check($email, $captcha, $event);
        if ($ret) {
            $this->success(__('成功'));
        } else {
            $this->error(__('验证码不正确'));
        }
    }
    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }
}
