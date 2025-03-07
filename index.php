<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Comprobar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Incluir la conexión a la base de datos
include_once('src/db.php');

// Definir la vista por defecto
global $view;
$view = $_GET['action'] ?? 'home'; // Si no se proporciona una acción, carga 'home'

// Incluir el header
include('header.php');

// Incluir la vista correspondiente si el archivo existe
$viewFile = $view . '.php';
if (file_exists($viewFile)) {
    include($viewFile);
} else {
    // Mostrar un mensaje si la vista no existe
    echo "<h2>Página no encontrada</h2>";
}

// Incluir el footer
include('footer.php');
?>
