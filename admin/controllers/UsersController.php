<?php
namespace App\Controllers;

use App\Controller;
use App\Models\User;

class UsersController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->requireRole('Administrador');
        $this->userModel = new User();
    }

    public function index() {
        $page = (int)($this->getQueryParams()['page'] ?? 1);
        $search = $this->getQueryParams()['search'] ?? '';
        
        $users = $this->userModel->getAllWithRoles($page, $search);
        $roles = $this->db->query("SELECT * FROM roles")->fetchAll();
        
        $this->view('admin/users/index', [
            'pageTitle' => 'Gestión de Usuarios',
            'users' => $users,
            'roles' => $roles,
            'search' => $search
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->getPostData();
            $errors = $this->userModel->validate($data);
            
            if (empty($errors)) {
                try {
                    $this->userModel->create($data);
                    $this->redirect(
                        '/admin/users',
                        'Usuario creado exitosamente',
                        'success'
                    );
                } catch (\Exception $e) {
                    $errors['general'] = 'Error al crear el usuario';
                }
            }
            
            $roles = $this->db->query("SELECT * FROM roles")->fetchAll();
            
            $this->view('admin/users/create', [
                'pageTitle' => 'Crear Usuario',
                'roles' => $roles,
                'data' => $data,
                'errors' => $errors
            ]);
            
        } else {
            $roles = $this->db->query("SELECT * FROM roles")->fetchAll();
            
            $this->view('admin/users/create', [
                'pageTitle' => 'Crear Usuario',
                'roles' => $roles
            ]);
        }
    }

    public function edit($id) {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->redirect('/admin/users', 'Usuario no encontrado', 'danger');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->getPostData();
            $data['id'] = $id;
            
            $errors = $this->userModel->validate($data);
            
            if (empty($errors)) {
                try {
                    $this->userModel->update($id, $data);
                    $this->redirect(
                        '/admin/users',
                        'Usuario actualizado exitosamente',
                        'success'
                    );
                } catch (\Exception $e) {
                    $errors['general'] = 'Error al actualizar el usuario';
                }
            }
            
            $roles = $this->db->query("SELECT * FROM roles")->fetchAll();
            
            $this->view('admin/users/edit', [
                'pageTitle' => 'Editar Usuario',
                'user' => $user,
                'roles' => $roles,
                'data' => $data,
                'errors' => $errors
            ]);
            
        } else {
            $roles = $this->db->query("SELECT * FROM roles")->fetchAll();
            
            $this->view('admin/users/edit', [
                'pageTitle' => 'Editar Usuario',
                'user' => $user,
                'roles' => $roles
            ]);
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $user = $this->userModel->find($id);
            
            if (!$user) {
                if ($this->isAjax()) {
                    $this->json(['error' => 'Usuario no encontrado']);
                }
                $this->redirect('/admin/users', 'Usuario no encontrado', 'danger');
            }
            
            try {
                $this->userModel->delete($id);
                
                if ($this->isAjax()) {
                    $this->json(['success' => true]);
                }
                
                $this->redirect(
                    '/admin/users',
                    'Usuario eliminado exitosamente',
                    'success'
                );
                
            } catch (\Exception $e) {
                if ($this->isAjax()) {
                    $this->json(['error' => 'Error al eliminar el usuario']);
                }
                $this->redirect(
                    '/admin/users',
                    'Error al eliminar el usuario',
                    'danger'
                );
            }
        }
    }

    public function export($format = 'excel') {
        $users = $this->userModel->getAllWithRoles()['items'];
        
        $data = [
            ['ID', 'Usuario', 'Email', 'Rol', 'Estado', 'Fecha de Creación']
        ];
        
        foreach ($users as $user) {
            $data[] = [
                $user['id'],
                $user['username'],
                $user['email'],
                $user['rol_nombre'],
                $user['activo'] ? 'Activo' : 'Inactivo',
                date('d/m/Y H:i', strtotime($user['created_at']))
            ];
        }
        
        if ($format === 'pdf') {
            $html = '<h1>Listado de Usuarios</h1>';
            $html .= '<table border="1" cellpadding="5">';
            $html .= '<tr><th>ID</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th><th>Fecha</th></tr>';
            
            foreach ($users as $user) {
                $html .= '<tr>';
                $html .= '<td>' . $user['id'] . '</td>';
                $html .= '<td>' . htmlspecialchars($user['username']) . '</td>';
                $html .= '<td>' . htmlspecialchars($user['email']) . '</td>';
                $html .= '<td>' . htmlspecialchars($user['rol_nombre']) . '</td>';
                $html .= '<td>' . ($user['activo'] ? 'Activo' : 'Inactivo') . '</td>';
                $html .= '<td>' . date('d/m/Y H:i', strtotime($user['created_at'])) . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</table>';
            
            $this->generatePdf($html, 'usuarios.pdf');
            
        } else {
            $this->generateExcel($data, 'usuarios.xlsx');
        }
    }
} 