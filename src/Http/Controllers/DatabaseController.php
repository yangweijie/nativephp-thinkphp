<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Database;

class DatabaseController
{
    /**
     * 执行 SQL 查询
     *
     * @param Request $request
     * @return Response
     */
    public function query(Request $request)
    {
        $sql = $request->param('sql');
        $params = $request->param('params', []);
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $results = Database::query($sql, $params);
            
            return json([
                'success' => true,
                'results' => is_array($results) ? $results : [],
                'affected_rows' => is_int($results) ? $results : 0,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 执行 SQL 语句
     *
     * @param Request $request
     * @return Response
     */
    public function exec(Request $request)
    {
        $sql = $request->param('sql');
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $success = Database::exec($sql);
            
            return json([
                'success' => $success,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 获取最后插入的 ID
     *
     * @param Request $request
     * @return Response
     */
    public function lastInsertId(Request $request)
    {
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $id = Database::lastInsertId();
            
            return json([
                'success' => true,
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 获取所有表
     *
     * @param Request $request
     * @return Response
     */
    public function tables(Request $request)
    {
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $tables = Database::getTables();
            
            return json([
                'success' => true,
                'tables' => $tables,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 获取表结构
     *
     * @param Request $request
     * @return Response
     */
    public function tableStructure(Request $request)
    {
        $table = $request->param('table');
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $structure = Database::getColumns($table);
            
            return json([
                'success' => true,
                'structure' => $structure,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 创建表
     *
     * @param Request $request
     * @return Response
     */
    public function createTable(Request $request)
    {
        $table = $request->param('table');
        $columns = $request->param('columns', []);
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $success = Database::createTable($table, $columns);
            
            return json([
                'success' => $success,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 删除表
     *
     * @param Request $request
     * @return Response
     */
    public function dropTable(Request $request)
    {
        $table = $request->param('table');
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $success = Database::dropTable($table);
            
            return json([
                'success' => $success,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 备份数据库
     *
     * @param Request $request
     * @return Response
     */
    public function backup(Request $request)
    {
        $path = $request->param('path');
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $success = Database::backup($path);
            
            return json([
                'success' => $success,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * 恢复数据库
     *
     * @param Request $request
     * @return Response
     */
    public function restore(Request $request)
    {
        $path = $request->param('path');
        $database = $request->param('database');
        
        if ($database) {
            Database::setDatabasePath($database);
        }
        
        try {
            $success = Database::restore($path);
            
            return json([
                'success' => $success,
            ]);
        } catch (\Exception $e) {
            return json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
