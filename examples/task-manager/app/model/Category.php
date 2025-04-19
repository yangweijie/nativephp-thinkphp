<?php

namespace app\model;

use think\Model;

class Category extends Model
{
    // 设置表名
    protected $name = 'category';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'description' => 'string',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
    
    // 定义关联关系
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
