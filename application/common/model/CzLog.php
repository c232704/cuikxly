<?php

namespace app\common\model;

use think\Model;

/**
 * 会员余额日志模型
 */
class CzLog Extends Model
{

    // 表名
    protected $name = 'paylog';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [];
    public function user()
    {
        return $this->belongsTo('User', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function users()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->field('id,avatar,nickname,istel,mobile,isrz,level,group_id')->setEagerlyType(0);
    }
}
