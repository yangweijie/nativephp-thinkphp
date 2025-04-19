<?php

namespace app\service;

use app\model\Note;
use app\model\Tag;
use think\facade\Db;

class NoteService
{
    /**
     * 获取笔记列表
     *
     * @param int $categoryId
     * @param string $tag
     * @param string $search
     * @return array
     */
    public function getNotes($categoryId = 0, $tag = '', $search = '')
    {
        $query = Note::order('updated_at', 'desc');
        
        // 按分类筛选
        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }
        
        // 按标签筛选
        if (!empty($tag)) {
            $query->whereExists(function ($query) use ($tag) {
                $query->table('note_tags')
                    ->join('tags', 'tags.id = note_tags.tag_id')
                    ->where('tags.name', $tag)
                    ->whereRaw('note_tags.note_id = notes.id');
            });
        }
        
        // 按关键词搜索
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->whereOr('content', 'like', "%{$search}%");
            });
        }
        
        // 获取笔记及其关联数据
        $notes = $query->with(['category', 'tags'])->select();
        
        return $notes;
    }
    
    /**
     * 搜索笔记
     *
     * @param string $keyword
     * @return array
     */
    public function searchNotes($keyword)
    {
        return Note::where('title', 'like', "%{$keyword}%")
            ->whereOr('content', 'like', "%{$keyword}%")
            ->order('updated_at', 'desc')
            ->select();
    }
    
    /**
     * 更新笔记标签
     *
     * @param int $noteId
     * @param array $tagIds
     * @return bool
     */
    public function updateNoteTags($noteId, array $tagIds)
    {
        // 删除旧的标签关联
        $this->deleteNoteTags($noteId);
        
        // 添加新的标签关联
        foreach ($tagIds as $tagId) {
            Db::table('note_tags')->insert([
                'note_id' => $noteId,
                'tag_id' => $tagId,
            ]);
        }
        
        return true;
    }
    
    /**
     * 删除笔记标签关联
     *
     * @param int $noteId
     * @return bool
     */
    public function deleteNoteTags($noteId)
    {
        return Db::table('note_tags')->where('note_id', $noteId)->delete();
    }
    
    /**
     * 初始化数据库
     *
     * @return bool
     */
    public function initDatabase()
    {
        // 创建笔记表
        Db::execute("
            CREATE TABLE IF NOT EXISTS notes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                content TEXT,
                category_id INTEGER DEFAULT 0,
                created_at DATETIME,
                updated_at DATETIME
            )
        ");
        
        // 创建分类表
        Db::execute("
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
        
        // 创建标签表
        Db::execute("
            CREATE TABLE IF NOT EXISTS tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
        
        // 创建笔记标签关联表
        Db::execute("
            CREATE TABLE IF NOT EXISTS note_tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                note_id INTEGER NOT NULL,
                tag_id INTEGER NOT NULL,
                UNIQUE(note_id, tag_id)
            )
        ");
        
        return true;
    }
}
