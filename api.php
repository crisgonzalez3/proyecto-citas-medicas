<?php 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php'; // Asegúrate de incluir la conexión a la base de datos

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

    public function dispatch($action) {
        $allowedActions = ['list', 'get', 'save', 'delete', 'saveCsv'];

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
                case 'saveCsv':
                    $this->saveCsv();
                    break;
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
        if ($uuid) {
            $stmt = $this->pdo->prepare("SELECT * FROM citas WHERE uuid = ?");
            $stmt->execute([$uuid]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($appointment) {
                $this->responseJson($appointment);
            } else {
                $this->responseJson(['success' => false, 'message' => 'Cita no encontrada'], 404);
            }
        } else {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);
        }
    }

    private function save() {
        $input = json_decode(file_get_contents('php://input'), true);

        if ($input) {
            $uuid = $input['uuid'] ?? $this->generateUUID();
            $stmt = $this->pdo->prepare("SELECT * FROM citas WHERE uuid = ?");
            $stmt->execute([$uuid]);
            $existingAppointment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingAppointment) {
                // Si la cita ya existe, actualizamos
                $stmt = $this->pdo->prepare("UPDATE citas SET date = ?, time = ?, patient = ?, description = ? WHERE uuid = ?");
                $stmt->execute([$input['date'], $input['time'], $input['patient'], $input['description'], $uuid]);
            } else {
                // Si no existe, la insertamos
                $stmt = $this->pdo->prepare("INSERT INTO citas (uuid, date, time, patient, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$uuid, $input['date'], $input['time'], $input['patient'], $input['description']]);
            }

            $this->responseJson(['success' => true], 201);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
    }

    private function delete() {
        $uuid = $_GET['uuid'] ?? '';
        if ($uuid) {
            $stmt = $this->pdo->prepare("DELETE FROM citas WHERE uuid = ?");
            $stmt->execute([$uuid]);

            if ($stmt->rowCount() > 0) {
                $this->responseJson(['success' => true]);
            } else {
                $this->responseJson(['success' => false, 'message' => 'Cita no encontrada'], 404);
            }
        } else {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);
        }
    }

    private function saveCsv() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['appointments']) && is_array($input['appointments'])) {
            foreach ($input['appointments'] as $appointmentData) {
                $uuid = $this->generateUUID();
                $stmt = $this->pdo->prepare("INSERT INTO citas (uuid, date, time, patient, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$uuid, $appointmentData[0], $appointmentData[1], $appointmentData[2], $appointmentData[3]]);
            }

            $this->responseJson(['success' => true], 201);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
    }

    private function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

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
