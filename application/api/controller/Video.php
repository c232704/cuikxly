<?php

namespace app\api\controller;
use think\Db;
use fast\Tree;
use app\common\controller\Api;
/**
 * 首页接口
 */
class Video extends Api
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
        $list =  model('Video')
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
    public function videoRecommend()
    {
        $uid=input('uid',0);
        $page=input('page',1);
        $limit=input('limit',100);
        $map=['lx'=>1,'status'=>'normal'];
        
        $list =DB::name('videolist')->where($map)->limit($limit)->page($page)->select();
        $total = DB::name('videolist')->where($map)->count();
        
        if($list){
            foreach ($list as $k=>$v){
                $video=DB::name('video')->order('weigh Asc,id Asc')->where(['pid'=>$v['id']])->find();
                $videolove=Db::name('videolove')->where(['uid'=>$uid,'vid'=>$v['id'],'mid'=>$video['id']])->find();
                $list[$k]['like']=1;
                if($videolove){
                    $list[$k]['like']=2;
                }
                
                $videorecord=Db::name('videorecord')->where(['uid'=>$uid,'vid'=>$v['id'],'mid'=>$video['id']])->find();
                $list[$k]['iszj']=1;
                if($videorecord){
                    $list[$k]['iszj']=2;
                }
                
                
                $User=\app\common\model\User::where(['id'=>$v['uid']])->field('id,nickname,avatar')->find();
 
                
                $list[$k]['mid']=$video['id'];
                $list[$k]['href']=$this->pissrc($video['img']);
                $list[$k]['img']=$this->pissrc($video['img']);
                $list[$k]['isShowProgressBarTime']=false;
                $list[$k]['isShowimage']=false;
                $list[$k]['isplay']=true;
                // $list[$k]['like']=$v['give'];
                $list[$k]['like_n']=$v['give'];
                $list[$k]['msg']=$v['text'];
                $list[$k]['pinlun']=[];
                $list[$k]['playIng']=false;
                $list[$k]['playNumber']=0;
                $list[$k]['sms_n']=0;
                $list[$k]['src']=$this->pissrc($video['videourl']);
                $list[$k]['state']="pause";
                $list[$k]['title']='《'.$v['name'].'》';
                $list[$k]['userID']=$User['id'];
                $list[$k]['username']=$User['nickname'];
                //$list[$k]['_id']=md5($v['id']);
                $list[$k]['_id']='vid'.$v['id'];
                $list[$k]['namets']=$video['name'].' （共'.$v['sum'].'集） 查看更多续集';
                
            }
        }
        
        $result = array("total" => $total,"isempty" => !empty($list)?1:2, "result" => $list,"affectedDocs" => $total, "data" => $list,'tuday'=>date('Y-m-d',time()));
        return json($result);
    }
    public function videoinfo()
    {
        $vid=input('vid',0);//视频id
        $page=input('page',1);
        $limit=input('limit',100);
        Db::name('videolist')->where(['id'=>$vid])->setInc('views');//累加播放记录
        
        $map=['status'=>'normal','pid'=>$vid];
        $list =DB::name('video')->where($map)->order('weigh Asc,id Asc')->limit($limit)->page($page)->select();
        $total = DB::name('video')->where($map)->count();
        
        //观看权限开始
        $isvip=1;//1不是vip 2是vip
        $pays=1; //1不能观看 2能观看
        $uid=0;
        $midlog=0;
        if(input('token') and input('token')!='undefined'){
            
           $Userdata=$this->auth->getUserinfo();
           if($Userdata){
                $uid=$Userdata['id'];
                if($Userdata['group_id']>1 and $Userdata['dtime']>=time()){
                    $isvip=2;  
                }
                $midlogs=DB::name('videolog')->where(['vid'=>$vid,'uid'=>$uid])->order('id desc')->find();
                if($midlogs){
                    $midlog=$midlogs['mid'];
                }
                //更新播放记录 开始
                // $up=['uid'=>$uid,'vid'=>$vid,'mid'=>input('mid'),'vtime'=>0,'createtime'=>time(),'updatetime'=>time()];
                // $videolog=Db::name('videolog')->where(['uid'=>$uid,'vid'=>$vid,'mid'=>input('mid')])->find();
                // if($videolog){
                //     Db::name('videolog')->where(['id'=>$videolog['id']])->delete();
                // }
                // Db::name('videolog')->insertGetId($up);
                //更新播放记录 结束
           }
           
        }
        
        $datavideo_order=Db::name('video_order')->where(['type'=>2,'lx'=>1,'sid'=>$vid,'uid'=>$uid])->find();// lx 1 全章购买
        if($datavideo_order){
          $pays=2;  
        }
        //观看权限结束
        
        $videolist=[];
        if($list){
            foreach ($list as $k=>$v){
                $list[$k]['pays']=$pays;
                
                //观看权限开始
                if($v['price']==0){
                    $list[$k]['pays']=2;
                }
                
                if($v['vipprice']==0 and $isvip==2){
                    $list[$k]['pays']=2;
                }
                $dataviod=Db::name('video_order')->where(['type'=>2,'lx'=>2,'sid'=>$vid,'uid'=>$uid,'smid'=>$v['id']])->find(); //lx 2 单集购买
                if($dataviod){
                  $list[$k]['pays']=2;  
                }
                
                
                $videolist=DB::name('videolist')->where(['id'=>$v['pid']])->find();
                if($videolist['price']==0){
                    $list[$k]['pays']=2;
                }
                if($videolist['vipprice']==0 and $isvip==2){
                    $list[$k]['pays']=2;
                }
                
                //var_dump($list[$k]['pays']);
                //观看权限结束
                
                if($videolist['sum']<=$total){
                    $videolist['wanji']='完结';
                }else{
                    $videolist['wanji']='更新至'.$v['name'];
                }
                
                $videolist['zcount']=$total;
                $videolove=Db::name('videolove')->where(['uid'=>$uid,'vid'=>$v['pid'],'mid'=>$v['id']])->find();
                $list[$k]['like']=1;
                if($videolove){
                    $list[$k]['like']=2;
                }
                
                $videorecord=Db::name('videorecord')->where(['uid'=>$uid,'vid'=>$v['pid'],'mid'=>$v['id']])->find();
                $list[$k]['iszj']=1;
                if($videorecord){
                    $list[$k]['iszj']=2;
                }
                
                
                $User=\app\common\model\User::where(['id'=>$videolist['uid']])->field('id,nickname,avatar')->find();
                $list[$k]['User']=$User;
              
                // $list[$k]['videourl']=$this->pissrc($v['videourl']);
                // $list[$k]['img']=$this->pissrc($v['img']);
                // $list[$k]['nickname']=$User['nickname'];
                // $list[$k]['avatar']=$this->pissrc($User['avatar']);
                // $list[$k]['type']="videoSmallCard";
                // $list[$k]['mid']=$v['id'];
                
                // $list[$k]['text']=$videolist['text'];
                // $list[$k]['give']=$videolist['give'];
                // $list[$k]['vname']=$videolist['name'];
                // $list[$k]['sum']=$videolist['sum'];
                
                // $list[$k]['fjname']=$v['name'];
                // $list[$k]['name']=$v['name'].' （共'.$videolist['sum'].'集） 选集 >';
                $list[$k]['priced']=$v['price'];
                $list[$k]['vippriced']=$v['vipprice'];
                
                $list[$k]['priceq']=$videolist['price'];
                $list[$k]['vippriceq']=$videolist['vipprice'];
                $list[$k]['isvip']=$isvip;
                
                $list[$k]['mid']=$v['id'];
                $list[$k]['href']=$this->pissrc($v['img']);
                $list[$k]['img']=$this->pissrc($v['img']);
                $list[$k]['isShowProgressBarTime']=false;
                $list[$k]['isShowimage']=false;
                $list[$k]['isplay']=true;
                // $list[$k]['like']=$videolist['give'];
                $list[$k]['like_n']=$videolist['give'];
                $list[$k]['msg']=$videolist['text'];
                $list[$k]['pinlun']=[];
                $list[$k]['playIng']=false;
                $list[$k]['playNumber']=0;
                $list[$k]['sms_n']=0;
                if($list[$k]['pays']==1){
                   $list[$k]['src']=''; 
                }else{
                    $list[$k]['src']=$this->pissrc($v['videourl']);
                }
                
                $list[$k]['state']="pause";
                $list[$k]['title']='《'.$videolist['name'].'》';
                $list[$k]['userID']=$User['id'];
                $list[$k]['username']=$User['nickname'];
                //$list[$k]['_id']=md5($videolist['id']);
                $list[$k]['_id']='vid'.$v['id'];
                $list[$k]['fjname']=$v['name'];
                $list[$k]['namets']=$v['name'].' （共'.$videolist['sum'].'集） 选集 >';
                
            }
        }
        $result = array( "page" => $page, "midlog" => $midlog,"isempty" => !empty($list)?1:2,"data" => $list,"affectedDocs" => $total, "total" => $total, "result" => $list,"videodata" => $videolist,'tuday'=>date('Y-m-d',time()));
        return json($result);
    }
    public function bfjl(){
        $vid=input('vid');
        $mid=input('mid');
        $uid=input('uid');
        if($uid and $mid and $uid){
            //更新播放记录 开始
            $up=['uid'=>$uid,'vid'=>$vid,'mid'=>$mid,'vtime'=>0,'createtime'=>time(),'updatetime'=>time()];
            $videolog=Db::name('videolog')->where(['uid'=>$uid,'vid'=>$vid,'mid'=>$mid])->find();
            if($videolog){
                Db::name('videolog')->where(['id'=>$videolog['id']])->delete();
            }
            Db::name('videolog')->insertGetId($up);
            //更新播放记录 结束  
            $this->success('success');
       }
        
    }
    public function zuiju()
    {
        $getweek=$this->get_week();
        $sy=0;
        $i=1;
        $tdsy=0;
        $fdong=50;
        $chus=100;
        foreach ($getweek as $k=>$v){
            $qdjl=Db::name('qdjl')->where(['uid'=>input('uid'),'time'=>$v['dates']])->find();
            if($qdjl){
                $sy=$qdjl['sy'];
                $getweek[$k]['xz']=2;
            }else{
                $getweek[$k]['xz']=1;
            }
            
            if($v['dates']==date('Y-m-d',time())){
               if($sy==0){
                  $sy=$chus; 
               }
               $tdsy=$sy;
               $getweek[$k]['day']='今天';
            }
            $getweek[$k]['discount']=$sy>0?$sy:$chus;
            if($sy>0){
               $sy=$sy+$fdong;
            }
        }
        
        $qdjls=Db::name('qdjl')->where(['uid'=>input('uid')])->order('id desc')->find();
        if($qdjls){
            $qdjl=$qdjls['sum'];
        }else{
            $qdjl=0;
        }
        
        
        $map1['uid']=input('uid');
        $list =DB::name('videolog')->where($map1)->group('vid')->order('id desc')->limit(12)->select();
        foreach ($list as $k=>$v){
            $v=DB::name('videolog')->where(['vid'=>$v['vid'],'uid'=>input('uid')])->order('id desc')->find();
            $list[$k]=$v;
            
            $videolist = DB::name('videolist')->where(['id'=>$v['vid']])->find();
            $list[$k]['vname']=$videolist['name'];
            $list[$k]['img']=$this->pissrc($videolist['img']);
            $videocount = DB::name('video')->where(['pid'=>$v['vid']])->count();
            $videoweigh = DB::name('video')->where(['pid'=>$v['vid']])->order('weigh desc,id desc')->find();
            if($videolist['sum']<=$videocount){
                $list[$k]['mname']='完结';
            }else{
                $list[$k]['mname']='更新至'.$videoweigh['name'];
            }
            $video = DB::name('video')->where(['id'=>$v['mid']])->find();
            $list[$k]['dqname']='看到'.$video['name'];
        }
        
        $map2['uid']=input('uid');
        $record =DB::name('videorecord')->where($map2)->group('vid')->limit(12)->select();
        foreach ($record as $k=>$v){
            $v=DB::name('videorecord')->where(['vid'=>$v['vid'],'uid'=>input('uid')])->order('id desc')->find();
            $record[$k]=$v;
            $videolist = DB::name('videolist')->where(['id'=>$v['vid']])->find();
            $record[$k]['vname']=$videolist['name'];
            $record[$k]['img']=$this->pissrc($videolist['img']);
            $videocount = DB::name('video')->where(['pid'=>$v['vid']])->count();
            $videoweigh = DB::name('video')->where(['pid'=>$v['vid']])->order('weigh desc,id desc')->find();
            if($videolist['sum']<=$videocount){
                $record[$k]['mname']='完结';
            }else{
                $record[$k]['mname']='更新至'.$videoweigh['name'];
            }
            $video = DB::name('video')->where(['id'=>$v['mid']])->find();
            $record[$k]['dqname']='看到'.$video['name'];
        }
        
        $result = array( "tdsy" => $tdsy,"qdjl" => $qdjl, "getweek" => $getweek,"new" => $list,"record" => $record, 'config'=>$this->config());
        return json($result);
    }
    /**
     * 获取本周所有日期
     */
    public function get_week($time = '', $format='Y-m-d'){
        $time = $time != '' ? $time : time();
        //获取当前周几
        $week = date('w', $time);
        $date = [];
        for ($i=1; $i<=7; $i++){
            $date[$i-1]['day'] = date('m.d',strtotime( '+' . $i-$week .' days', $time));
            $date[$i-1]['dates'] = date($format ,strtotime( '+' . $i-$week .' days', $time));
            $date[$i-1]['week']=$i;
        }
        return $date;
    }
    public function wdxh()
    {
            $config=config('site');
            
            
            
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['uid']=input('uid');
            $order='id desc';
            $total = model('Videolove')
                ->with('users')
                ->group('vid')
                ->where($where)
                ->where($map)
                ->count();
            $record = model('Videolove')
                ->with('users')
                ->group('vid')
                ->where($where)
                ->where($map)
                ->order($order)
                ->limit($offset, $limit)
                ->select();
                foreach ($record as $k=>$v){
                    $videolist = DB::name('videolist')->where(['id'=>$v['vid']])->find();
                    $record[$k]['vname']=$videolist['name'];
                    $record[$k]['img']=$this->pissrc($videolist['img']);
                }
           
            $result = array("total" => $total, "rows" => $record,'config'=>$this->config());
            return json($result);
    }
    
    public function recordlist()
    {
            $config=config('site');
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['uid']=input('uid');
            $order='id desc';
            $total = model('Videorecord')
                ->with('users')
                ->group('vid')
                ->where($where)
                ->where($map)
                ->count();
            $record = model('Videorecord')
                ->with('users')
                ->group('vid')
                ->where($where)
                ->where($map)
                ->order($order)
                ->limit($offset, $limit)
                ->select();
                foreach ($record as $k=>$v){
                    $v=DB::name('videorecord')->where(['vid'=>$v['vid'],'uid'=>input('uid')])->order('id desc')->find();
                    $record[$k]=$v;
                    $videolist = DB::name('videolist')->where(['id'=>$v['vid']])->find();
                    
                    $record[$k]['vname']=$videolist['name'];
                    $record[$k]['img']=$this->pissrc($videolist['img']);
                    $videocount = DB::name('video')->where(['pid'=>$v['vid']])->count();
                    $videoweigh = DB::name('video')->where(['pid'=>$v['vid']])->order('weigh desc,id desc')->find();
                    if($videolist['sum']<=$videocount){
                        $record[$k]['mname']='完结';
                    }else{
                        $record[$k]['mname']='更新至'.$videoweigh['name'];
                    }
                    $video = DB::name('video')->where(['id'=>$v['mid']])->find();
                    $record[$k]['dqname']='看到'.$video['name'];
                    
                }
           
            $result = array("total" => $total, "rows" => $record,'config'=>$this->config());
            return json($result);
    }
    public function videolog()
    {
            $config=config('site');
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $map['uid']=input('uid');
            $order='id desc';
            $total = model('Videolog')
                ->with('users')
                ->group('vid')
                ->where($where)
                ->where($map)
                ->count();
            $record = model('Videolog')
                ->with('users')
                ->group('vid')
                ->where($where)
                ->where($map)
                ->order($order)
                ->limit($offset, $limit)
                ->select();
                foreach ($record as $k=>$v){
                    $v=DB::name('videolog')->where(['vid'=>$v['vid'],'uid'=>input('uid')])->order('id desc')->find();
                    $record[$k]=$v;
                    $videolist = DB::name('videolist')->where(['id'=>$v['vid']])->find();
                    
                    $record[$k]['vname']=$videolist['name'];
                    $record[$k]['img']=$this->pissrc($videolist['img']);
                    $videocount = DB::name('video')->where(['pid'=>$v['vid']])->count();
                    $videoweigh = DB::name('video')->where(['pid'=>$v['vid']])->order('weigh desc,id desc')->find();
                    if($videolist['sum']<=$videocount){
                        $record[$k]['mname']='完结';
                    }else{
                        $record[$k]['mname']='更新至'.$videoweigh['name'];
                    }
                    $video = DB::name('video')->where(['id'=>$v['mid']])->find();
                    $record[$k]['dqname']='看到'.$video['name'];
                    
                }
           
            $result = array("total" => $total, "rows" => $record,'config'=>$this->config());
            return json($result);
    }
    public function indexdata()
    {
            $map1=['videolist.lx'=>1,'videolist.status'=>'normal'];
            $list = model('Videolist')->with('users')->where($map1)->order('videolist.createtime desc')->limit(12)->select();
            
            $mapjq=['videolist.lx'=>1,'videolist.status'=>'normal'];
            $mapjq['videolist.story'] = ['neq',''];
            $listjq = model('Videolist')->with('users')->where($mapjq)->order('videolist.createtime desc')->limit(50)->select();
            
            $map2=['videolist.lx'=>1,'videolist.status'=>'normal'];
            $hotdata = model('Videolist')->with('users')->where($map2)->order('videolist.views desc')->limit(12)->select();
            
            $map3=['videolist.lx'=>1,'videolist.status'=>'normal'];
            $givedata = model('Videolist')->with('users')->where($map3)->order('videolist.give desc')->limit(12)->select();
            
            $mapbz=['videolist.lx'=>4,'videolist.status'=>'normal'];
            $listbz = model('Videolist')->with('users')->where($mapbz)->order('videolist.id desc')->limit(50)->select();
            
            $result = array("listbz" => $this->listadt($listbz),"new" => $this->listadt($list),"listjq" => $this->listadt($listjq),"hotdata" => $this->listadt($hotdata),"givedata" => $this->listadt($givedata), 'config'=>$this->config());
            return json($result);
    }
    public function listadt($list)
    {
        if($list){
            $config=config('site');
            foreach ($list as $k=>$v){
                $list[$k]['updatetime']=date('Y-m-d',$v['updatetime']);
                $lx=[1=>'视频',2=>'音频',3=>'小说',4=>'图片'];
                $list[$k]['lxname']=isset($lx[$list[$k]['lx']])?$lx[$list[$k]['lx']]:'其他';
                
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
            	if(strpos($v['img'],'http') !== false){ 
                        $list[$k]['img']=$v['img'];
                    }else{
                        if($v['img']){
                            $list[$k]['img']= $config['imgurl'].$v['img'];
                        }else{
                             $list[$k]['img']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                        }
                }
            }
        }
        return $list;
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
            $order='videolist.createtime desc';
            if(input('ishot')){
                $map['videolist.ishot']=input('ishot');
            }
            // if(input('lx')){
            //     $map['videolist.lx']=input('lx');
            // }
            if(input('lx')==3){
                $order='videolist.give desc';
            }else if(input('lx')==2){
                $map['videolist.price']=0;
            }
            $map['videolist.lx']=1;
            if(input('typeid')){
                $map['videolist.type'] = ['like', '%"'.input('typeid').'"%'];
            }
            if(input('addddid')){
                $map['videolist.adddd'] = ['like', '%"'.input('addddid').'"%'];
            }
            if(input('yearid')){
                $map['videolist.yearq'] = input('yearid');
            }
            if(input('type')){
                if(input('type')=='全部'){
                    
                }else{
                    $map['videolist.type']=input('type');
                }
            }
            if(input('keytext')){
                $map['videolist.name|videolist.id'] = ['like', '%'.input('keytext').'%'];
            }
            $map['videolist.status']='normal';
            $total = model('Videolist')
                ->with('users')
                ->where($where)
                ->where($map)
                ->count();
            $list = model('Videolist')
                ->with('users')
                ->where($where)
                ->where($map)
                ->order($order)
                ->limit($offset, $limit)
                ->select();
                if($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['updatetime']=date('Y-m-d',$v['updatetime']);
                        $lx=[1=>'视频',2=>'音频',3=>'小说',4=>'图片'];
                        $list[$k]['lxname']=isset($lx[$list[$k]['lx']])?$lx[$list[$k]['lx']]:'其他';
                        
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
                    	if(strpos($v['img'],'http') !== false){ 
                                $list[$k]['img']=$v['img'];
                            }else{
                                if($v['img']){
                                    $list[$k]['img']= $config['imgurl'].$v['img'];
                                }else{
                                     $list[$k]['img']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                        }
                    }
                }
            $typedata=['type'=>$this->fenlei('type'),'adddd'=>$this->fenlei('adddd'),'year'=>$this->fenlei('year'),'type2'=>$this->fenlei('type2'),'type3'=>$this->fenlei('type3'),'type4'=>$this->fenlei('type4')];
            $result = array("total" => $total, "rows" => $list, 'config'=>$this->config(),'typedata'=>$typedata);
            return json($result);
    }
    public function info()
    {
            $mid=input('mid',0);
            $config=config('site');
            //var_dump($where);
            $isvip=0;
            $Userdata=[];
            $datavideo_order=[];
            $datavideo_orderd=[];
            if(input('token')){
               $Userdata=$this->auth->getUserinfo();
               if($Userdata['group_id']==1){
                   $isvip=0;
               }else{
                  if($Userdata['dtime']>=time()){
                        $isvip=2;
                  }else{
                        $isvip=1;   
                  } 
               }
            }
            
            $list = model('Videolist')
                ->where(['id'=>input('id')])
                ->find();
                if($list){
                    $list['updatetime']=date('Y-m-d',$list['updatetime']);
                   if(strpos($list['img'],'http') !== false){ 
                                $list['img']=$list['img'];
                            }else{
                                if($list['img']){
                                    $list['img']= $config['imgurl'].$list['img'].'?v='.time();
                                }else{
                                     $list['img']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                        }
                }
                
            $price=$list['price']*1;
            $vipprice=$list['vipprice']*1;
            
            if($Userdata){
                $datavideo_order=Db::name('video_order')->where(['type'=>2,'lx'=>1,'sid'=>input('id'),'uid'=>$Userdata['id']])->find();//1 全章购买 
            }
            
            $orders='weigh asc';
            $Video = model('Video')
                ->where(['pid'=>input('id')])
                ->order($orders)
                ->select();
                if($Video){
                    foreach ($Video as $k=>$v){
                        $Video[$k]['updatetime']=date('Y-m-d',$v['updatetime']);

                        if(strpos($v['img'],'http') !== false){ 
                                $Video[$k]['img']=$v['img'];
                            }else{
                                if($v['img']){
                                    $Video[$k]['img']= $config['imgurl'].$v['img'];
                                }else{
                                    $Video[$k]['img']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                        }
                        if(strpos($v['videourl'],'http') !== false){ 
                                $Video[$k]['videourl']=$v['videourl'];
                        }else{
                            if($v['videourl']){
                                $Video[$k]['videourl']= $config['imgurl'].$v['videourl'];
                            }else{
                                $Video[$k]['videourl']='';
                            }
                        } 
                        if($mid==$Video[$k]['id']){
                            $list['videourl']=$Video[$k]['videourl'];
                            $list['pid']=$Video[$k]['id'];
                            $list['ji']=$Video[$k]['name'];
                            $list['playIndex']=$k;
                            $list['infos']=$Video[$k]['info'];
                            $list['priced']=$Video[$k]['price'];
                            $list['vippriced']=$Video[$k]['vipprice'];
                            $list['syj']=isset($Video[$k-1]['id'])?$Video[$k-1]['id']:0;
                            $list['xyj']=isset($Video[$k+1]['id'])?$Video[$k+1]['id']:0;
                        }else if($mid==0){
                            if($v['weigh']==1){
                                $list['videourl']=$Video[$k]['videourl'];
                                $list['pid']=$Video[$k]['id'];
                                $list['ji']=$Video[$k]['name'];
                                $list['playIndex']=0;
                                $list['infos']=$Video[$k]['info'];
                                $list['priced']=$Video[$k]['price'];
                                $list['vippriced']=$Video[$k]['vipprice'];
                                $list['syj']=0;
                                $list['xyj']=isset($Video[$k+1]['id'])?$Video[$k+1]['id']:0;
                            }    
                        }
     
                        
                        $Video[$k]['isplay']=0;
                        $dataviod=[];
                        if($Userdata){
                            $dataviod=Db::name('video_order')->where(['type'=>2,'lx'=>2,'sid'=>input('id'),'uid'=>$Userdata['id'],'smid'=>$Video[$k]['id']])->find(); //单集购买
                        }
                        
                        if($price==0){//判断是否全集购买
                            $Video[$k]['isplay']=1;
                        }else if($isvip>1 and $vipprice==0){
                            $Video[$k]['isplay']=1;
                        }else if($datavideo_order){
                            $Video[$k]['isplay']=1;
                        }
                        
                        if($Video[$k]['price']==0){//判断是否单集购买
                            $Video[$k]['isplay']=1;
                        }else if($isvip>1 and $Video[$k]['vipprice']==0){
                            $Video[$k]['isplay']=1;
                        }else if($dataviod){
                            $Video[$k]['isplay']=1;
                        }
                        if($Video[$k]['isplay']==0){
                            $Video[$k]['src']=$config['imgurl'].'/assets/mp3/1b626083-179ccf4b36011.wav?v='.$Video[$k]['id'];
                            $Video[$k]['title']=$Video[$k]['name'].' 【未购买】';
                        }else{
                            $Video[$k]['src']=$Video[$k]['videourl'];
                            $Video[$k]['title']=$Video[$k]['name'];//$list['name'].' '.
                        }
                        $Video[$k]['singer']=$list['uname'];
                        $Video[$k]['coverImgUrl']=$Video[$k]['img'];
                        
                    }
                }
            if($mid==0){
                $mid=$list['pid'];
            }
            if($Userdata){
                $datavideo_orderd=Db::name('video_order')->where(['type'=>2,'lx'=>2,'sid'=>input('id'),'uid'=>$Userdata['id'],'smid'=>$mid])->find(); //2单集购买	 
            }
            $list['video']=$Video;
            $lx=[1=>'视频',2=>'音频',3=>'文章',3=>'图片'];
            $list['lxname']=isset($lx[$list['lx']])?$lx[$list['lx']]:'其他';
            
            $priced=$list['priced']*1;
            $vippriced=$list['vippriced']*1;
            
            $isplay=0;
            $isplayd=0;
            if($price==0){
                $isplay=1;
            }else if($isvip>1 and $vipprice==0){
                $isplay=1;
            }else if($datavideo_order){
                $isplay=1;
            }
            
            if($priced==0){
                //$isplay=1;
                $isplayd=1;
            }else if($isvip>1 and $vippriced==0){
                //$isplay=1;
                $isplayd=1;
            }else if($datavideo_orderd){
                //$isplay=1;
                $isplayd=1;
            }
            $list['isplayd']=$isplayd;
            $list['isplay']=$isplay;
            $list['isvip']=$isvip;
            $list['mrseek']=isset($config['mrseek'])?$config['mrseek']:15;
            return json($list);
    }
    public function infotp()
    {
            $mid=input('mid',0);
            $config=config('site');
            //var_dump($where);
            $isvip=0;
            $Userdata=[];
            $datavideo_order=[];
            $datavideo_orderd=[];
            if(input('token')){
               $Userdata=$this->auth->getUserinfo();
               if($Userdata['group_id']==1){
                   $isvip=0;
               }else{
                  if($Userdata['dtime']>=time()){
                        $isvip=2;
                  }else{
                        $isvip=1;   
                  } 
               }
            }
            
            $list = model('Videolist')
                ->where(['id'=>input('id')])
                ->find();
                if($list){
                    $list['updatetime']=date('Y-m-d',$list['updatetime']);
                   if(strpos($list['img'],'http') !== false){ 
                                $list['img']=$list['img'];
                            }else{
                                if($list['img']){
                                    $list['img']= $config['imgurl'].$list['img'].'?v='.time();
                                }else{
                                     $list['img']=$config['imgurl'].'/uploads/20200523/250b3f89b40ff3714b07cc51b4c2f63d.png';
                                }
                        }
                }
                
            $price=$list['price']*1;
            $vipprice=$list['vipprice']*1;
            
            if($Userdata){
                $datavideo_order=Db::name('video_order')->where(['type'=>2,'lx'=>1,'sid'=>input('id'),'uid'=>$Userdata['id']])->find();//1 全章购买 
            }
            
            
            if($Userdata){
                $datavideo_orderd=Db::name('video_order')->where(['type'=>2,'lx'=>2,'sid'=>input('id'),'uid'=>$Userdata['id'],'smid'=>$mid])->find(); //2单集购买	 
            }
            $ud=[];
            if($list['pic']){
                $arr = explode(',', $list['pic']);
                foreach ($arr as $ka=>$va){
                    $ud[]=$this->pissrc($va);
                }
            }
            $list['images']=$ud;
            $list['imageslength']=count($ud);
            //$this->pissrc($v['videourl']);
            
            
            
            $lx=[1=>'视频',2=>'音频',3=>'文章',3=>'图片'];
            $list['lxname']=isset($lx[$list['lx']])?$lx[$list['lx']]:'其他';
            

            $isplay=0;
            $isplayd=0;
            if($price==0){
                $isplay=1;
            }else if($isvip>1 and $vipprice==0){
                $isplay=1;
            }else if($datavideo_order){
                $isplay=1;
            }else if($isvip>1){
                //$isplay=1;
                $isplayd=1;
            }else if($datavideo_orderd){
                //$isplay=1;
                $isplayd=1;
            }
            $list['videoids']=isset($config['weixinxcx']['videoAd'])?$config['weixinxcx']['videoAd']:'';
            $list['isplayd']=$isplayd;
            $list['isplay']=$isplay;
            $list['isvip']=$isvip;
            $list['mrseek']=isset($config['mrseek'])?$config['mrseek']:15;
            return json($list);
    }
    public function orderlists()
    {
            $config=config('site');
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $order='video_order.id desc';
            $map['users.id']=input('uid');
            $map['video_order.type']=2;
            $total = model('VideoOrder')
                ->with('users')
                ->where($where)
                ->where($map)
                ->count();
            $list = model('VideoOrder')
                ->with('users')
                ->where($where)
                ->where($map)
                ->order($order)
                ->limit($offset, $limit)
                ->select();
                if($list){
                    foreach ($list as $k=>$v){
                        //var_dump($v['img']);
                        $videolists=Db::name('videolist')->where(['id'=>$v['sid']])->find();
                        $splx=isset($videolists['lx'])?$videolists['lx']:0;
                        $list[$k]['time']=date('Y-m-d',$v['updatetime']);
                        if($v['type']==1){
                            $list[$k]['status_means']='未支付';//1未支付 2已支付
                        }else if($v['type']==2){
                            $list[$k]['status_means']='已支付';
                        }
                        $list[$k]['status']=1;
                        $list[$k]['exchange_type']=3;
                        $list[$k]['delivery_type']=1;
                        $list[$k]['tp']=1;
                        $list[$k]['splx']=$splx;
                        
                        //$list[$k]['name']=substr($v['name'],0,10);
                    }
                }
            
            $result = array("total" => $total, "rows" => $list, 'config'=>$this->config());
            return json($result);
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
    public function fenlei($tpie='type')
    {
        $tree = Tree::instance();
        $this->model = model('app\common\model\Category');
        $tree->init(collection($this->model->where(['type'=>$tpie])->order('weigh desc,id desc')->field('id,pid,name,nickname,image,type,flag')->select())->toArray(), 'pid');
        $fenlei = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $groupdata[0]=['name'=>'全部','id'=>0];
        foreach ($fenlei as $k => $v) {
                $groupdata[$k+1]['name'] = $v['name'];
                $groupdata[$k+1]['id'] = $v['id'];
        }
        return $groupdata;
    }
}
