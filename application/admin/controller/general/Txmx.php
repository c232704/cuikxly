<?php

namespace app\admin\controller\general;

use app\admin\model\Admin;
use app\common\controller\Backend;
use fast\Random;
use think\Session;
use think\Validate;
use app\common\model\Txjl;
use think\Db;
/**
 * 个人配置
 *
 * @icon fa fa-user
 */
class Txmx extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $model = model('Txjl');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $model
                ->where($where)
                ->where('admin_id', $this->auth->id)
                ->order($sort, $order)
                ->count();

            $list = $model
                ->where($where)
                ->where('admin_id', $this->auth->id)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        $admin = Admin::get($this->auth->id);
        Session::set("admin", $admin->toArray());
        return $this->view->fetch();
    }

    /**
     * 更新个人信息
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $site=config('site');
                $money=$params['money'];
                if($money<$site['zstx']){
                    $this->error($site['txbz']);  
                }
                if(!$params['type']){
                    $this->error('请填写提现类型');
                }
                if(!$params['name']){
                    $this->error('请填写提现名字');
                }
                if(!$params['cord']){
                    $this->error('请填写提现帐号');
                }
                $user_id=$this->auth->id;
                $vadmin=Db::name('admin')->where('id',$user_id)->find();
                if ($vadmin && $money != 0) {
                    if($vadmin['money']<$money){
                       $this->error('余额不足'); 
                    }
                    Db::startTrans();
                    try {
                        $moneys=$money;
                        //更新会员信息
                        $before = $vadmin['money'];
                        $after = $vadmin['money'] - $moneys;
                        Db::name('admin') ->where('id',$user_id)->setDec('money', $moneys);
                        //写入日志
                        Db::name('admin_money_log')->insertGetId(['admin_id' =>$user_id, 'money' => $moneys,'createtime' => time(),'before' => $before, 'after' => $after, 'memo' => '用户提现']);
                        
                        $site['txsxf']=isset($site['txsxf'])?$site['txsxf']:0;
                        $sxf=$money*$site['txsxf'];
                        $up=[
                                'admin_id' => $user_id,
                                'type' => $params['type'],
                                'name' => $params['name'],
                                'cord' => $params['cord'], 
                                'money' => $money, 
                                'moneydz' => $money-$sxf, 
                                'sxf' => $sxf,
                                'iscl' => 1,
                                'islx' => 2,
                                'memo' => '后台用户提现'
                            ];
                        //写入提现记录
                        Txjl::create($up);  
                        $admin = Admin::get($this->auth->id);
                        Session::set("admin", $admin->toArray());
                        Db::commit();
                        $this->success('提交成功');
                    }catch (Exception $e){
                        Db::rollback();
                        $this->error($e->getMessage());
                    }
                    
                }else{
                   $this->error(__('金额不对')); 
                }
            }
            $this->error();
        }
        return;
    }
}
