<?php

namespace app\admin\controller\txjl;

use app\common\controller\Backend;
use app\common\model\MoneyLog;
use app\admin\model\Admin;
use think\Session;
use think\Db;
/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Txjl extends Backend
{

    protected $relationSearch = true;


    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Txjl');
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
                if($row['uid']>0){
                    $this->bohui($row['uid'],$row['money'],'提现驳回');
                }
                if($row['admin_id']>0){
                    $this->htbohui($row['admin_id'],$row['money'],'提现驳回');
                }
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
    public function htbohui($uid,$money,$tp='提现驳回'){
            $vadmin=Db::name('admin')->where('id',$uid)->find();
            if(!$vadmin){
                $this->error('会员id不正确');    
            }
            Db::startTrans();
            try {
                $moneys=$money;
                //更新会员信息
                $before = $vadmin['money'];
                $after = $vadmin['money'] + $moneys;
                Db::name('admin') ->where('id',$uid)->setInc('money', $moneys);
                //写入日志
                Db::name('admin_money_log')->insertGetId(['admin_id' =>$uid, 'money' => $moneys,'createtime' => time(), 'before' => $before,'after' => $after, 'memo' => $tp]);
                
               
                
                Db::commit();
            }catch (Exception $e){
                Db::rollback();
                $this->error($e->getMessage());
            }
    }
    public function bohui($uid,$money,$tp='提现驳回'){
            $user = \app\common\model\User::getById($uid);
            if(!$user){
                $this->error('会员id不正确');    
            }
            $before = $user->money;
            $after = $user->money + $money;
            Db::startTrans();
            try {
                $user->save(['money' => $after]);
                //写入日志
                MoneyLog::create(['user_id' => $user['id'], 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => $tp]);
                Db::commit();
            }catch (Exception $e){
                Db::rollback();
                $this->error($e->getMessage());
            }
    }

}
