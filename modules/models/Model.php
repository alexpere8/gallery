<?php

namespace Models;

require_once $base_path . 'modules\helpers.php';

class Model implements \Iterator
{
    protected const TABLE_NAME = '';
    protected const DEFAULT_ORDER = '';
    protected const RELATIONS = [];

    private static $connection = NULL;
    private static $connectionCount = 0;

    private $query = NULL;
    private $record = FALSE;

    public function __construct()
    {
        if (!self::$connection) {
            self::$connection = \Helpers\connectToDB();
        }
        self::$connectionCount++;
    }

    public function __destruct()
    {
        self::$connectionCount--;
        if (self::$connectionCount == 0) {
            self::$connection = NULL;
        }
    }

    public function runQuery($sql, $params = NULL)
    {
        if ($this->query) {
            $this->query->closeCursor();
        }

        $this->query = self::$connection->prepare($sql);
        if ($params) {
            foreach ($params as $key => $value) {
                $keyValue = (is_integer($key)) ? $key + 1 : $key;
                switch (gettype($value)) {
                    case 'integer':
                        $typeValue = \PDO::PARAM_INT;
                        break;
                    case 'boolean':
                        $typeValue = \PDO::PARAM_BOOL;
                        break;
                    case 'NULL':
                        $typeValue = \PDO::PARAM_NULL;
                        break;
                    default:
                        $typeValue = \PDO::PARAM_STR;
                }
                $this->query->bindValue($keyValue, $value, $typeValue);
            }
        }
        $this->query->execute();
    }

    public function select(
        $fields = '*',
        $reletions = NULL,
        $where = '',
        $params = NULL,
        $order = '',
        $offset = NULL,
        $limit = NULL,
        $group = '',
        $having = ''
        ) {
            $selectStr = 'SELECT ' . $fields . ' FROM ' . static::TABLE_NAME;

            if ($reletions) {
                foreach ($reletions as $ext_table) {
                    $rel = static::RELATIONS[$ext_table];
                    $relType = (key_exists('type', $rel)) ? $rel['type'] : 'INNER';
                    $selectStr .= ' ' . $relType . ' JOIN ' .  $ext_table .
                        ' ON ' . static::TABLE_NAME . '.' .
                        $rel['external'] . ' = ' . $ext_table . '.' .
                        $rel['primary'];
                }
            }

            if ($where) {
                $selectStr .= ' WHERE ' . $where;
            }

            if ($group) {
                $selectStr .= ' GROUP BY ' . $group;

                if ($having) {
                    $selectStr .= ' HAVING ' . $having;
                }
            }

            if ($order) {
                $selectStr .= ' ORDER BY ' . $order;
            } else {
                $selectStr .= ' ORDER BY ' . static::DEFAULT_ORDER;
            }

            if ($limit && $offset !== NULL) {
                $selectStr .= ' LIMIT ' . $offset . ', ' . $limit;
            }

            $selectStr .= ';';
            $this->runQuery($selectStr, $params);
    }

    public function current()
    {
        return $this->record;
    }

    public function key()
    {
        return 0;
    }

    public function next()
    {
        $this->record = $this->query->fetch(\PDO::FETCH_ASSOC);
    }

    public function rewind()
    {
        $this->record = $this->query->fetch(\PDO::FETCH_ASSOC);
    }

    public function valid()
    {
        return $this->record !== FALSE;
    }

    public function getRecord($fields = '*', $links = NULL, $where = '', $params = NULL)
    {
        $this->record = FALSE;
        $this->select($fields, $links, $where, $params);
        return $this->query->fetch(\PDO::FETCH_ASSOC);
    }

    public function get($value, $keyField = 'id', $fields = '*', $links = NULL)
    {
        return $this->getRecord($fields, $links, $keyField . ' = ?', [$value]);
    }

    public function getOr404($value, $keyField = 'id', $fields = '*', $links = NULL)
    {
        $rec = $this->get($value, $keyField, $fields, $links);
        if ($rec) {
            return $rec;
        } else {
            throw new \Page404Exception();
        }
    }

    protected function beforeInsert(&$fields) {}

    public function insert($fields)
    {
        static::beforeInsert($fields);
        $insertStr = 'INSERT INTO ' . static::TABLE_NAME;
        $insertValuesStr = $insertFieldsStr = '';
        foreach ($fields as $fieldName => $fieldValue) {
            if ($insertFieldsStr) {
                $insertFieldsStr .= ', ';
                $insertValuesStr .= ', ';
            }
            $insertFieldsStr .= $fieldName;
            $insertValuesStr .= ':' . $fieldName;
        }
        $insertStr .= ' (' . $insertFieldsStr . ') VALUES (' . $insertValuesStr . ');';
        $this->runQuery($insertStr, $fields);
        $lastInsertId = self::$connection -> lastInsertId();
        return $lastInsertId;
    }

    protected function beforeUpdate(&$fields, $value, $keyField = 'id') {}
    
    public function update($fields, $value, $keyField = 'id')
    {
        static::beforeUpdate($fields, $value, $keyField);
        $updateStr = 'UPDATE ' . static::TABLE_NAME . ' SET ';
        $updateFieldsStr = '';
        foreach ($fields as $fieldName => $fieldsValue) {
            if ($updateFieldsStr) {
                $updateFieldsStr .= ', ';
            }
            $updateFieldsStr .= $fieldName . ' = :' . $fieldName;
        }
        $updateStr .= $updateFieldsStr . ' WHERE ' . $keyField . ' = :__key;';
        $fields['__key'] = $value;
        $this->runQuery($updateStr, $fields);
    }

    protected function beforeDelete($value, $keyField = 'id') {}

    public function delete($value, $keyField = 'id')
    {
        static::beforeDelete($value, $keyField);
        $deleteStr = 'DELETE FROM ' . static::TABLE_NAME;
        $deleteStr .= ' WHERE ' . $keyField . ' = ?;';
        $this->runQuery($deleteStr, [$value]);
    }

    public function getAll(
        $fields = '*',
        $links = NULL,
        $where = '',
        $params = NULL,
        $order = '',
        $offset = NULL,
        $limit = NULL,
        $group = '',
        $having = ''
        ) {
            $this->select($fields, $links, $where, $params, $order,
                $offset, $limit, $group, $having);
            return $this->query->fetchAll(\PDO::FETCH_ASSOC);
    }
}