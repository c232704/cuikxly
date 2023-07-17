<?php

namespace app\common\model;

use think\Model;

/**
 * 会员模型
 */
class TaskOrder extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [];
    public function task()
    {
        return $this->belongsTo('Task', 'oid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
