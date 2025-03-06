<?php
// Devolvemos la respuesta en formato JSON
header('Content-Type: application/json'); // Define el tipo de contenido como JSON.
header('Access-Control-Allow-Origin: *'); // Permite solicitudes desde cualquier origen.
header('Access-Control-Allow-Methods: GET, POST, DELETE'); // Especifica qué métodos HTTP son permitidos.
header('Access-Control-Allow-Headers: Content-Type'); // Permite el encabezado Content-Type.

require_once 'db.php'; // Incluye la conexión a la base de datos (asegúrate de que el archivo db.php existe).


// Leemos el cuerpo de la petición (en formato JSON) enviado por el cliente.
$input = file_get_contents('php://input'); 
$data = json_decode($input, true); // Decodifica el JSON en un array asociativo.


// Obtenemos la acción que se quiere realizar, que viene en el JSON.
$action = isset($data['action']) ? $data['action'] : null;

switch ($action) { 
    
    // ACCIONES PARA LA TABLA `USUARIO`
    
    // Crear un nuevo usuario
    case 'create_user':
        $name = $data['data']['name'] ?? '';
        $surname = $data['data']['surname'] ?? '';
        $email = $data['data']['email'] ?? '';
        $user = $data['data']['user'] ?? '';
        $password = $data['data']['password'] ?? '';
        
        // Si los datos son completos, ejecuta la inserción en la base de datos.
        if ($name && $surname && $email && $user && $password) {
            $stmt = $pdo->prepare("INSERT INTO usuario (name, surname, email, user, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $surname, $email, $user, password_hash($password, PASSWORD_DEFAULT)]);
            echo json_encode(["mensaje" => "Usuario creado con éxito: $user"]);
        } else {
            echo json_encode(["error" => "Datos incompletos para crear el usuario"]);
        }
        break;
        
    // Actualizar usuario
    case 'update_user':
        // Recibe la ID del usuario y los nuevos datos para actualizar.
        $id = $data['id'] ?? null;
        $name = $data['data']['name'] ?? '';
        $surname = $data['data']['surname'] ?? '';
        $email = $data['data']['email'] ?? '';
        $user = $data['data']['user'] ?? '';
        $password = $data['data']['password'] ?? '';
        
        // Verifica que los datos sean completos antes de actualizar.
        if ($id && $name && $surname && $email && $user && $password) {
            $stmt = $pdo->prepare("UPDATE usuario SET name = ?, surname = ?, email = ?, user = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $surname, $email, $user, password_hash($password, PASSWORD_DEFAULT), $id]);
            echo json_encode(["mensaje" => "Usuario con ID $id actualizado"]);
        } else {
            echo json_encode(["error" => "Datos incompletos para actualizar el usuario"]);
        }
        break;

    // Eliminar usuario
    case 'delete_user':
        $id = $data['id'] ?? null;
        
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM usuario WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Usuario con ID $id eliminado"]);
            } else {
                echo json_encode(["error" => "No se encontró el usuario con ID $id"]);
            }
        } else {
            echo json_encode(["error" => "ID no proporcionado para eliminar el usuario"]);
        }
        break;
    
    // ACCIONES PARA LA TABLA `CITAS`

    // Crear una nueva cita
    case 'create_cita':
        $uuid = $data['data']['uuid'] ?? null;
        $date = $data['data']['date'] ?? '';
        $time = $data['data']['time'] ?? '';
        $patient = $data['data']['patient'] ?? '';
        $description = $data['data']['description'] ?? '';

        if ($uuid && $date && $time && $patient && $description) {
            $stmt = $pdo->prepare("INSERT INTO citas (uuid, date, time, patient, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$uuid, $date, $time, $patient, $description]);
            echo json_encode(["mensaje" => "Cita creada con éxito para el paciente $patient"]);
        } else {
            echo json_encode(["error" => "Datos incompletos para crear la cita"]);
        }
        break;

    // Actualizar una cita
    case 'update_cita':
        $uuid = $data['uuid'] ?? null;
        $date = $data['data']['date'] ?? '';
        $time = $data['data']['time'] ?? '';
        $patient = $data['data']['patient'] ?? '';
        $description = $data['data']['description'] ?? '';

        if ($uuid && $date && $time && $patient && $description) {
            $stmt = $pdo->prepare("UPDATE citas SET date = ?, time = ?, patient = ?, description = ? WHERE uuid = ?");
            $stmt->execute([$date, $time, $patient, $description, $uuid]);
            echo json_encode(["mensaje" => "Cita con UUID $uuid actualizada"]);
        } else {
            echo json_encode(["error" => "Datos incompletos para actualizar la cita"]);
        }
        break;

    // Eliminar una cita
    case 'delete_cita':
        $uuid = $data['uuid'] ?? null;

        if ($uuid) {
            $stmt = $pdo->prepare("DELETE FROM citas WHERE uuid = ?");
            $stmt->execute([$uuid]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["mensaje" => "Cita con UUID $uuid eliminada"]);
            } else {
                echo json_encode(["error" => "No se encontró la cita con UUID $uuid"]);
            }
        } else {
            echo json_encode(["error" => "UUID no proporcionado para eliminar la cita"]);
        }
        break;

    default:
        // Si la acción no es reconocida, se devuelve un error.
        echo json_encode(["error" => "Acción no reconocida"]);
        break;
}
?>
