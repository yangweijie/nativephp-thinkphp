/**
 * NativePHP for ThinkPHP 数据库 JavaScript 客户端
 */
(function(window) {
    'use strict';

    // 检查是否在 Electron 环境中
    const isElectron = window.NativePHP !== undefined;

    // 获取基础客户端
    const NativeClient = window.NativeClient || {};

    // 扩展 NativeClient
    NativeClient.database = {
        /**
         * 当前数据库路径
         */
        _database: null,

        /**
         * 设置数据库路径
         * 
         * @param {string} database 数据库路径
         * @returns {object} this
         */
        database: function(database) {
            this._database = database;
            return this;
        },

        /**
         * 执行 SQL 查询
         * 
         * @param {string} sql SQL 查询语句
         * @param {Array} params 绑定参数
         * @returns {Promise<Array>} 查询结果
         */
        query: function(sql, params = []) {
            if (isElectron) {
                return window.NativePHP.database.query(sql, params);
            }
            return NativeClient.app._apiCall('database/query', {
                sql,
                params,
                database: this._database
            }, 'POST').then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Query failed');
                }
                return response.results;
            });
        },

        /**
         * 执行 SQL 语句
         * 
         * @param {string} sql SQL 语句
         * @returns {Promise<boolean>} 是否成功
         */
        exec: function(sql) {
            if (isElectron) {
                return window.NativePHP.database.exec(sql);
            }
            return NativeClient.app._apiCall('database/exec', {
                sql,
                database: this._database
            }, 'POST').then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Execution failed');
                }
                return true;
            });
        },

        /**
         * 获取最后插入的 ID
         * 
         * @returns {Promise<string>} 最后插入的 ID
         */
        lastInsertId: function() {
            if (isElectron) {
                return window.NativePHP.database.lastInsertId();
            }
            return NativeClient.app._apiCall('database/last-insert-id', {
                database: this._database
            }).then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Failed to get last insert ID');
                }
                return response.id;
            });
        },

        /**
         * 获取所有表
         * 
         * @returns {Promise<Array>} 表列表
         */
        getTables: function() {
            if (isElectron) {
                return window.NativePHP.database.getTables();
            }
            return NativeClient.app._apiCall('database/tables', {
                database: this._database
            }, 'POST').then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Failed to get tables');
                }
                return response.tables;
            });
        },

        /**
         * 获取表结构
         * 
         * @param {string} table 表名
         * @returns {Promise<Array>} 表结构
         */
        getTableStructure: function(table) {
            if (isElectron) {
                return window.NativePHP.database.getTableStructure(table);
            }
            return NativeClient.app._apiCall('database/table-structure', {
                table,
                database: this._database
            }, 'POST').then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Failed to get table structure');
                }
                return response.structure;
            });
        },

        /**
         * 创建表
         * 
         * @param {string} table 表名
         * @param {Array} columns 列定义
         * @returns {Promise<boolean>} 是否成功
         */
        createTable: function(table, columns) {
            if (isElectron) {
                return window.NativePHP.database.createTable(table, columns);
            }
            return NativeClient.app._apiCall('database/create-table', {
                table,
                columns,
                database: this._database
            }, 'POST').then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Failed to create table');
                }
                return true;
            });
        },

        /**
         * 删除表
         * 
         * @param {string} table 表名
         * @returns {Promise<boolean>} 是否成功
         */
        dropTable: function(table) {
            if (isElectron) {
                return window.NativePHP.database.dropTable(table);
            }
            return NativeClient.app._apiCall('database/drop-table', {
                table,
                database: this._database
            }, 'POST').then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Failed to drop table');
                }
                return true;
            });
        },

        /**
         * 插入数据
         * 
         * @param {string} table 表名
         * @param {object} data 数据
         * @returns {Promise<number>} 插入的 ID
         */
        insert: function(table, data) {
            // 构建 SQL 语句
            const columns = Object.keys(data);
            const placeholders = columns.map(() => '?').join(', ');
            const values = columns.map(column => data[column]);
            
            const sql = `INSERT INTO ${table} (${columns.join(', ')}) VALUES (${placeholders})`;
            
            return this.query(sql, values).then(() => {
                return this.lastInsertId();
            });
        },

        /**
         * 更新数据
         * 
         * @param {string} table 表名
         * @param {object} data 数据
         * @param {object} where 条件
         * @returns {Promise<number>} 影响的行数
         */
        update: function(table, data, where) {
            // 构建 SET 子句
            const setColumns = Object.keys(data);
            const setClause = setColumns.map(column => `${column} = ?`).join(', ');
            const setValues = setColumns.map(column => data[column]);
            
            // 构建 WHERE 子句
            const whereColumns = Object.keys(where);
            const whereClause = whereColumns.map(column => `${column} = ?`).join(' AND ');
            const whereValues = whereColumns.map(column => where[column]);
            
            // 合并参数
            const params = [...setValues, ...whereValues];
            
            const sql = `UPDATE ${table} SET ${setClause} WHERE ${whereClause}`;
            
            return this.query(sql, params);
        },

        /**
         * 删除数据
         * 
         * @param {string} table 表名
         * @param {object} where 条件
         * @returns {Promise<number>} 影响的行数
         */
        delete: function(table, where) {
            // 构建 WHERE 子句
            const whereColumns = Object.keys(where);
            const whereClause = whereColumns.map(column => `${column} = ?`).join(' AND ');
            const whereValues = whereColumns.map(column => where[column]);
            
            const sql = `DELETE FROM ${table} WHERE ${whereClause}`;
            
            return this.query(sql, whereValues);
        },

        /**
         * 查询数据
         * 
         * @param {string} table 表名
         * @param {Array} columns 列
         * @param {object} where 条件
         * @param {object} options 选项
         * @returns {Promise<Array>} 查询结果
         */
        select: function(table, columns = ['*'], where = {}, options = {}) {
            // 构建 SELECT 子句
            const selectClause = Array.isArray(columns) ? columns.join(', ') : columns;
            
            // 构建 WHERE 子句
            const whereColumns = Object.keys(where);
            const whereClause = whereColumns.length > 0 ? 
                whereColumns.map(column => `${column} = ?`).join(' AND ') : '';
            const whereValues = whereColumns.map(column => where[column]);
            
            // 构建 ORDER BY 子句
            const orderByClause = options.orderBy ? `ORDER BY ${options.orderBy}` : '';
            
            // 构建 LIMIT 子句
            const limitClause = options.limit ? `LIMIT ${options.limit}` : '';
            
            // 构建 OFFSET 子句
            const offsetClause = options.offset ? `OFFSET ${options.offset}` : '';
            
            // 构建完整 SQL 语句
            let sql = `SELECT ${selectClause} FROM ${table}`;
            if (whereClause) {
                sql += ` WHERE ${whereClause}`;
            }
            if (orderByClause) {
                sql += ` ${orderByClause}`;
            }
            if (limitClause) {
                sql += ` ${limitClause}`;
            }
            if (offsetClause) {
                sql += ` ${offsetClause}`;
            }
            
            return this.query(sql, whereValues);
        },

        /**
         * 获取单条数据
         * 
         * @param {string} table 表名
         * @param {Array} columns 列
         * @param {object} where 条件
         * @returns {Promise<object>} 查询结果
         */
        selectOne: function(table, columns = ['*'], where = {}) {
            return this.select(table, columns, where, { limit: 1 }).then(results => {
                return results.length > 0 ? results[0] : null;
            });
        },

        /**
         * 备份数据库
         * 
         * @param {string} path 备份路径
         * @returns {Promise<boolean>} 是否成功
         */
        backup: function(path = null) {
            if (isElectron) {
                return window.NativePHP.database.backup(path);
            }
            return NativeClient.app._apiCall('database/backup', {
                path,
                database: this._database
            }, 'POST').then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Backup failed');
                }
                return true;
            });
        },

        /**
         * 恢复数据库
         * 
         * @param {string} path 备份路径
         * @returns {Promise<boolean>} 是否成功
         */
        restore: function(path) {
            if (isElectron) {
                return window.NativePHP.database.restore(path);
            }
            return NativeClient.app._apiCall('database/restore', {
                path,
                database: this._database
            }, 'POST').then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Restore failed');
                }
                return true;
            });
        }
    };

})(window);
