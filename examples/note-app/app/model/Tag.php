<?php

namespace app\model;

use think\Model;

class Tag extends Model
{
    protected $table = 'tags';
    
    // 获取标签下的笔记
    public function notes()
    {
        return $this->belongsToMany(Note::class, 'note_tags', 'tag_id', 'note_id');
    }
}
