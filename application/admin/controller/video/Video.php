<?php

namespace app\admin\controller\video;

use app\common\controller\Backend;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Video extends Backend
{

    protected $relationSearch = true;


    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Video');
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
            $map=[];
            if(input('pid')){
               $map['video.pid']= input('pid');
            }
            $total = $this->model
                ->with('videolist')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('videolist')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL,$pid = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        if($row['lx']==1){
            $lx='video/mp4';
            $lxname='视频';
        }elseif ($row['lx']==2) {
            $lx='audio/mpeg';
            $lxname='音频';
        }elseif ($row['lx']==3){
            $lx='wenz';
            $lxname='文章';
        }else {
            $lx='';
            $lxname='';
        }
        $this->view->assign('lx', $lx);
        $this->view->assign('lxname', $lxname);
        
        
        $Videolist = model('Videolist')->get($pid);
        $this->view->assign('Videolist', $Videolist);
        return parent::edit($ids);
    }
    public function getsignature(){
        
        $site=config('site');
        $secret_id = $site['txydbapi']['secret_id'];
        $secret_key = $site['txydbapi']['secret_key'];
        
        // 确定签名的当前时间和失效时间
        $current = time();
        $expired = $current + 86400;  // 签名有效期：1天
        
        // 向参数列表填入参数
        $arg_list = array(
            "secretId" => $secret_id,
            "currentTimeStamp" => $current,
            "expireTime" => $expired,
            "random" => rand());
        
        // 计算签名
        $original = http_build_query($arg_list);
        $signature = base64_encode(hash_hmac('SHA1', $original, $secret_key, true).$original);
        echo json_encode(['code' => '1', 'signature' => $signature]);
    }
    public function add($pid = NULL)
    {
        
        $Videolist = model('Videolist')->get($pid);
        if($Videolist['lx']==1){
            $lx='video/mp4';
            $lxname='视频';
        }elseif ($Videolist['lx']==2) {
            $lx='audio/mp3';
            $lxname='音频';
        }elseif ($Videolist['lx']==3){
            $lx='wenz';
            $lxname='文章';
        }else {
            $lx='';
            $lxname='';
        }
        $this->view->assign('lx', $lx);
        $this->view->assign('lxname', $lxname);
        $this->view->assign('Videolist', $Videolist);
        if($Videolist){
           return parent::add(); 
        }else{
            echo '请到视频管理里添加';
        }
        
    }

}
