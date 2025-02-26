<?php
// Configuración de la base de datos
$host = 'localhost'; // Servidor de la base de datos
$dbname = 'proyecto_citas_medicas'; // Nombre de tu base de datos
$username = 'root'; // Usuario de MySQL
$password = ''; // Contraseña de MySQL
$port = 3306; // Puerto de MySQL 

// Crear conexión (asegúrate de usar las variables correctas)
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos.";
}
?>
