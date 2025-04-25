<?php
header('Content-Type: application/json');  // Especifica que la respuesta será en formato JSON
header('Access-Control-Allow-Origin: *');  // Permite solicitudes desde cualquier dominio (CORS)
header('Access-Control-Allow-Methods: GET, POST, DELETE, HEAD, OPTIONS');  // Métodos HTTP permitidos
header('Access-Control-Allow-Headers: Content-Type');  // Permite encabezados de tipo Content-Type

// Incluir la clase de conexión a la base de datos
include_once('db.php');

class Dispatcher {
    private $pdo; 

    public function __construct() {
        $this->pdo = DB::getConnection();  
    }

    public function dispatch($action) {
        $allowedActions = [
            'list', 'calendar', 'formulario', 'login', 'logout', 'get', 'save', 'delete', 'listview', 'home',
            'listUsuarios', 'getUsuario', 'saveUsuario', 'deleteUsuario', 'index'
        ];

        if (!in_array($action, $allowedActions)) {
            $this->responseJson(['success' => false, 'message' => 'Acción no válida'], 400);  
            return;
        }

        try {

            switch ($action) {
                case 'list':  
                    $this->list();
                    break;
                case 'get':  
                    $this->get();
                    break;
                case 'save':  
                    $this->save();
                    break;
                case 'delete':  
                    $this->delete();
                    break;
                case 'listUsuarios':  
                    $this->listUsuarios();
                    break;
                case 'getUsuario':  
                    $this->getUsuario();
                    break;
                case 'saveUsuario':  
                    $this->saveUsuario();
                    break;
                case 'deleteUsuario': 
                    $this->deleteUsuario();
                    break;
               
                case 'index':
                case 'home':
                case 'formulario':
                     $this->get();
                case 'calendar':
                case 'listview':
                case 'login':
                case 'logout':
                    header("Location: http://localhost/proyecto-citas-medicas/index.php?action=$action"); 
                    exit;
            }
        } catch (Exception $e) {
            
            $this->responseJson(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function list() {
        $stmt = $this->pdo->query("SELECT * FROM citas");  
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);  
        $this->responseJson($appointments);  
    }

    private function get() {
        $uuid = $_GET['uuid'] ?? ''; 
        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400); 
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM citas WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);  
       
        if ($appointment) {
            $this->responseJson($appointment);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Cita no encontrada'], 404); 
        }
    }

    private function save() {
        $data = $_POST;  
        if (!isset($data['uuid']) || !isset($data['date']) || !isset($data['time']) || !isset($data['patient'])) {
            $this->responseJson(['success' => false, 'message' => 'Faltan datos obligatorios'], 400);  
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM citas WHERE uuid = ?");
        $stmt->execute([$data['uuid']]);
        $existingAppointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingAppointment) {
            $stmt = $this->pdo->prepare("UPDATE citas SET date = ?, time = ?, patient = ?, description = ? WHERE uuid = ?");
            $result = $stmt->execute([
                $data['date'],
                $data['time'],
                $data['patient'],
                $data['description'] ?? '',
                $data['uuid']
            ]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO citas (uuid, date, time, patient, description) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['uuid'],
                $data['date'],
                $data['time'],
                $data['patient'],
                $data['description'] ?? ''
            ]);
        }

        if ($result) {
            $this->responseJson(['success' => true, 'message' => 'Cita guardada correctamente'], 200);
        } else {
         
            $this->responseJson(['success' => false, 'message' => 'Error al guardar la cita'], 500);
        }
    }

    private function delete() {
        $uuid = $_GET['uuid'] ?? '';  
        
        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);  
            return;
        }

        $stmt = $this->pdo->prepare("DELETE FROM citas WHERE uuid = ?");
        $result = $stmt->execute([$uuid]);  
        if ($result) {
            header("Location: http://localhost/proyecto-citas-medicas/index.php?action=listview");
            exit;
        } else {
            $this->responseJson(['success' => false, 'message' => 'Error al eliminar la cita'], 500);  
        }
    }

    private function listUsuarios() {
        $stmt = $this->pdo->query("SELECT * FROM usuarios"); 
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        $this->responseJson($usuarios);  
    }

    private function getUsuario() {
        $uuid = $_GET['uuid'] ?? '';  

        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);  
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE uuid = ?");
        $stmt->execute([$uuid]);  
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);  
        if ($usuario) {
            $this->responseJson($usuario);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Usuario no encontrado'], 404);  
        }
    }

    private function saveUsuario() {
        $data = json_decode(file_get_contents('php://input'), true); 

        if (!$data || !isset($data['uuid']) || !isset($data['name']) || !isset($data['email'])) {
            $this->responseJson(['success' => false, 'message' => 'Faltan datos obligatorios'], 400);  
            return;
        }

        $stmt = $this->pdo->prepare("INSERT INTO usuarios (uuid, name, email) VALUES (?, ?, ?)");
        $result = $stmt->execute([$data['uuid'], $data['name'], $data['email']]);

        if ($result) {
            $this->responseJson(['success' => true, 'message' => 'Usuario guardado correctamente'], 200);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Error al guardar el usuario'], 500);
        }
    }

    private function deleteUsuario() {
        $uuid = $_GET['uuid'] ?? '';  
        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);  
            return;
        }

        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE uuid = ?");
        $result = $stmt->execute([$uuid]);  
        if ($result) {
            $this->responseJson(['success' => true, 'message' => 'Usuario eliminado correctamente'], 200);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Error al eliminar el usuario'], 500); 
        }
    }

    private function responseJson($data, $statusCode = 200) {
        header('Content-Type: application/json');  
        http_response_code($statusCode);  
        echo json_encode($data);  
        die();  
    }
}

$disp = new Dispatcher();
$action = $_GET['action'] ?? 'index';  
$disp->dispatch($action);  
?>