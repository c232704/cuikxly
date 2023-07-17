<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;
use think\Db;
/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Xxts extends Backend
{

    protected $relationSearch = true;


    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Xxts');
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }
    public function tuis($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->view->assign('row',$row);
        return $this->view->fetch();
    }
    public function tuisax()
    {
        $tdata=$this->model->where(['id'=>input('id')])->find();
        Db::startTrans();
            try {
            $this->model->where(['id'=>$tdata['id']])->update(['type'=>2]);
            $list= model('User')->where(['status'=>'normal'])->select();
            foreach ($list as $v){
                if($v['wxxopenid']){
                    $url=$tdata['url'].'?id='.$tdata['urlid'];
                    $this->pushmsg($v['wxxopenid'],$tdata['info'],$tdata['name'],$url); 
                }
            }
            Db::commit();
            $this->success('推送成功');
        }catch (Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }
    }
    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        return parent::edit($ids);
    }
    public function add()
    {
        return parent::add();
    }
    //$uid 用户id $zt提醒状态 $title项目标题 $beizhu备注 $tjtime提交时间 $shtime审核时间 $id跳转id  $xcxurl跳转地址
    public function pushmsg($wxxopenid,$info,$name,$url)
    {
        $site=config('site');
        $appid =$site['weixinxcx']['template_id'];
        $touser=$wxxopenid;
        $page=isset($url)?$url:'pages/client/index';
        $template_id=$appid; //提醒
        $content=[
                "thing1"=>['value' => $info],
                "thing2"=>['value' => $name],
                "thing4"=>['value' => 'admin'],

        ];
        $rdd=self::sendSubscribeMessage($touser,$template_id,$page,$content);
        $arr = json_decode($rdd,true);
        $errcode=isset($arr['errcode'])?$arr['errcode']:1;
        if($arr['errcode']>0){
            $errmsg=isset($arr['errmsg'])?$arr['errmsg']:'1';
            $this->error(__($arr['errmsg']));
        }
    }
    //发送订阅消息
    public function sendSubscribeMessage($touser,$template_id,$page,$content)
    {
        //access_token
        $access_token = self::getAccessToken() ;
        //请求url
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $access_token ;
        //发送内容
        $data = [] ;
        //接收者（用户）的 openid
        $data['touser'] = $touser ;
        //所需下发的订阅模板id
        $data['template_id'] = $template_id ;
        //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
        $data['page'] = $page ;
        //模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
        $data['data'] = $content;
        //跳转小程序类型：developer为开发版；trial为体验版；formal为正式版；默认为正式版
        $data['miniprogram_state'] = 'formal' ;
        return self::curlPost($url,json_encode($data)) ;
    }
    /**
     * Notes:获取accessToken
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function getAccessToken()
    {
        $site=config('site');
        $appid =$site['weixinxcx']['appid'];//小程序的appid
        $appSecret = $site['weixinxcx']['appSecret'];// 小程序的$appSecret
        //当前时间戳
        $now_time = strtotime(date('Y-m-d H:i:s',time())) ;
        //失效时间
        $timeout = 7200 ;
        //判断access_token是否过期
        $before_time = $now_time - $timeout ;
        //未查找到就为过期
        $access_token = Db::name('takeout_access_token')->where('id',1)
            ->where('update_time' ,'>',$before_time)
            ->value('access_token');
        //如果过期
        if( !$access_token ) {
            //获取新的access_token
            $appid  = $appid;
            $secret = $appSecret;
            $url    = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
            $res = json_decode(file_get_contents($url),true);
            $access_token = $res['access_token'] ;
            //更新数据库
            $update = ['access_token' => $access_token ,'update_time' => $now_time] ;
            Db::name('takeout_access_token')->where('id',1)->update($update) ;
        }
        return $access_token ;
    }
    //发送post请求
    protected function curlPost($url,$data)
    {
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = FALSE; //是否返回响应头信息
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = $data;
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }

}
