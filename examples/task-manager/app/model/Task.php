<?php

namespace app\model;

use think\Model;

class Task extends Model
{
    // 设置表名
    protected $name = 'task';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'title'       => 'string',
        'description' => 'string',
        'category_id' => 'int',
        'priority'    => 'string',
        'status'      => 'string',
        'due_date'    => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'completed_at'=> 'datetime',
    ];
    
    // 定义关联关系
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }
}
