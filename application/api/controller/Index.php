<?php

namespace app\api\controller;

use app\common\controller\Api;
/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        $config=$this->config();
        $configs=config('site');
        $map['ishot']=2;
        $map['endtimesjc']=['>',time()];
        if(input('uid',0)>0){
            $map['uid']=input('uid');
        }
        $list =  model('Task')
                ->with('users')
                ->where($map)
                ->limit(10)
                ->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
                    	if(isset($v['users']['avatar'])){
                    	    if(strpos($v['users']['avatar'],'http') !== false){ 
                                $list[$k]['avatar']=$v['users']['avatar'];
                            }else{
                                if($v['users']['avatar']){
                                    $list[$k]['avatar']= $configs['imgurl'].$v['users']['avatar'];
                                }else{
                                     $list[$k]['avatar']=$configs['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                            } 
                    	}else{
                    	    $list[$k]['avatar']=$configs['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                    	}
                    	
                        
                    }
                }
        $data=['config'=>$config,'doctor'=>$list];
        $this->success('请求成功',$data);
    }
    public function webconfig()
    {
        $config=$this->config();
        $this->success('请求成功',$config);
    }
    public function lists()
    {
            $config=config('site');
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if(input('uid',0)>0){
                $map['uid']=input('uid');
            }
            $order='task.id desc';
            if(input('tabIndex')==0){
                 $order='task.id desc';
            }
            if(input('tabIndex')==1){
                $order='task.id desc';
            }
            if(input('tabIndex')==2){
                $order='task.price desc';
            }
            if(input('tabBarsname')){
                if(input('tabBarsname')=='全部'){
                    
                }else{
                    $map['task.type']=input('tabBarsname');
                }
            }
            if(input('keytext')){
                $map['task.name|task.id'] = ['like', '%'.input('keytext').'%'];
            }
            //dump($where);
            $map['task.endtimesjc']=['>',time()];
            $map['task.paytype']=2;
            $map['task.status']='normal';
            $total = model('Task')
                ->with('users')
                ->where($where)
                ->where($map)
                ->count();
            $list = model('Task')
                ->with('users')
                ->where($where)
                ->where($map)
                ->order($order)
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
    public function info()
    {
            $config=config('site');
            //var_dump($where);
            $list = model('Task')
                ->where(['id'=>input('id')])
                ->find();
                if($list){
                    $odcount=model('TaskOrder')->where(['oid'=>$list['id'],'uid'=>input('uid',0)])->count();
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
                        if($list['buz']){
                            $list['buz']=json_decode($list['buz'],true);
                        }else{
                             $list['buz']=[];
                        }
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
                $list['newtime']=time();
            return json($list);
    }
    public function tjinfo()
    {
            $config=config('site');
            //var_dump($where);
            $list = model('Task')
                ->where(['id'=>input('id')])
                ->find();
                if($list){
                    $odcount=model('TaskOrder')->where(['oid'=>$list['id'],'uid'=>input('uid',0)])->count();
                    $TaskOrder=model('TaskOrder')->where(['id'=>input('rwid',0)])->find();
                    if($TaskOrder){
                        if($TaskOrder['buz']){
                            $TaskOrder['buz']=json_decode($TaskOrder['buz'],true);
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
                        if($list['buz']){
                            $list['buz']=json_decode($list['buz'],true);
                        }else{
                             $list['buz']=[];
                        }
                        if($TaskOrder['buz']){
                            $list['buz']=$TaskOrder['buz'];
                        }
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
}
