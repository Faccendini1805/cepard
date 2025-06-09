<?php
namespace App;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new \PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            throw new \Exception("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            throw new \Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO " . $table . " (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $values) . ")";
        
        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $fields = array_map(function($field) {
            return "$field = ?";
        }, array_keys($data));
        
        $sql = "UPDATE " . $table . " SET " . implode(', ', $fields) . " WHERE " . $where;
        
        $params = array_merge(array_values($data), $whereParams);
        $this->query($sql, $params);
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM " . $table . " WHERE " . $where;
        $this->query($sql, $params);
    }

    private function __clone() {}
    private function __wakeup() {}
} 