<?php
namespace App\Models;

use App\Model;
use App\Database;

class User extends Model {
    protected $table = 'usuarios';
    protected $fillable = ['username', 'password', 'email', 'rol_id', 'activo'];

    public function validate($data) {
        $errors = [];

        // Validar username
        if (empty($data['username'])) {
            $errors['username'] = 'El nombre de usuario es requerido';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'El nombre de usuario debe tener al menos 3 caracteres';
        } elseif ($this->usernameExists($data['username'], $data['id'] ?? null)) {
            $errors['username'] = 'Este nombre de usuario ya está en uso';
        }

        // Validar email
        if (empty($data['email'])) {
            $errors['email'] = 'El email es requerido';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido';
        } elseif ($this->emailExists($data['email'], $data['id'] ?? null)) {
            $errors['email'] = 'Este email ya está en uso';
        }

        // Validar password en creación
        if (!isset($data['id'])) {
            if (empty($data['password'])) {
                $errors['password'] = 'La contraseña es requerida';
            } elseif (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
                $errors['password'] = 'La contraseña debe tener al menos ' . PASSWORD_MIN_LENGTH . ' caracteres';
            }
        }

        // Validar rol
        if (empty($data['rol_id'])) {
            $errors['rol_id'] = 'El rol es requerido';
        } elseif (!$this->rolExists($data['rol_id'])) {
            $errors['rol_id'] = 'El rol seleccionado no es válido';
        }

        return $errors;
    }

    public function create($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return parent::create($data);
    }

    public function update($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        return parent::update($id, $data);
    }

    public function getRol() {
        $stmt = $this->db->query(
            "SELECT r.* FROM roles r 
             JOIN usuarios u ON u.rol_id = r.id 
             WHERE u.id = ?",
            [$this->{$this->primaryKey}]
        );
        return $stmt->fetch();
    }

    protected function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->query($sql, $params)->fetch();
        return $result['count'] > 0;
    }

    protected function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->query($sql, $params)->fetch();
        return $result['count'] > 0;
    }

    protected function rolExists($rolId) {
        $result = $this->db->query(
            "SELECT COUNT(*) as count FROM roles WHERE id = ?",
            [$rolId]
        )->fetch();
        return $result['count'] > 0;
    }

    public function getAllWithRoles($page = 1, $search = '') {
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT u.*, r.nombre as rol_nombre 
                FROM usuarios u 
                JOIN roles r ON u.rol_id = r.id";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE u.username LIKE ? OR u.email LIKE ?";
            $params = ["%$search%", "%$search%"];
        }
        
        $sql .= " ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $items = $this->db->query($sql, $params)->fetchAll();
        
        $sqlCount = "SELECT COUNT(*) as count 
                     FROM usuarios u 
                     JOIN roles r ON u.rol_id = r.id";
        if (!empty($search)) {
            $sqlCount .= " WHERE u.username LIKE ? OR u.email LIKE ?";
            $paramsCount = ["%$search%", "%$search%"];
        } else {
            $paramsCount = [];
        }
        
        $total = $this->db->query($sqlCount, $paramsCount)->fetch()['count'];
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
} 