<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;

/**
 * 会员接口
 */
class Order extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('TaskOrder');
    }

    public function Index()
    {
        $config=config('site');
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['uid']=input('uid',0);
            if(input('stype',0)>0){
                $map['stype']=input('stype');
            }
            $map['isqx']=1;
            $total = $this->model
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $subtime=0;
                        $Task=model('Task')->where(['id'=>$v['oid']])->find();
                        if($Task){
                            $subtime=$this->getsubtime($Task['subtime']);
                            $subtime=$v['createtime']+$subtime-time();
                        }
                        $list[$k]['subtime']=$subtime;
                        $list[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
                        $User=model('User')->where(['id'=>$v['fid']])->find();
                    	if(isset($User['avatar'])){
                    	    if(strpos($User['avatar'],'http') !== false){ 
                                $list[$k]['avatar']=$User['avatar'];
                            }else{
                                if($User['avatar']){
                                    $list[$k]['avatar']= $config['imgurl'].$User['avatar'];
                                }else{
                                     $list[$k]['avatar']=$list[$k]['img'];
                                }
                            } 
                    	}else{
                    	    $list[$k]['avatar']=$list[$k]['img'];
                    	}
                    	
                        
                    }
                }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
    }
    public function orderinfo(){
            $config=config('site');
            //var_dump($where);
            $list = model('TaskOrder')
                ->where(['id'=>input('id')])
                ->find();
                if($list){
                    $subtime=0;
                    $shetime=0;
                    $Task=model('Task')->where(['id'=>$list['oid']])->find();
                    if($Task){
                        $subtime=$this->getsubtime($Task['subtime']);
                        $subtime=$list['createtime']+$subtime-time();
                        
                        $shetime=$this->getshetime($Task['shetime']);
                        $shetime=$list['ttime']+$shetime-time();
                    }
                    $list['subtime']=$subtime;//提交时间
                    $list['shetime']=$shetime;//审核时间
                    
                    $list['createtime']=date('Y-m-d H:i',$list['createtime']);
 
                }
            return json($list);
    }
    public function taskinfosh(){
        $config=config('site');
            //var_dump($where);
            $TaskOrder=model('TaskOrder')->where(['id'=>input('id',0)])->find();
            $list = model('Task')->where(['id'=>$TaskOrder['oid']])->find();
                if($list){
                    $odcount=model('TaskOrder')->where(['oid'=>$list['id'],'uid'=>input('uid',0)])->count();
                    if($TaskOrder){
                        if($TaskOrder['buz']){
                            $list['buz']=json_decode($TaskOrder['buz'],true);
                        }
                    }
                    $list['TaskOrder']=$TaskOrder;
                    if($list['idsum']>$odcount){
                        $list['islname']=1;
                    }else{
                        $list['islname']=2;
                    }
                    if($list['status']=='hidden'){
                        $list['islname']=3;
                    }
                    $list['odcount']=$odcount;
                    $UserGroup=model('UserGroup')->where(['id'=>$list['users']['group_id']])->find();
                        if(isset($UserGroup['name'])){
                            $list['Groupname']=$UserGroup['name'];
                        }else{
                            $list['Groupname']='';
                        }
                        $list['createtime']=date('Y-m-d H:i',$list['createtime']);
                    	if(isset($list['users']['avatar'])){
                    	    if(strpos($list['users']['avatar'],'http') !== false){ 
                                $list['avatar']=$list['users']['avatar'];
                            }else{
                                if($list['users']['avatar']){
                                    $list['avatar']= $config['imgurl'].$list['users']['avatar'];
                                }else{
                                     $list['avatar']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                            } 
                    	}else{
                    	    $list['avatar']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                    	}
 
                }
            return json($list);
    }
    public function delorder(){
        $tdata=model('TaskOrder')->where(['id'=>input('id')])->find();
        if($tdata['stype']>2){
            $this->error(__('当前任务正在进行不能取消'));
        }
        $up=['isqx'=>2,'qxbz'=>'用户取消 '.date('Y-m-d H:i',time())];
        model('TaskOrder')->where(['id'=>input('id')])->update($up);
        $this->success(__('取消成功'));
    }
    
    
    public function getsubtime($time){
        $Task['subtime']=$time;
        if($Task['subtime']=='1小时'){
            $subtime=3600*1;
        }else if($Task['subtime']=='2小时'){
            $subtime=3600*3;
        }else if($Task['subtime']=='3小时'){
            $subtime=3600*3;
        }else if($Task['subtime']=='4小时'){
            $subtime=3600*4;
        }else if($Task['subtime']=='5小时'){
            $subtime=3600*5;
        }else if($Task['subtime']=='6小时'){
            $subtime=3600*6;
        }else if($Task['subtime']=='7小时'){
            $subtime=3600*7;
        }else if($Task['subtime']=='8小时'){
            $subtime=3600*7;
        }else{
            $subtime=0;
        } 
        return $subtime;
    }
    public function getshetime($time){
        $Task['subtime']=$time;
        if($Task['subtime']=='24小时'){
            $subtime=3600*24;
        }else if($Task['subtime']=='48小时'){
            $subtime=3600*48;
        }else if($Task['subtime']=='72小时'){
            $subtime=3600*72;
        }else{
            $subtime=0;
        } 
        return $subtime;
    }
    
}
