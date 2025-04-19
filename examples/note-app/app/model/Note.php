<?php

namespace app\model;

use think\Model;

class Note extends Model
{
    protected $table = 'notes';
    
    // 获取笔记的标签
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'note_tags', 'note_id', 'tag_id');
    }
    
    // 获取笔记的分类
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
