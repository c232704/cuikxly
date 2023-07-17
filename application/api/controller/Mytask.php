<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\MoneyLog;
use think\Db;

/**
 * 会员接口
 */
class Mytask extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Task');
        $this->Usermodel = model('User');
    }

    /**
     * 会员中心
     */
    public function index()
    {
            $config=config('site');
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map=[];
            if(input('uid',0)>0){
                $map['uid']=input('uid');
            }
            if(input('tabIndex')==1){
                $map['paytype']=1;
            }
            if(input('tabIndex')==0){
                $map['paytype']=2;
                $map['endtimesjc']=['>',time()];
            }
            if(input('tabIndex')==2){
                $map['endtimesjc']=['<',time()];
            }
            $total = $this->model
                ->with('users')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('users')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
                        $TaskOrder=model('TaskOrder')->where(['oid'=>$v['id']])->select();
                        $TaskOrdercount=model('TaskOrder')->where(['oid'=>$v['id'],'stype'=>2])->count();
                        $list[$k]['TaskOrder']=$TaskOrder;
                        $list[$k]['TaskOrdercount']=$TaskOrdercount;
                    	if(isset($v['users']['avatar'])){
                    	    if(strpos($v['users']['avatar'],'http') !== false){ 
                                $list[$k]['avatar']=$v['users']['avatar'];
                            }else{
                                if($v['users']['avatar']){
                                    $list[$k]['avatar']= $config['imgurl'].$v['users']['avatar'];
                                }else{
                                     $list[$k]['avatar']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                            } 
                    	}else{
                    	    $list[$k]['avatar']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                    	}
                    	
                        
                    }
                }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
    }
    
    public function tasksh()
    {
        $config=config('site');
           // $this->relationSearch = true;
           
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['oid']=input('id',0);
            if(input('stype')>1){
                $map['stype']=input('stype');
            }
            $total = model('TaskOrder')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->count();
            $list = model('TaskOrder')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
                        $list[$k]['ttime']=date('Y-m-d H:i',$v['ttime']);
                        $v['users']=model('User')->where(['id'=>$v['uid']])->find();
                    	if(isset($v['users']['avatar'])){
                    	    if(strpos($v['users']['avatar'],'http') !== false){ 
                                $list[$k]['avatar']=$v['users']['avatar'];
                            }else{
                                if($v['users']['avatar']){
                                    $list[$k]['avatar']= $config['imgurl'].$v['users']['avatar'];
                                }else{
                                     $list[$k]['avatar']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                            } 
                    	}else{
                    	    $list[$k]['avatar']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                    	}
                    	
                        
                    }
                }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
    }
    
    public function renwjsuan()
    {
        $Task=$this->model->where(['id'=>input('id')])->find(); 
        if($Task['sfjs']==2){
            $this->error('已经结算 请勿重复操作'); 
        }
        $money=$Task['zprice']-$Task['kprice'];
        if($money>0){
            $this->model->where(['id'=>input('id')])->update(['sfjs'=>2]);
            $user=$this->auth->getUserinfo();
            $user = $this->Usermodel::get($user['id']);
            if ($user && $money != 0) {
                $before = $user->money;
                $after = $user->money + $money;
                //更新会员信息
                $user->save(['money' => $after]);
                //写入日志
                MoneyLog::create(['user_id' => $user['id'], 'money' => $money, 'before' => $before, 'after' => $after,'fid' => 0, 'sxf' =>0,'memo' => '任务结算']);
            }else{
               $this->error(__('金额不对')); 
            }
            $this->success(__('操作成功'));
        }else{
             $this->error('没有金额可以结算'); 
        }   
    }
    public function onsubshnehe()
    {
        $TaskOrder=model('TaskOrder')->where(['id'=>input('id',0)])->find();
        if($TaskOrder['stype']>2){
                $this->error('已经审核 请勿重复操作');
        }
        if($TaskOrder['stype']==1){
                $this->error('未提交 不能操作');
        }
        model('TaskOrder')->where(['id'=>$TaskOrder['id']])->update(['stype'=>4,'stime'=>time(),'sbz'=>input('sbz')]);
        $this->success('提交成功');
    }
    public function subshnehe()
    {
        $TaskOrder=model('TaskOrder')->where(['id'=>input('id',0)])->find();
        if($TaskOrder){
            if($TaskOrder['stype']>2){
                $this->error('已经审核 请勿重复操作');
            }
            if($TaskOrder['stype']==1){
                $this->error('未提交 不能操作');
            }
            $User=model('User')->where(['id'=>$TaskOrder['uid']])->find();
            if(!$User){
                $this->error('User错误');
            }
            $User['group_id']=isset($User['group_id'])?$User['group_id']:1;
            $User['group_id']=!empty($User['group_id'])?$User['group_id']:1;
            $UserGroup=model('UserGroup')->where(['id'=>$User['group_id']])->find();
            if(!$UserGroup){
                $this->error('UserGroup错误');
            }
            $yongj=$TaskOrder['price']-$TaskOrder['price']*$UserGroup['huilv'];
            $sxf=$TaskOrder['price']*$UserGroup['huilv'];
                Db::startTrans();
                try {
                    $this->money($yongj,$User['id'],$TaskOrder['id'],$sxf);
                    model('TaskOrder')->where(['id'=>$TaskOrder['id']])->update(['stype'=>3,'stime'=>time(),'sbz'=>input('sbz')]);
                    $this->model->where(['id'=>$TaskOrder['oid']])->setInc('kprice',$TaskOrder['price']);
                    Db::commit();
                    $this->success('审核成功');
                }catch (Exception $e){
                    Db::rollback();
                    $this->error($e->getMessage());
                }
        }
        
    }
    public function lqyj(){
        $TaskOrder=model('TaskOrder')->where(['id'=>input('id',0)])->find();
        if($TaskOrder){
            if($TaskOrder['stype']>2){
                $this->error('已经审核 请勿重复操作');
            }
            if($TaskOrder['stype']==1){
                $this->error('未提交 不能操作');
            }
            $User=model('User')->where(['id'=>$TaskOrder['uid']])->find();
            if(!$User){
                $this->error('User错误');
            }
            $User['group_id']=isset($User['group_id'])?$User['group_id']:1;
            $User['group_id']=!empty($User['group_id'])?$User['group_id']:1;
            $UserGroup=model('UserGroup')->where(['id'=>$User['group_id']])->find();
            if(!$UserGroup){
                $this->error('UserGroup错误');
            }
            $yongj=$TaskOrder['price']-$TaskOrder['price']*$UserGroup['huilv'];
            $sxf=$TaskOrder['price']*$UserGroup['huilv'];
                Db::startTrans();
                try {
                    $this->money($yongj,$User['id'],$TaskOrder['id'],$sxf);
                    model('TaskOrder')->where(['id'=>$TaskOrder['id']])->update(['stype'=>3,'stime'=>time(),'sbz'=>'用户领取']);
                    $this->model->where(['id'=>$TaskOrder['oid']])->setInc('kprice',$TaskOrder['price']);
                    Db::commit();
                    $this->success('领取成功');
                }catch (Exception $e){
                    Db::rollback();
                    $this->error($e->getMessage());
                }
        } 
    }
    public function money($money,$user_id,$id,$sxf=0){
        $user = $this->Usermodel::get($user_id);
        if ($user && $money != 0) {
            $before = $user->money;
            $after = $user->money + $money;
            //更新会员信息
            $user->save(['money' => $after]);
            //写入日志
            MoneyLog::create(['user_id' => $user_id, 'money' => $money, 'before' => $before, 'after' => $after,'fid' => $id, 'sxf' =>$sxf,'memo' => '用户佣金']);
        }else{
           $this->error(__('金额不对')); 
        }
    }
    public function del()
    {
        $tdata=$this->model->where(['id'=>input('oid')])->find();
        if($tdata['paytype']==2){
            $this->error(__('当前任务正在进行不能删除'));
        }
       $this->model->where(['id'=>input('id')])->delete(); 
       $this->success(__('删除成功'));
    }
    public function statusn()
    {
       $this->model->where(['id'=>input('id')])->update(['status'=>'hidden']); 
       $this->success(__('操作成功'));
    }
    public function statuson()
    {
       $this->model->where(['id'=>input('id')])->update(['status'=>'normal']); 
       $this->success(__('操作成功'));
    }
    
    public function lqrw()
    {
        $tdata=$this->model->where(['id'=>input('oid')])->find();
        $up=[
                'name'=>input('name'),
                'stype'=>1,
                'uid'=>input('uid'),
                'oid'=>input('oid'),
                'price'=>input('price'),
                'img'=>input('img'),
                'fid'=>$tdata['uid'],
                'createtime'=>time(),
            ];
            
            if($tdata['sumsy']<=0){
                $this->error(__('任务已经做完'));
            }
            if($tdata['status']=='hidden'){
                $this->error(__('任务停止推广'));
            }
            if($tdata['sumsy']==1){
              $this->model->where(['id'=>input('oid')])->update(['endtimesjc'=>0]);  
            }
            $this->model->where(['id'=>input('oid')])->setInc('sumed',1);
            $this->model->where(['id'=>input('oid')])->setDec('sumsy',1);
            $id=Db::name('task_order')->insertGetId($up);
            $this->success(__('领取成功'));
        
    }
    public function rwtj()
    {
        $datapost =input('param.');//订单数据
        $datapost['buzs']=htmlspecialchars_decode($datapost['buz']);
        $up=[
                'stype'=>2,
                'buz'=>$datapost['buzs'],
                'ttime'=>time(),
            ];
        $rt=model('TaskOrder')->where(['id'=>input('id')])->update($up); 
        $this->success(__('提交成功'));
    }
    
    public function order()
    {
        $config=config('site');
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['uid']=input('uid',0);
            if(input('stype')>0){
                $map['stype']=input('stype');
            }
            $total = model('TaskOrder')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->count();
            $list = model('TaskOrder')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
                    	if(isset($v['users']['avatar'])){
                    	    if(strpos($v['users']['avatar'],'http') !== false){ 
                                $list[$k]['avatar']=$v['users']['avatar'];
                            }else{
                                if($v['users']['avatar']){
                                    $list[$k]['avatar']= $config['imgurl'].$v['users']['avatar'];
                                }else{
                                     $list[$k]['avatar']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                            } 
                    	}else{
                    	    $list[$k]['avatar']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                    	}
                    	
                        
                    }
                }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
    }
    
    
}
