<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Crear la conexión a la base de datos
$db = new DB();
$pdo = $db->getConnection();  // Obtener la conexión a la base de datos

// Responder a las solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

class Dispatcher {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function init() {
        echo("Prueba"); // Esto es solo para ver si la inicialización funciona
        exit;
    }

    public function dispatch($action) {
        $allowedActions = [
            'list', 'get', 'save', 'delete', 'saveCsv', 
            'listUsuarios', 'getUsuario', 'saveUsuario', 'deleteUsuario'
        ];

        if (!in_array($action, $allowedActions)) {
            $this->responseJson(['success' => false, 'message' => 'Acción no válida'], 400);
            return;
        }

        try {
            switch ($action) {
                // Acciones para la tabla citas
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
                case 'saveCsv':
                    $this->saveCsv();
                    break;

                // Acciones para la tabla usuario
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
            }
        } catch (Exception $e) {
            $this->responseJson(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Métodos para manejar las citas
    private function list() {
        $stmt = $this->pdo->query("SELECT * FROM citas");
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->responseJson($appointments);
    }

    private function get() {
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde los parámetros de la URL

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
        // Obtenemos los datos del formulario desde $_POST
        $data = $_POST;
    
        // Verificamos si los campos obligatorios están presentes
        if (!isset($data['uuid']) || !isset($data['date']) || !isset($data['time']) || !isset($data['patient'])) {
            $this->responseJson(['success' => false, 'message' => 'Faltan datos obligatorios'], 400);
            return;
        }
    
        // Preparamos la consulta para insertar los datos en la base de datos
        $stmt = $this->pdo->prepare("INSERT INTO citas (uuid, date, time, patient, description) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $data['uuid'], 
            $data['date'], 
            $data['time'], 
            $data['patient'], 
            $data['description'] ?? ''  // Usamos null coalescing para manejar la descripción opcional
        ]);
    
        // Verificamos si la inserción fue exitosa
        if ($result) {
            $this->responseJson(['success' => true, 'message' => 'Cita guardada correctamente'], 200);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Error al guardar la cita'], 500);
        }
    }
    

    private function delete() {
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde los parámetros de la URL

        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);
            return;
        }

        $stmt = $this->pdo->prepare("DELETE FROM citas WHERE uuid = ?");
        $result = $stmt->execute([$uuid]);

        if ($result) {
            $this->responseJson(['success' => true, 'message' => 'Cita eliminada correctamente'], 200);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Error al eliminar la cita'], 500);
        }
    }

    private function saveCsv() {
        // Lógica para guardar citas desde un archivo CSV
    }

    // Métodos para manejar los usuarios
    private function listUsuarios() {
        $stmt = $this->pdo->query("SELECT * FROM usuarios");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->responseJson($usuarios);
    }

    private function getUsuario() {
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde los parámetros de la URL

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
        $data = json_decode(file_get_contents('php://input'), true); // Obtener los datos del body de la solicitud

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
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde los parámetros de la URL

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

    // Método para responder con datos en formato JSON
    private function responseJson($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}

// Instanciamos y ejecutamos el Dispatcher
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$dispatcher = new Dispatcher($pdo); // Pasamos la conexión a la base de datos
$dispatcher->dispatch($action);

?>
