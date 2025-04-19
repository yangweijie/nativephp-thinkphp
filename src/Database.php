<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use PDO;
use PDOException;
use Native\ThinkPHP\Client\Client;

class Database
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * 数据库连接
     *
     * @var PDO|null
     */
    protected $connection = null;

    /**
     * 数据库路径
     *
     * @var string|null
     */
    protected $databasePath = null;

    /**
     * 是否使用远程数据库
     *
     * @var bool
     */
    protected $useRemote = false;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
        $this->databasePath = $this->getDefaultDatabasePath();
        $this->useRemote = $this->app->config->get('native.database.use_remote', false);
    }

    /**
     * 获取默认数据库路径
     *
     * @return string
     */
    protected function getDefaultDatabasePath()
    {
        $appDataPath = $this->app->getRuntimePath() . 'database/';

        if (!is_dir($appDataPath)) {
            mkdir($appDataPath, 0755, true);
        }

        return $appDataPath . 'native.sqlite';
    }

    /**
     * 设置数据库路径
     *
     * @param string $path
     * @return $this
     */
    public function setDatabasePath($path)
    {
        $this->databasePath = $path;

        // 如果已经连接，则重新连接
        if ($this->connection) {
            $this->disconnect();
            $this->connect();
        }

        return $this;
    }

    /**
     * 获取数据库路径
     *
     * @return string|null
     */
    public function getDatabasePath()
    {
        return $this->databasePath;
    }

    /**
     * 连接数据库
     *
     * @return PDO|null
     */
    public function connect()
    {
        // 如果 PDO 扩展不可用，返回 null
        if (!extension_loaded('pdo_sqlite')) {
            return null;
        }

        if ($this->connection) {
            return $this->connection;
        }

        try {
            // 确保目录存在
            $dir = dirname($this->databasePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // 连接数据库
            $this->connection = new PDO('sqlite:' . $this->databasePath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connection;
        } catch (PDOException $e) {
            // 如果发生异常，返回 null
            return null;
        }
    }

    /**
     * 断开数据库连接
     *
     * @return void
     */
    public function disconnect()
    {
        $this->connection = null;
    }

    /**
     * 创建新实例
     *
     * @return \Native\ThinkPHP\Database
     */
    public static function new()
    {
        return new self(app());
    }

    /**
     * 设置是否使用远程数据库
     *
     * @param bool $useRemote
     * @return $this
     */
    public function useRemote($useRemote = true)
    {
        $this->useRemote = $useRemote;
        return $this;
    }

    /**
     * 执行 SQL 查询
     *
     * @param string $sql
     * @param array $params
     * @return array|int
     */
    public function query($sql, array $params = [])
    {
        // 如果 PDO 扩展不可用，返回空数组
        if (!extension_loaded('pdo_sqlite') && !$this->useRemote) {
            return [];
        }

        // 如果使用远程数据库，则通过 Client 发送请求
        if ($this->useRemote) {
            $response = $this->client->post('database/query', [
                'sql' => $sql,
                'params' => $params,
                'database' => $this->databasePath,
            ]);

            // 如果是 SELECT 查询，则返回结果集
            if (stripos(trim($sql), 'SELECT') === 0) {
                return $response->json('results') ?? [];
            }

            // 否则返回受影响的行数
            return (int) $response->json('affected_rows');
        }

        // 否则使用本地数据库
        try {
            $connection = $this->connect();

            // 如果连接失败，返回空数组
            if ($connection === null) {
                return [];
            }

            $statement = $connection->prepare($sql);
            $statement->execute($params);

            // 如果是 SELECT 查询，则返回结果集
            if (stripos(trim($sql), 'SELECT') === 0) {
                return $statement->fetchAll(PDO::FETCH_ASSOC);
            }

            // 否则返回受影响的行数
            return $statement->rowCount();
        } catch (PDOException $e) {
            // 如果发生异常，返回空数组
            return [];
        }
    }

    /**
     * 执行 SQL 语句
     *
     * @param string $sql
     * @return bool
     */
    public function exec($sql)
    {
        // 如果 PDO 扩展不可用，返回 false
        if (!extension_loaded('pdo_sqlite') && !$this->useRemote) {
            return false;
        }

        // 如果使用远程数据库，则通过 Client 发送请求
        if ($this->useRemote) {
            $response = $this->client->post('database/exec', [
                'sql' => $sql,
                'database' => $this->databasePath,
            ]);

            return (bool) $response->json('success');
        }

        // 否则使用本地数据库
        try {
            $connection = $this->connect();

            // 如果连接失败，返回 false
            if ($connection === null) {
                return false;
            }

            return $connection->exec($sql) !== false;
        } catch (PDOException $e) {
            // 如果发生异常，返回 false
            return false;
        }
    }

    /**
     * 获取最后插入的 ID
     *
     * @return string
     */
    public function lastInsertId()
    {
        // 如果使用远程数据库，则通过 Client 发送请求
        if ($this->useRemote) {
            $response = $this->client->get('database/last-insert-id', [
                'database' => $this->databasePath,
            ]);

            return $response->json('id') ?? '0';
        }

        // 否则使用本地数据库
        return $this->connect()->lastInsertId();
    }

    /**
     * 开始事务
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connect()->beginTransaction();
    }

    /**
     * 提交事务
     *
     * @return bool
     */
    public function commit()
    {
        return $this->connect()->commit();
    }

    /**
     * 回滚事务
     *
     * @return bool
     */
    public function rollBack()
    {
        return $this->connect()->rollBack();
    }

    /**
     * 检查表是否存在
     *
     * @param string $table
     * @return bool
     */
    public function tableExists($table)
    {
        $result = $this->query("SELECT name FROM sqlite_master WHERE type='table' AND name=?", [$table]);
        return !empty($result);
    }

    /**
     * 创建表
     *
     * @param string $table
     * @param array $columns
     * @return bool
     */
    public function createTable($table, array $columns)
    {
        $columnsDefinition = [];

        foreach ($columns as $name => $definition) {
            $columnsDefinition[] = "{$name} {$definition}";
        }

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (" . implode(', ', $columnsDefinition) . ")";

        return $this->exec($sql);
    }

    /**
     * 删除表
     *
     * @param string $table
     * @return bool
     */
    public function dropTable($table)
    {
        return $this->exec("DROP TABLE IF EXISTS {$table}");
    }

    /**
     * 插入数据
     *
     * @param string $table
     * @param array $data
     * @return int
     */
    public function insert($table, array $data)
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

        $this->query($sql, array_values($data));

        return (int) $this->lastInsertId();
    }

    /**
     * 更新数据
     *
     * @param string $table
     * @param array $data
     * @param string $where
     * @param array $params
     * @return int
     */
    public function update($table, array $data, $where, array $params = [])
    {
        $set = [];

        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$where}";

        return $this->query($sql, array_merge(array_values($data), $params));
    }

    /**
     * 删除数据
     *
     * @param string $table
     * @param string $where
     * @param array $params
     * @return int
     */
    public function delete($table, $where, array $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";

        return $this->query($sql, $params);
    }

    /**
     * 查询数据
     *
     * @param string $table
     * @param string $columns
     * @param string|null $where
     * @param array $params
     * @param string|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function select($table, $columns = '*', $where = null, array $params = [], $orderBy = null, $limit = null, $offset = null)
    {
        $sql = "SELECT {$columns} FROM {$table}";

        if ($where) {
            $sql .= " WHERE {$where}";
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";

            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->query($sql, $params);
    }

    /**
     * 获取单行数据
     *
     * @param string $table
     * @param string $columns
     * @param string|null $where
     * @param array $params
     * @param string|null $orderBy
     * @return array|null
     */
    public function selectOne($table, $columns = '*', $where = null, array $params = [], $orderBy = null)
    {
        $result = $this->select($table, $columns, $where, $params, $orderBy, 1);

        return $result ? $result[0] : null;
    }

    /**
     * 获取表的所有列
     *
     * @param string $table
     * @return array
     */
    public function getColumns($table)
    {
        $result = $this->query("PRAGMA table_info({$table})");

        $columns = [];

        foreach ($result as $column) {
            $columns[$column['name']] = [
                'type' => $column['type'],
                'notnull' => (bool) $column['notnull'],
                'default' => $column['dflt_value'],
                'primary' => (bool) $column['pk'],
            ];
        }

        return $columns;
    }

    /**
     * 获取所有表
     *
     * @return array
     */
    public function getTables()
    {
        $result = $this->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

        $tables = [];

        foreach ($result as $table) {
            $tables[] = $table['name'];
        }

        return $tables;
    }

    /**
     * 备份数据库
     *
     * @param string|null $path
     * @return bool
     */
    public function backup($path = null)
    {
        // 如果使用远程数据库，则通过 Client 发送请求
        if ($this->useRemote) {
            $response = $this->client->post('database/backup', [
                'database' => $this->databasePath,
                'path' => $path,
            ]);

            return (bool) $response->json('success');
        }

        // 否则使用本地数据库
        if (!$path) {
            $path = $this->databasePath . '.backup-' . date('YmdHis');
        }

        // 确保目录存在
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 备份数据库
        return copy($this->databasePath, $path);
    }

    /**
     * 恢复数据库
     *
     * @param string $path
     * @return bool
     */
    public function restore($path)
    {
        // 如果使用远程数据库，则通过 Client 发送请求
        if ($this->useRemote) {
            $response = $this->client->post('database/restore', [
                'database' => $this->databasePath,
                'path' => $path,
            ]);

            return (bool) $response->json('success');
        }

        // 否则使用本地数据库
        if (!file_exists($path)) {
            return false;
        }

        // 断开连接
        $this->disconnect();

        // 恢复数据库
        $result = copy($path, $this->databasePath);

        // 重新连接
        $this->connect();

        return $result;
    }
}
