<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir el archivo de conexión a la base de datos
include_once('db.php');

class Dispatcher {
    private $pdo;

    // Constructor que recibe la conexión PDO
    public function __construct() {
        $this->pdo = DB::getConnection();
    }

    public function dispatch($action) {
        // Definir las acciones permitidas
        $allowedActions = [
            'list', 'calendar', 'formulario', 'login', 'logout', 'get', 'save', 'delete', 'listview', 'home',
            'listUsuarios', 'getUsuario', 'saveUsuario', 'deleteUsuario', 'index'
        ];

        if (!in_array($action, $allowedActions)) {
            $this->responseJson(['success' => false, 'message' => 'Acción no válida'], 400);
            return;
        }

        try {
            // Dependiendo de la acción, ejecutamos la acción correspondiente
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

                // Cargar la vista correspondiente
                case 'index':
                    $this->loadView('index');  // Vista 'index'
                    break;
                case 'home':
                    $this->loadView('home');  // Vista 'home'
                    break;
                case 'formulario':
                    $this->loadView('formulario');
                    break;
                case 'calendar':
                    $this->loadView('calendar');
                    break;
                case 'listview':
                    $this->listView();
                    break;
                case 'login':
                    $this->loadView('login');
                    break;
                case 'logout':
                    $this->loadView('logout');
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

    // Método para manejar las citas
    private function list() {
        $stmt = $this->pdo->query("SELECT * FROM citas");
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->responseJson($appointments);
    }

    // Método para cargar la vista de la lista de citas
    private function listView() {

        // Consulta a la base de datos
        $stmt = $this->pdo->query("SELECT uuid, date, time, patient, description FROM citas");
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Verificar que hemos obtenido las citas correctamente
        // var_dump($appointments); // Esto te permitirá verificar si la consulta está funcionando correctamente.
        
        // Comprobar si no hay citas
        if (!$appointments) {
            echo "No hay citas registradas.";
            return;
        }
        header('Content-Type: text/html');
        // Incluir las vistas
        include('header.php');
        include('list.php');
        include('footer.php');
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

        // Verificar si el uuid ya existe
        $stmt = $this->pdo->prepare("SELECT * FROM citas WHERE uuid = ?");
        $stmt->execute([$data['uuid']]);
        $existingAppointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingAppointment) {
            // Si ya existe, actualizar la cita
            $stmt = $this->pdo->prepare("UPDATE citas SET date = ?, time = ?, patient = ?, description = ? WHERE uuid = ?");
            $result = $stmt->execute([ 
                $data['date'],
                $data['time'],
                $data['patient'],
                $data['description'] ?? '',
                $data['uuid']
            ]);
        } else {
            // Si no existe, insertar la nueva cita
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
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde los parámetros de la URL

        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);
            return;
        }

        $stmt = $this->pdo->prepare("DELETE FROM citas WHERE uuid = ?");
        $result = $stmt->execute([$uuid]);

        if ($result) {
            // Redirigir a la vista de la lista de citas después de eliminar la cita
            header("Location: Dispatcher.php?action=listview");
            exit;
        } else {
            $this->responseJson(['success' => false, 'message' => 'Error al eliminar la cita'], 500);
        }
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
        die();
    }

    // Método para cargar la vista
    private function loadView($viewName) {
        header('Content-Type: text/html');
        include('header.php');
        include($viewName . '.php');
        include('footer.php');
    }
}

// Crear una instancia de Dispatcher
$disp = new Dispatcher();  // Aquí instanciamos el objeto

// Obtener la acción desde la URL y llamar al método dispatch()
$action = $_GET['action'] ?? 'index';  // Si no hay acción, por defecto 'index'
$disp->dispatch($action);  // Llamamos al método dispatch
?>
