<?php
namespace App;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
        return $stmt->fetch();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $this->db->update(
            $this->table,
            $data,
            "{$this->primaryKey} = ?",
            [$id]
        );
        
        return true;
    }

    public function delete($id) {
        $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$id]
        );
        return true;
    }

    public function where($conditions, $params = []) {
        $where = [];
        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        $stmt = $this->db->query($sql, array_values($params));
        return $stmt->fetchAll();
    }

    public function paginate($page = 1, $perPage = null) {
        if ($perPage === null) {
            $perPage = ITEMS_PER_PAGE;
        }
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$perPage, $offset]);
        
        $items = $stmt->fetchAll();
        
        $total = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}")->fetch()['count'];
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    protected function filterFillable($data) {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function validate($data) {
        return true;
    }
} 