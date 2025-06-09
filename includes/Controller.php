<?php
namespace App;

abstract class Controller {
    protected $auth;
    protected $db;

    public function __construct() {
        $this->auth = Auth::getInstance();
        $this->db = Database::getInstance();
        
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login.php');
        }
    }

    protected function view($template, $data = []) {
        extract($data);
        
        ob_start();
        require APP_PATH . "/templates/$template.php";
        $content = ob_get_clean();
        
        require APP_PATH . '/templates/layout.php';
    }

    protected function redirect($path, $message = null, $type = 'info') {
        if ($message) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = $type;
        }
        
        header('Location: ' . APP_URL . $path);
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validateCsrf() {
        $token = $_POST['csrf_token'] ?? '';
        
        try {
            $this->auth->validateCsrfToken($token);
            return true;
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Token CSRF inválido']);
            } else {
                $this->redirect('/error.php', 'Token CSRF inválido', 'danger');
            }
        }
    }

    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function requireRole($role) {
        $this->auth->requireRole($role);
    }

    protected function generatePdf($html, $filename = 'documento.pdf') {
        require_once APP_PATH . '/vendor/autoload.php';
        
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(APP_NAME);
        $pdf->SetTitle($filename);
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pdf->AddPage();
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $pdf->Output($filename, 'D');
        exit;
    }

    protected function generateExcel($data, $filename = 'documento.xlsx') {
        require_once APP_PATH . '/vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Agregar datos
        $sheet->fromArray($data, null, 'A1');
        
        // Crear el archivo
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Headers para descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    protected function getPostData() {
        return filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING) ?? [];
    }

    protected function getQueryParams() {
        return filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING) ?? [];
    }
} 