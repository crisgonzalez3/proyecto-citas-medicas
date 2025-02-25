<?php
// Devolvemos la respuesta en JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Leemos el cuerpo de la petición
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Obtenemos la acción
$action = isset($data['action']) ? $data['action'] : null;

switch ($action) {
    case 'create':
        // Tomamos los datos de 'data'
        $nombre = $data['data']['nombre'] ?? '';
        $descripcion = $data['data']['descripcion'] ?? '';
        // Aquí iría la lógica para insertar en tu base de datos
        echo json_encode(["mensaje" => "Modelo creado: $nombre, $descripcion"]);
        break;

    case 'update':
        // Para actualizar, necesitamos el id y la data
        $id = $data['id'] ?? null;
        $nombre = $data['data']['nombre'] ?? '';
        $descripcion = $data['data']['descripcion'] ?? '';
        // Aquí iría la lógica para actualizar en la BD (por ejemplo, usando $id)
        echo json_encode(["mensaje" => "Modelo con ID $id actualizado a: $nombre, $descripcion"]);
        break;

    case 'delete':
        // Para eliminar, solo necesitamos el id
        $id = $data['id'] ?? null;
        // Aquí iría la lógica para eliminar de la BD
        echo json_encode(["mensaje" => "Modelo con ID $id eliminado"]);
        break;

    default:
        // Acción desconocida
        echo json_encode(["error" => "Acción no reconocida"]);
        break;
}
?>
