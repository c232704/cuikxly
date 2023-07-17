<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\MoneyLog;
use app\common\model\ScoreLog;
use app\common\model\Txjl;
use app\common\model\Kami;

use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use think\Validate;
use think\Db;
/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = ['login', 'logingzh','getphone','getOpenid','mobilelogin', 'register','reg', 'resetpwd', 'changeemail', 'changemobile', 'third'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->Usermodel = model('User');
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $site=config('site');
        $data=$this->auth->getUserinfo();
        $UserGroup=model('UserGroup')->where(['id'=>$data['group_id']])->find();
        if(isset($UserGroup['name'])){
            $data['Groupname']=$UserGroup['name'];
        }else{
            $data['Groupname']='';
        }
        if($site){
            $data['config']=$site;
        }else{
            $data['config']=[];
        }
        $data['zstx']=$site['zstx']*1;
        if($data['dtime']<time() and $data['group_id']>1){
            $timediff = $data['dtime']-time();
            $data['dqts']=intval($timediff/86400);
        }else{
            $timediff = $data['dtime']-time();
            $data['dqts']=intval($timediff/86400);
        }
        $data['dtime']=date('Y-m-d',$data['dtime']);
        $this->success('',$data);
    }
    public function duihuan(){
        $site=config('site');
        $sum=input('sum');
        if(!input('sum')){
            $this->error('请填写提兑换数量');
        }
        if($sum<$site['jfdhzs']){
            $this->error($site['jfdhsm']);
        }
         Db::startTrans();
            try {
                $user = $this->auth->getUser();
                if ($user && $sum != 0) {
                    $before = $user->money;
                    if($user->money<$sum){
                        $this->error('积分不够');
                    }
                    $after = $user->score - $sum;
                    //更新会员信息
                    $user->save(['score' => $after]);
                    //写入日志
                    ScoreLog::create(['user_id' => $user->id, 'score' => $sum, 'before' => $before, 'after' => $after,'memo' => '积分兑换']);
                }else{
                   $this->error(__('金额不对')); 
                }
                $usersa = $this->auth->getUser();
                $money=$sum*$site['jfdh'];
                if ($usersa && $money != 0) {
                    $before = $usersa->money;
                    $after = $usersa->money + $money;
                    //更新会员信息
                    $usersa->save(['money' => $after]);
                    //写入日志
                    MoneyLog::create(['user_id' => $usersa->id, 'money' => $money, 'before' => $before, 'after' => $after,'fid' => 0, 'sxf' =>0,'memo' => '积分兑换']);
                }else{
                    $this->error(__('兑换错误')); 
                }
                Db::commit();
                $this->success(__('兑换成功'));
            }catch (Exception $e){
                Db::rollback();
                $this->error($e->getMessage());
            } 
    }
    public function mytuandui()
    {
        $configs=config('site');
        $user = $this->auth->getUser();
        $usera = \app\common\model\User::where(['pid'=>$this->auth->id])->select();
        foreach ($usera as $k=>$v){
                    	if(isset($v['avatar'])){
                    	    if(strpos($v['avatar'],'http') !== false){ 
                                $usera[$k]['avatar']=$v['avatar'];
                            }else{
                                if($v['avatar']){
                                    $usera[$k]['avatar']= $configs['imgurl'].$v['avatar'];
                                }else{
                                     $usera[$k]['avatar']=$configs['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                            } 
                    	}else{
                    	    $usera[$k]['avatar']=$configs['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                    	}
                    	
                        
                    }
        $this->success('success',$usera);
    }
    /**
     * 会员登录
     *
     * @param string $account  账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->request('account');
        $password = $this->request->request('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $userinfo=$this->auth->getUserinfo();
            $data = ['config'=>$this->config(),'userinfo' =>$userinfo ];
            
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 手机验证码登录
     *
     * @param string $mobile  手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('Account is locked'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret) {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     * @param string $code   验证码
     */
    public function register()
    {
        $username = $this->request->request('username');
        $password = $this->request->request('password');
        $email = $this->request->request('email');
        $mobile = $this->request->request('mobile');
        $code = $this->request->request('code');
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        if ($email && !Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $ret = Sms::check($mobile, $code, 'register');
        if (!$ret) {
            $this->error(__('Captcha is incorrect'));
        }
        $ret = $this->auth->register($username, $password, $email, $mobile, []);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }
    public function reg()
    {
        $username = $this->request->request('username');
        $password = $this->request->request('password');
        $email = $this->request->request('email');
        $mobile = $this->request->request('mobile');
        $code = $this->request->request('code');
        $pid = $this->request->request('pid');
        if(!$pid){
            $pid=1;
        }
        $usera = \app\common\model\User::getById($pid);
        if(!$usera){
            $this->error(__('邀请码不正确'));
        }
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        if(!$email){
            $email=$mobile.'@10068.com';
        }
        if ($email && !Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        
        // $ret =  Db::name('sms')->where(['mobile' => $mobile, 'event' => 'register'])
        //     ->order('id', 'DESC')
        //     ->find();
        // if (!$ret) {
        //     $this->error(__('Captcha is incorrect'));
        // }
        // if($ret['code']!=$code){
        //     $this->error(__('Captcha is incorrect'));
        // }
        
        // $ret = Sms::check($mobile, $code, 'register');
        // if (!$ret) {
        //     $this->error(__('Captcha is incorrect'));
        // }
        
        $avatar='/uploads/face/A'.rand(1, 96).'.jpg';
        $ret = $this->auth->register($username, $password, $email, $mobile, ['avatar'=>$avatar,'pid'=>$pid]);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }
    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }
    
    
    /**
     * 修改会员个人信息
     *
     * @param string $avatar   头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio      个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->request('username');
        $nickname = $this->request->request('nickname');
        $bio = $this->request->request('bio');
        $avatar = $this->request->request('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Username already exists'));
            }
            $user->username = $username;
        }
        $user->nickname = $nickname;
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();
        $this->success();
    }
    public function edituser()
    {
        $user = $this->auth->getUser();

        $nickname = $this->request->request('nickname');
        $birthday = $this->request->request('org');
        $gender = $this->request->request('sex');
        if($nickname){
            $user->nickname = $nickname;
            $user->save();
            $this->success();
        }
        if($birthday){
            $user->birthday = $birthday;
            $user->save();
            $this->success();
        }
        if($gender>=0){
            $user->gender = $gender; 
            $user->save();
            $this->success();
        }
         $this->error(__('修改失败'));
    }
    /**
     * 修改邮箱
     *
     * @param string $email   邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->request('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @param string $mobile   手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @param string $platform 平台名称
     * @param string $code     Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->request("platform");
        $code = $this->request->request("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo'  => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @param string $mobile      手机号
     * @param string $newpassword 新密码
     * @param string $captcha     验证码
     */
    public function resetpwd()
    {
        $type = $this->request->request("type");
        $mobile = $this->request->request("mobile");
        $email = $this->request->request("email");
        $newpassword = $this->request->request("newpassword");
        $captcha = $this->request->request("captcha");
        if (!$newpassword || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if ($type == 'mobile') {
            if (!Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('Mobile is incorrect'));
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error(__('Email is incorrect'));
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }
    public function tx(){
        $user = $this->auth->getUser();
        $site=config('site');
        $money=input('money');
        if($money<$site['zstx']){
          $this->error($site['txbz']);  
        }
        if(!input('type')){
            $this->error('请填写提现类型');
        }
        if(!input('name')){
            $this->error('请填写提现名字');
        }
        if(!input('cord')){
            $this->error('请填写提现帐号');
        }
        $user_id=$user['id'];
        
        if ($user && $money != 0) {
            if($user->money<$money){
               $this->error('余额不足'); 
            }
            $before = $user->money;
            $after = $user->money - $money;
            Db::startTrans();
            try {
                //更新会员信息
                $user->save(['money' => $after]);
                //写入日志
                MoneyLog::create(['user_id' => $user_id, 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => '用户提现']);
                $site['txsxf']=isset($site['txsxf'])?$site['txsxf']:0;
                $sxf=$money*$site['txsxf'];
                $up=[
                        'uid' => $user_id,
                        'type' => input('type'),
                        'name' => input('name'),
                        'cord' => input('cord'), 
                        'money' => $money, 
                        'moneydz' => $money-$sxf, 
                        'sxf' => $sxf,
                        'iscl' => 1,
                        'memo' => '用户提现'
                    ];
                //写入提现记录
                Txjl::create($up);    
                Db::commit();
                $this->success('提交成功');
            }catch (Exception $e){
                Db::rollback();
                $this->error($e->getMessage());
            }
            
        }else{
           $this->error(__('金额不对')); 
        }
    }
    public function txinfo()
    {
            $user = $this->auth->getUser();
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['uid']=$user['id'];
            $total = Txjl::where($where)->where($map)->order($sort, $order)->count();
            $list = Txjl::where($where)->where($map)->order($sort, $order)->limit($offset, $limit)->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['createtime']=date('Y-m-d',$v['createtime']);
                    }
                }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
    }
    public function userscoreinfo()
    {
            $user = $this->auth->getUser();
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['user_id']=$user['id'];
            $total = ScoreLog::where($where)->where($map)->order($sort, $order)->count();
            $list = ScoreLog::where($where)->where($map)->order($sort, $order)->limit($offset, $limit)->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
                        if($v['after']>$v['before']){
                          $list[$k]['jzt']='+'; 
                        }else if($v['after']==$v['before']){
                          $list[$k]['jzt']=''; 
                        }else{
                          $list[$k]['jzt']='-'; 
                        }
                    }
                }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
    }
    public function usermoneyinfo()
    {
            $user = $this->auth->getUser();
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['user_id']=$user['id'];
            $total = MoneyLog::where($where)->where($map)->order($sort, $order)->count();
            $list = MoneyLog::where($where)->where($map)->order($sort, $order)->limit($offset, $limit)->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
                        if($v['after']>$v['before']){
                          $list[$k]['jzt']='+'; 
                        }else if($v['after']==$v['before']){
                          $list[$k]['jzt']=''; 
                        }else{
                          $list[$k]['jzt']='-'; 
                        }
                    }
                }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
    }
    public function userfxinfo()
    {
            $user = $this->auth->getUser();
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['user_id']=$user['id'];
            $map['oid']=['>',0];
            $total = MoneyLog::where($where)->where($map)->order($sort, $order)->count();
            $list = MoneyLog::where($where)->where($map)->order($sort, $order)->limit($offset, $limit)->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
                        if($v['after']>$v['before']){
                          $list[$k]['jzt']='+'; 
                        }else if($v['after']==$v['before']){
                          $list[$k]['jzt']=''; 
                        }else{
                          $list[$k]['jzt']='-'; 
                        }
                    }
                }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
    }
    
    /**
     * 上传文件
     */
    public function upload()
    {
        return action('api/common/upload');
    }
    /**
     * 上传头像
     */
    public function avatar()
    {
        if(input('base64')){
            return action('api/common/base64_image_content');
        }
        return action('api/common/uploadavatar');
        
    }
    public function paysj()
    {
            $site=config('site');
            $arr=[];
            if(isset($site['vipsj'])){
                foreach ($site['vipsj'] as $k=>$v){
                    //一年(￥80) num:'10',coupon:0
                   $arr[]=['num'=>$k.'个月（￥'.$v.'）','coupon'=>$k];
                }  
            }
            $this->success('success',$arr);
    }
    public function taplove(){
        $up=[
                'uid'=>input('uid'),
                'vid'=>input('vid'),
                'mid'=>input('mid'),
                'vtime'=>0,
                'createtime'=>time(),
                'updatetime'=>time(),
        ];
            
        $videolove=Db::name('videolove')->where(['uid'=>input('uid'),'vid'=>input('vid'),'mid'=>input('mid')])->find();
        if($videolove){
            Db::name('videolove')->where(['id'=>$videolove['id']])->delete();
            Db::name('videolist')->where(['id'=>input('vid')])->setDec('give');
            $this->success('取消成功');
        }else{
            $id=Db::name('videolove')->insertGetId($up);
            Db::name('videolist')->where(['id'=>input('vid')])->setInc('give');
            $this->success('点赞成功'); 
        }
    }
    public function videorecord(){
        $up=[
                'uid'=>input('uid'),
                'vid'=>input('vid'),
                'mid'=>input('mid'),
                'vtime'=>0,
                'createtime'=>time(),
                'updatetime'=>time(),
            ];
            
        $videorecord=Db::name('videorecord')->where(['uid'=>input('uid'),'vid'=>input('vid'),'mid'=>input('mid')])->find();
        if($videorecord){
            Db::name('videorecord')->where(['id'=>$videorecord['id']])->delete();
            $this->success('取消成功');
        }else{
            $id=Db::name('videorecord')->insertGetId($up);
            $this->success('追剧成功'); 
        }
    }
    public function qingdao(){
        $times=date('Y-m-d',time());
        $qdjl=Db::name('qdjl')->where(['uid'=>input('uid'),'time'=>$times])->find();
        
        $yesterday=date("Y-m-d",strtotime("-1 day"));
        $yesterdaydata=Db::name('qdjl')->where(['uid'=>input('uid'),'time'=>$yesterday])->find();
        if($yesterdaydata){
           $sum= $yesterdaydata['sum']+1;
           $tdsy=$yesterdaydata['sy']+50;
           if($sum>=7){//连续7天
               $sum=1;
           }
        }else{
            $sum=1;
            $tdsy=100;
        }
        if($qdjl){
            $this->error('今日已经签到过了');
        }
        $up=[
            'uid'=>input('uid'),
            'sy'=>$tdsy,
            'sum'=>$sum,
            'time'=>$times,
        ];
        $this->qdmoney($sum);
        $id=Db::name('qdjl')->insertGetId($up);
        $this->success('签到成功');
    }
    public function vipsj()
    {
        $site=config('site');
        $num=input('num');
        if(!input('num')){
            $this->error('请填选择会员升级期限');
        }
        if(isset($site['vipsj'])){
           //var_dump();
           
            $money=$site['vipsj'][$num];
            $user = $this->auth->getUser();
            
            if($user->money<$money){
               $this->error('余额不足'); 
            }
            
            $before = $user->money;
            $after = $user->money - $money;
            Db::startTrans();
            try {
                //更新会员信息
                if($user->dtime-time()<=0 or $user->group_id==1){
                    $timea=strtotime("+".$num." months", time());
                }else{
                    $timea=strtotime("+".$num." months", $user->dtime);
                }
                $user->save(['money' => $after,'group_id' =>2,'stime'=>time(),'dtime' =>$timea]);
                //写入日志
                MoneyLog::create(['user_id' => $user['id'], 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => '会员升级']);
                $this->yongjin(1,$user['id'],$money,'级升级佣金结算');//佣金结算
                Db::commit();
                $this->success('升级成功',['config'=>$this->config(),'userinfo' => $this->auth->getUserinfo()]);
            }catch (Exception $e){
                Db::rollback();
                $this->error($e->getMessage());
            }
           
        } 
    }
    public function kami()
    {
            $crd=input('crd');
            if(!$crd){
                $this->error('请填写卡密');
            }
            $Kami=Kami::where(['crd'=>$crd])->find();
            if(!$Kami){
                $this->error('卡号不正确');
            }
            if($Kami['type']==2){
                $this->error('已经使用');
            }
            $money=$Kami['price'];
            $user = $this->auth->getUser();
            $before = $user->money;
            $after = $user->money + $money;
            Db::startTrans();
            try {
                //更新会员信息
                $user->save(['money' => $after]);
                //写入日志
                MoneyLog::create(['user_id' => $user['id'], 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => '卡密充值']);
                Kami::where(['crd'=>$crd])->update(['uid'=>$user['id'],'type'=>2,'stime'=>time()]);
                Db::commit();
                $data = ['config'=>$this->config(),'userinfo' => $this->auth->getUserinfo()];
                $this->success('充值成功',$data);
            }catch (Exception $e){
                Db::rollback();
                $this->error($e->getMessage());
            }
    }
    public function getOpenid() {
        $site=config('site');
        $site['dltel']=isset($site['dltel'])?$site['dltel']:-1;
        $code = input('code');//小程序传来的code值
        $appid =$site['weixinxcx']['appid'];//小程序的appid
        $appSecret = $site['weixinxcx']['appSecret'];// 小程序的$appSecret
        $wxUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
        $getUrl = sprintf($wxUrl, $appid, $appSecret, $code);//把appid，appsecret，code拼接到url里
        $result = $this->curl_get($getUrl);//请求拼接好的url
        $wxResult = json_decode($result, true);

        if (empty($wxResult)) {
            $this->error('获取openid时异常，微信内部错误');
        } else {
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {//请求失败
                $this->error($wxResult);
            } else {//请求成功
                $openid = $wxResult['openid'];
                if($site['dltel']==0){//等于0的时候直接登录
                    $users = \app\common\model\User::getByWxxopenid($openid);
                    if($users){
                        $this->auth->direct($users->id);
                        $data = ['openid'=>$openid,'userinfo' => $this->auth->getUserinfo(),'config'=>$this->config()];
                	    $this->success(__('登录成功'), $data);
                    }
                }
                $wxResult['dltel']=$site['dltel'];
                $this->success('获取openid成功',$wxResult);
            }
        }
    }
    public function logingzh(){
        $openid = input('openid');
        $unionid = input('unionid');
        $users = \app\common\model\User::getByUnionid($unionid);
        //$users = \app\common\model\User::getByGzhopenid($openid);//仅仅公众号
        if($users){
            if ($users->status != 'normal') {
        	    $this->error(__('Account is locked'));
        	}
        	//如果已经有账号则直接登录
        	$ret = $this->auth->direct($users->id);
        	if ($ret) {
                \app\common\model\User::where(['id'=> $users->id])->update(['gzhopenid'=>$openid]);
                $data = ['userinfo' => $this->auth->getUserinfo(),'config'=>$this->config()];
        	    $this->success(__('Logged in successful'), $data);
            }
        }else{
            $pid = $this->request->request('pid');
            if(!$pid){
                $pid=1;
            }
            $usera = \app\common\model\User::getById($pid);
            if(!$usera){
                $this->error(__('邀请码不正确'));
            }
            $mobile=$openid;
            $email=$mobile.'@100.com';
            $avatar='/uploads/face/A'.rand(1, 96).'.jpg';;
            $nickName=input('nickName','公众号用户');
            $ret = $this->auth->register($mobile, '123456', $email, $mobile, ['wxxopenid'=>$openid,'unionid'=>$unionid,'nickname'=>$nickName,'avatar'=>$avatar,'pid'=>$pid]);
            if ($ret) {
                $data = ['userinfo' => $this->auth->getUserinfo(),'config'=>$this->config()];
                $this->success(__('Sign up successful'), $data);
            } else {
                $this->error($this->auth->getError());
            }
        }
    }
    public function getphone()
    {
        $site=config('site');
        $dltel=isset($site['dltel'])?$site['dltel']:-1;
        if($dltel==0){
            $openid=input('openid');
            $users = \app\common\model\User::getByWxxopenid($openid);
            if($users){
                $this->auth->direct($users->id);
                $data = ['userinfo' => $this->auth->getUserinfo(),'config'=>$this->config()];
                $this->success(__('登录成功'), $data);
            }else{
                $pid = $this->request->request('pid');
                if(!$pid){
                    $pid=1;
                }
                $usera = \app\common\model\User::getById($pid);
                if(!$usera){
                    $this->error(__('邀请码不正确'));
                }
                //$number = date('ymdh', time()) . rand(10000, 99999);
                $mobile=$openid;
                $email=$mobile.'@100.com';
                $avatar=input('avatarUrl');
                $nickName=input('nickName');
                $ret = $this->auth->register($mobile, '123456', $email, $mobile, ['wxxopenid'=>$openid,'nickname'=>$nickName,'avatar'=>$avatar,'pid'=>$pid]);
                if ($ret) {
                    $users=$this->auth->getUserinfo();
                    //var_dump($users);
                    $idsa=$users['id'];
                    \app\common\model\User::where(['id'=> $idsa])->update(['mobile'=>'user_'.$idsa,'email'=>'user_'.$idsa.'@100.com','username'=>'user_'.$idsa]);
                    $data = ['userinfo' => $users,'config'=>$this->config()];
                    $this->success(__('Sign up successful'), $data);
                } else {
                    $this->error($this->auth->getError());
                }
            }
        }
        $APPID =$site['weixinxcx']['appid'];//小程序的appid
        $AppSecret = $site['weixinxcx']['appSecret'];// 小程序的$appSecret
        
		if(!input('code')){
			$this->error('code出错');
			exit;
		}
		$code=input('code');

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $APPID . "&secret=" . $AppSecret . "&js_code=" . $code . "&grant_type=authorization_code";
        $arr = $this->vget($url);  // 一个使用curl实现的get方法请求
        $arr = json_decode($arr, true);

        $session_key = $arr['session_key'];
        $openid = $arr['openid'];
        $unionid = isset($arr['unionid'])?$arr['unionid']:null;
        Vendor("wxBizData.wxBizDataCrypt");  //加载解密文件，在官方有下载
        $pc = new \WXBizDataCrypt($APPID, $session_key);
        $iv = input('iv');
        $encryptedData = input('encryptedData');
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        if ($errCode == 0) {
            //print($data . "\n");
            //dump($arr);
            $phone =  json_decode($data,true);
            if($phone['purePhoneNumber']){
		        //$users = \app\common\model\User::getByMobile($phone['purePhoneNumber']);
		        $user = \app\common\model\User::getByUnionid($unionid);
		        if ($user) {
		            if ($user->status != 'normal') {
		                $this->error(__('Account is locked'));
		            }
		            //如果已经有账号则直接登录
		            $ret = $this->auth->direct($user->id);
		            if ($ret) {
			            $data = ['userinfo' => $this->auth->getUserinfo(),'config'=>$this->config()];
			            \app\common\model\User::where(['id'=> $user->id])->update(['mobile'=>$phone['purePhoneNumber'],'wxxopenid'=>$openid,'unionid'=>$unionid]);
			            $this->success(__('Logged in successful'), $data);
		        	}
		        }else{
		                $users = \app\common\model\User::getByMobile($phone['purePhoneNumber']);
		                if($users){
		                    if ($users->status != 'normal') {
        		                $this->error(__('Account is locked'));
        		            }
        		            //如果已经有账号则直接登录
        		            $ret = $this->auth->direct($users->id);
        		            if ($ret) {
        			            $data = ['userinfo' => $this->auth->getUserinfo(),'config'=>$this->config()];
        			            \app\common\model\User::where(['id'=> $users->id])->update(['mobile'=>$phone['purePhoneNumber'],'wxxopenid'=>$openid,'unionid'=>$unionid]);
        			            $this->success(__('Logged in successful'), $data);
        		        	}
		                }else{
		                    $pid = $this->request->request('pid');
                            if(!$pid){
                                $pid=1;
                            }
                            $usera = \app\common\model\User::getById($pid);
                            if(!$usera){
                                $this->error(__('邀请码不正确'));
                            }
                            $mobile=$phone['purePhoneNumber'];
                            $email=$mobile.'@100.com';
                            $avatar=input('avatarUrl');
                            $nickName=input('nickName');
                            $ret = $this->auth->register($mobile, '123456', $email, $mobile, ['wxxopenid'=>$openid,'unionid'=>$unionid,'nickname'=>$nickName,'avatar'=>$avatar,'pid'=>$pid]);
                            if ($ret) {
                                $data = ['userinfo' => $this->auth->getUserinfo(),'config'=>$this->config()];
                                $this->success(__('Sign up successful'), $data);
                            } else {
                                $this->error($this->auth->getError());
                            }
		                }
		        }
            }
        }else{
        	 $this->error($errCode);
        }
    }
    public function qdmoney($money){
        $site=config('site');
        $user = $this->auth->getUser();
        if ($user && $money != 0) {
            $before = $user->money;
            $after = $user->money + $money;
            //更新会员信息
            $user->save(['score' => $after]);
            //写入日志
            ScoreLog::create(['user_id' => $user->id, 'score' => $money, 'before' => $before, 'after' => $after,'memo' => '签到赠送']);
        }else{
           $this->error(__('金额不对')); 
        }
    }
    public function admoney(){
        $site=config('site');
        $money=isset($site['admoney'])?$site['admoney']:0.01;
        $user = $this->auth->getUser();
        if ($user && $money != 0) {
            $before = $user->money;
            $after = $user->money + $money;
            //更新会员信息
            $user->save(['money' => $after]);
            //写入日志
            MoneyLog::create(['user_id' => $user->id, 'money' => $money, 'before' => $before, 'after' => $after,'fid' => 0, 'sxf' =>0,'memo' => '看广告赠送']);
            $this->success('获取成功',$this->config());
        }else{
           $this->error(__('金额不对')); 
        }
    }
     /**
     * @Author     : kiven
     * @Description:curl模拟请求方法
     * @LastModify : kiven
     * @param $url
     * @return bool|string
     */
    public function vget($url){
        $info=curl_init();
        curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($info,CURLOPT_HEADER,0);
        curl_setopt($info,CURLOPT_NOBODY,0);
        curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($info,CURLOPT_URL,$url);
        $output= curl_exec($info);
        curl_close($info);
        return $output;
    }
    //php请求网络的方法
    public function curl_get($url, &$httpCode = 0) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
        //不做证书校验,部署在linux环境下请改为true
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }


}
