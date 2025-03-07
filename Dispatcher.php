<?php
// Configuración de cabeceras para la respuesta HTTP
header('Content-Type: application/json');  // Especifica que la respuesta será en formato JSON
header('Access-Control-Allow-Origin: *');  // Permite solicitudes desde cualquier dominio (CORS)
header('Access-Control-Allow-Methods: GET, POST, DELETE, HEAD, OPTIONS');  // Métodos HTTP permitidos
header('Access-Control-Allow-Headers: Content-Type');  // Permite encabezados de tipo Content-Type

// Incluir la clase de conexión a la base de datos
include_once('db.php');

// Clase Dispatcher que se encarga de manejar las diferentes acciones solicitadas
class Dispatcher {
    private $pdo;  // Propiedad para la conexión a la base de datos

    // Constructor que establece la conexión a la base de datos
    public function __construct() {
        $this->pdo = DB::getConnection();  // Usamos la clase DB para obtener la conexión PDO
    }

    // Método principal para procesar las acciones recibidas
    public function dispatch($action) {
        // Definir las acciones permitidas
        $allowedActions = [
            'list', 'calendar', 'formulario', 'login', 'logout', 'get', 'save', 'delete', 'listview', 'home',
            'listUsuarios', 'getUsuario', 'saveUsuario', 'deleteUsuario', 'index'
        ];

        // Verificar que la acción solicitada esté permitida
        if (!in_array($action, $allowedActions)) {
            $this->responseJson(['success' => false, 'message' => 'Acción no válida'], 400);  // Error si la acción no es válida
            return;
        }

        try {
            // Ejecutar el código correspondiente según la acción solicitada
            switch ($action) {
                case 'list':  // Listar citas
                    $this->list();
                    break;
                case 'get':  // Obtener una cita específica
                    $this->get();
                    break;
                case 'save':  // Guardar o actualizar una cita
                    $this->save();
                    break;
                case 'delete':  // Eliminar una cita
                    $this->delete();
                    break;
                case 'listUsuarios':  // Listar usuarios
                    $this->listUsuarios();
                    break;
                case 'getUsuario':  // Obtener un usuario específico
                    $this->getUsuario();
                    break;
                case 'saveUsuario':  // Guardar un usuario
                    $this->saveUsuario();
                    break;
                case 'deleteUsuario':  // Eliminar un usuario
                    $this->deleteUsuario();
                    break;
                // Para vistas, redirigir a index.php
                case 'index':
                case 'home':
                case 'formulario':
                    // if (empty($uuid)) {
                    //     $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);
                    //     return;
                    // }
                     $this->get();
                case 'calendar':
                case 'listview':
                case 'login':
                case 'logout':
                    header("Location: index.php?action=$action");  // Redirigir a la vista correspondiente
                    exit;
            }
        } catch (Exception $e) {
            // Si ocurre un error, devolver mensaje de error en formato JSON
            $this->responseJson(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Método para listar todas las citas
    private function list() {
        $stmt = $this->pdo->query("SELECT * FROM citas");  // Obtener todas las citas
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Recuperar todas las citas como un array asociativo
        $this->responseJson($appointments);  // Enviar respuesta en formato JSON
    }
    
    // Método para obtener los detalles de una cita específica por su UUID
    private function get() {
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde el parámetro de la URL
        // Verificar que el UUID esté presente
        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);  // Error si falta el UUID
            return;
        }

        // Preparar la consulta para obtener la cita por UUID
        $stmt = $this->pdo->prepare("SELECT * FROM citas WHERE uuid = ?");
        $stmt->execute([$uuid]);  // Ejecutar la consulta
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);  // Recuperar la cita
        
        // Verificar si se encontró la cita y devolverla en formato JSON
        if ($appointment) {
            $this->responseJson($appointment);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Cita no encontrada'], 404);  // Error si no se encuentra la cita
        }
    }

