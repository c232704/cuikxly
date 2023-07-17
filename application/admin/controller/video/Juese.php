<?php

namespace app\admin\controller\video;

use app\common\controller\Backend;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Juese extends Backend
{

    protected $relationSearch = true;


    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Juese');
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
               $map['juese.pid']= input('pid');
            }
            $total = $this->model
                ->with('videolist,user')
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('videolist,user')
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
        return parent::edit($ids);
    }
    public function add($pid = NULL)
    {
        
        $Videolist = model('Videolist')->get($pid);
        if($Videolist){
           return parent::add(); 
        }else{
            echo '请到视频管理里添加';
        }
        
    }

}
