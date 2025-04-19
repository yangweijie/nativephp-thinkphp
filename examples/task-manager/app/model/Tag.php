<?php

namespace app\model;

use think\Model;

class Tag extends Model
{
    // 设置表名
    protected $name = 'tag';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
    
    // 定义关联关系
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_tag');
    }
}
