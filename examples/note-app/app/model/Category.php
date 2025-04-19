<?php

namespace app\model;

use think\Model;

class Category extends Model
{
    protected $table = 'categories';
    
    // 获取分类下的笔记
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
