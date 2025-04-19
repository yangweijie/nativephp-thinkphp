<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Database new() 创建新实例
 * @method static \Native\ThinkPHP\Database useRemote(bool $useRemote = true) 设置是否使用远程数据库
 * @method static \Native\ThinkPHP\Database setDatabasePath(string $path) 设置数据库路径
 * @method static string|null getDatabasePath() 获取数据库路径
 * @method static \PDO connect() 连接数据库
 * @method static void disconnect() 断开数据库连接
 * @method static array|int query(string $sql, array $params = []) 执行 SQL 查询
 * @method static bool exec(string $sql) 执行 SQL 语句
 * @method static string lastInsertId() 获取最后插入的 ID
 * @method static bool beginTransaction() 开始事务
 * @method static bool commit() 提交事务
 * @method static bool rollBack() 回滚事务
 * @method static bool tableExists(string $table) 检查表是否存在
 * @method static bool createTable(string $table, array $columns) 创建表
 * @method static bool dropTable(string $table) 删除表
 * @method static int insert(string $table, array $data) 插入数据
 * @method static int update(string $table, array $data, string $where, array $params = []) 更新数据
 * @method static int delete(string $table, string $where, array $params = []) 删除数据
 * @method static array select(string $table, string $columns = '*', string|null $where = null, array $params = [], string|null $orderBy = null, int|null $limit = null, int|null $offset = null) 查询数据
 * @method static array|null selectOne(string $table, string $columns = '*', string|null $where = null, array $params = [], string|null $orderBy = null) 获取单行数据
 * @method static array getColumns(string $table) 获取表的所有列
 * @method static array getTables() 获取所有表
 * @method static bool backup(string|null $path = null) 备份数据库
 * @method static bool restore(string $path) 恢复数据库
 *
 * @see \Native\ThinkPHP\Database
 */
class Database extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.database';
    }
}
