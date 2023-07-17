<?php

namespace app\admin\controller\video;

use app\common\controller\Backend;
use fast\Tree;
use think\Db;
/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Videolist extends Backend
{

    protected $relationSearch = true;


    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Videolist');
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
            $map['bid']=$this->auth->id;
            $total = $this->model
                ->with('user')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('user')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => &$v) {
                $groups = json_decode($v['type'],true);
                $arr=[];
                foreach ($groups as $k1=>$v1){
                    $Category=model('Category')->where(['id'=>$v1])->find();
                    $arr[$v1]=isset($Category['name'])?$Category['name']:'';    
                }
                $list[$k]['type'] = implode(',', array_keys($arr));
                $list[$k]['type_text'] = implode(',', array_values($arr));
            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        //$dds=['1'=>'视频','2'=>'音频','3'=>'小说','4'=>'图片','5'=>'图文'];
        $dds=['1'=>'视频','4'=>'图片'];
        $this->view->assign("mimetypeList", $dds);
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
        $this->error(__('No Results were found'));
        
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                if($params['lx']==1){
                    $params['type']=json_encode($params['type']);
                    $params['adddd']=json_encode($params['adddd']);
                }
                if($params['lx']==2){
                    $params['type']=json_encode($params['type2']);
                }
                if($params['lx']==3){
                    $params['type']=json_encode($params['type3']);
                }
                if($params['lx']==4){
                    $params['type']=json_encode($params['type4']);
                }
                if($params['lx']==5){
                    $params['type']=json_encode($params['type5']);
                }
                unset($params['type3']);
                unset($params['type2']);
                unset($params['type4']);
                unset($params['type5']);
                $result = $row->save($params);
                if ($result === false) {
                    $this->error($this->model->getError());
                }
                $this->success();
            }else{
                $this->error();
            }
        }
        
        $this->view->assign('groupids', json_decode($row['type'],true));
        $this->view->assign('addddid', json_decode($row['adddd'],true));
        //$this->view->assign('yearqid', json_decode($row['yearq'],true));
        
        $this->view->assign('row', $row);
        $this->view->assign('groupdata', $this->fenlei('type'));
        $this->view->assign('adddddata', $this->fenlei('adddd'));
        $this->view->assign('yearqdata', $this->fenlei('year'));
        $this->view->assign('type2data', $this->fenlei('type2'));
        $this->view->assign('type3data', $this->fenlei('type3'));
        $this->view->assign('type4data', $this->fenlei('type4'));
        $this->view->assign('type5data', $this->fenlei('type5'));
        return $this->view->fetch();
    }
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
 
                if($params['lx']==1){
                    $params['type']=json_encode($params['type']);
                    $params['adddd']=json_encode($params['adddd']);
                }
                if($params['lx']==2){
                    $params['type']=json_encode($params['type2']);
                }
                if($params['lx']==3){
                    $params['type']=json_encode($params['type3']);
                }
                if($params['lx']==4){
                    $params['type']=json_encode($params['type4']);
                }
                if($params['lx']==5){
                    $params['type']=json_encode($params['type5']);
                }
                unset($params['type3']);
                unset($params['type2']);
                unset($params['type4']);
                unset($params['type5']);
                $result = $this->model->save($params);
                if ($result === false) {
                    $this->error($this->model->getError());
                }
                $this->success();
            }else{
                $this->error();
            }
        }
        $this->view->assign('groupdata', $this->fenlei('type'));
        $this->view->assign('adddddata', $this->fenlei('adddd'));
        $this->view->assign('yearqdata', $this->fenlei('year'));
        $this->view->assign('type2data', $this->fenlei('type2'));
        $this->view->assign('type3data', $this->fenlei('type3'));
        $this->view->assign('type4data', $this->fenlei('type4'));
        $this->view->assign('type5data', $this->fenlei('type5'));
        $this->view->assign("id", $this->auth->id);
        return $this->view->fetch();
    }
    public function fenlei($tpie='type')
    {
        $tree = Tree::instance();
        $this->model = model('app\common\model\Category');
        $tree->init(collection($this->model->where(['type'=>$tpie])->order('weigh desc,id desc')->field('id,pid,name,nickname,image,type,flag')->select())->toArray(), 'pid');
        $fenlei = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $groupdata=[];
        foreach ($fenlei as $k => $v) {
                $groupdata[$v['id']] = $v['name'];
        }
        return $groupdata;
    }
    
     

}
