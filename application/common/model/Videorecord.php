<?php

namespace app\common\model;

use think\Model;

/**
 * 会员模型
 */
class Videorecord extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [];
    public function user()
    {
        return $this->belongsTo('User', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function users()
    {
        return $this->belongsTo('User', 'uid', 'id', [], 'LEFT')->field('id,avatar,nickname,istel,mobile,isrz,level,group_id')->setEagerlyType(0);
    }
}