    // Método para guardar o actualizar una cita
    private function save() {
        $data = $_POST;  // Obtener los datos enviados por POST
        // Verificar que los datos obligatorios estén presentes
        if (!isset($data['uuid']) || !isset($data['date']) || !isset($data['time']) || !isset($data['patient'])) {
            $this->responseJson(['success' => false, 'message' => 'Faltan datos obligatorios'], 400);  // Error si faltan datos
            return;
        }

        // Verificar si ya existe una cita con el mismo UUID
        $stmt = $this->pdo->prepare("SELECT * FROM citas WHERE uuid = ?");
        $stmt->execute([$data['uuid']]);
        $existingAppointment = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si la cita ya existe, actualizamos sus datos; si no, insertamos una nueva
        if ($existingAppointment) {
            $stmt = $this->pdo->prepare("UPDATE citas SET date = ?, time = ?, patient = ?, description = ? WHERE uuid = ?");
            $result = $stmt->execute([
                $data['date'],
                $data['time'],
                $data['patient'],
                $data['description'] ?? '',  // Si no hay descripción, dejamos un string vacío
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

        // Si la operación fue exitosa, respondemos con un mensaje de éxito
        if ($result) {
            $this->responseJson(['success' => true, 'message' => 'Cita guardada correctamente'], 200);
        } else {
            // Si hubo un error, respondemos con un mensaje de error
            $this->responseJson(['success' => false, 'message' => 'Error al guardar la cita'], 500);
        }
    }

    // Método para eliminar una cita específica por su UUID
    private function delete() {
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde el parámetro de la URL
        
        // Verificar que el UUID esté presente
        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);  // Error si falta el UUID
            return;
        }

        // Preparar la consulta para eliminar la cita por UUID
        $stmt = $this->pdo->prepare("DELETE FROM citas WHERE uuid = ?");
        $result = $stmt->execute([$uuid]);  // Ejecutar la consulta

        // Si la cita fue eliminada, redirigimos a la vista de lista de citas
        if ($result) {
            header("Location: index.php?action=listview");
            exit;
        } else {
            $this->responseJson(['success' => false, 'message' => 'Error al eliminar la cita'], 500);  // Error si no se pudo eliminar
        }
    }

    // Métodos para gestionar usuarios (listado, obtener, guardar, eliminar usuarios)
    private function listUsuarios() {
        $stmt = $this->pdo->query("SELECT * FROM usuarios");  // Obtener todos los usuarios
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Recuperar todos los usuarios
        $this->responseJson($usuarios);  // Enviar respuesta en formato JSON
    }

    private function getUsuario() {
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde el parámetro de la URL

        // Verificar que el UUID esté presente
        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);  // Error si falta el UUID
            return;
        }

        // Preparar la consulta para obtener un usuario por UUID
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE uuid = ?");
        $stmt->execute([$uuid]);  // Ejecutar la consulta
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);  // Recuperar el usuario

        // Verificar si se encontró el usuario y devolverlo en formato JSON
        if ($usuario) {
            $this->responseJson($usuario);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Usuario no encontrado'], 404);  // Error si no se encuentra el usuario
        }
    }

    private function saveUsuario() {
        $data = json_decode(file_get_contents('php://input'), true);  // Obtener los datos JSON enviados

        // Verificar que los datos obligatorios estén presentes
        if (!$data || !isset($data['uuid']) || !isset($data['name']) || !isset($data['email'])) {
            $this->responseJson(['success' => false, 'message' => 'Faltan datos obligatorios'], 400);  // Error si faltan datos
            return;
        }

        // Preparar la consulta para insertar un nuevo usuario
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (uuid, name, email) VALUES (?, ?, ?)");
        $result = $stmt->execute([$data['uuid'], $data['name'], $data['email']]);

        // Si la operación fue exitosa, respondemos con un mensaje de éxito
        if ($result) {
            $this->responseJson(['success' => true, 'message' => 'Usuario guardado correctamente'], 200);
        } else {
            // Si hubo un error, respondemos con un mensaje de error
            $this->responseJson(['success' => false, 'message' => 'Error al guardar el usuario'], 500);
        }
    }

    private function deleteUsuario() {
        $uuid = $_GET['uuid'] ?? '';  // Obtener el UUID desde el parámetro de la URL

        // Verificar que el UUID esté presente
        if (empty($uuid)) {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);  // Error si falta el UUID
            return;
        }

        // Preparar la consulta para eliminar un usuario por UUID
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE uuid = ?");
        $result = $stmt->execute([$uuid]);  // Ejecutar la consulta

        // Si el usuario fue eliminado, respondemos con éxito
        if ($result) {
            $this->responseJson(['success' => true, 'message' => 'Usuario eliminado correctamente'], 200);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Error al eliminar el usuario'], 500);  // Error si no se pudo eliminar
        }
    }

    // Método para enviar una respuesta JSON
    private function responseJson($data, $statusCode = 200) {
        header('Content-Type: application/json');  // Especificamos que la respuesta es JSON
        http_response_code($statusCode);  // Establecer el código de estado HTTP
        echo json_encode($data);  // Enviar la respuesta en formato JSON
        die();  // Finaliza el script
    }
}

// Crear una instancia de la clase Dispatcher y procesar la acción solicitada
$disp = new Dispatcher();
$action = $_GET['action'] ?? 'index';  // Obtener la acción solicitada, por defecto 'index'
$disp->dispatch($action);  // Procesar la acción correspondiente
?>