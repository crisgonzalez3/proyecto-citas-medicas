<?php 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Responder a las solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

class Dispatcher {
    private $filename = 'citas.csv'; // Cambiado a citas.csv

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
        $appointments = [];
        if (($handle = fopen($this->filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $appointments[] = [
                    'uuid' => $data[0],
                    'date' => $data[1],
                    'time' => $data[2],
                    'patient' => $data[3],
                    'description' => $data[4]
                ];
            }
            fclose($handle);
        }
        $this->responseJson($appointments);
    }

    private function get() {
        $uuid = $_GET['uuid'] ?? '';
        if ($uuid) {
            if (($handle = fopen($this->filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if ($data[0] == $uuid) {
                        $appointment = [
                            'uuid' => $data[0],
                            'date' => $data[1],
                            'time' => $data[2],
                            'patient' => $data[3],
                            'description' => $data[4]
                        ];
                        fclose($handle);
                        $this->responseJson($appointment);
                        return;
                    }
                }
                fclose($handle);
            }
        }
        $this->responseJson(['success' => false, 'message' => 'Cita no encontrada'], 404);
    }

    private function save() {
        $input = json_decode(file_get_contents('php://input'), true);

        if ($input) {
            $uuid = $input['uuid'] ?? $this->generateUUID();
            $appointments = [];
            $updated = false;

            if (($handle = fopen($this->filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if ($data[0] != $uuid) {
                        $appointments[] = $data;
                    } else {
                        $appointments[] = [$uuid, $input['date'], $input['time'], $input['patient'], $input['description']];
                        $updated = true;
                    }
                }
                fclose($handle);
            }

            if (!$updated) {
                $appointments[] = [$uuid, $input['date'], $input['time'], $input['patient'], $input['description']];
            }

            if (($handle = fopen($this->filename, "w")) !== FALSE) {
                foreach ($appointments as $appointment) {
                    fputcsv($handle, $appointment);
                }
                fclose($handle);
                $this->responseJson(['success' => true], 201);
            } else {
                $this->responseJson(['success' => false, 'message' => 'No se pudo abrir el archivo CSV'], 500);
            }
        } else {
            $this->responseJson(['success' => false, 'message' => 'Datos inválidos'], 400);
        }
    }

    private function delete() {
        $uuid = $_GET['uuid'] ?? '';
        if ($uuid) {
            $appointments = [];
            if (($handle = fopen($this->filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if ($data[0] != $uuid) {
                        $appointments[] = $data;
                    }
                }
                fclose($handle);
            }

            if (($handle = fopen($this->filename, "w")) !== FALSE) {
                foreach ($appointments as $appointment) {
                    fputcsv($handle, $appointment);
                }
                fclose($handle);
                $this->responseJson(['success' => true]);
            } else {
                $this->responseJson(['success' => false, 'message' => 'No se pudo abrir el archivo CSV'], 500);
            }
        } else {
            $this->responseJson(['success' => false, 'message' => 'UUID no proporcionado'], 400);
        }
    }

    private function saveCsv() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['appointments']) && is_array($input['appointments'])) {
            $appointments = [];

            if (($handle = fopen($this->filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    $appointments[] = $data;
                }
                fclose($handle);
            }

            foreach ($input['appointments'] as $appointmentData) {
                $uuid = $this->generateUUID();
                $appointments[] = [$uuid, $appointmentData[0], $appointmentData[1], $appointmentData[2], $appointmentData[3]];
            }

            if (($handle = fopen($this->filename, "w")) !== FALSE) {
                foreach ($appointments as $appointment) {
                    fputcsv($handle, $appointment);
                }
                fclose($handle);
                $this->responseJson(['success' => true], 201);
            } else {
                $this->responseJson(['success' => false, 'message' => 'No se pudo abrir el archivo CSV'], 500);
            }
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
$dispatcher = new Dispatcher();
$dispatcher->dispatch($action);
?>
