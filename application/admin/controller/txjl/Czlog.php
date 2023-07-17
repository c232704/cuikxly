<?php
namespace app\admin\controller\txjl;
use app\common\controller\Backend;
use think\Db;
/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Czlog extends Backend
{

    protected $relationSearch = true;


    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('CzLog');
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
                ->with('user')
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('user')
                ->where($where)
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
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if($params['iscl']==3 and $row['iscl']!=3){
                $this->bohui($row['uid'],$row['money'],'提现驳回');
            }
        }
        return parent::edit($ids);
    }
    public function add()
    {
        return parent::add();
    }
    public function info($pid = NULL)
    {
        $row = $this->model->get($pid);
        if (!$row)
            $this->error(__('No Results were found'));
        return parent::edit($pid);
    }


}
