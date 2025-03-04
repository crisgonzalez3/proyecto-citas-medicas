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
include_once('db.php');

// Incluir el Dispatcher y ejecutarlo
include('Dispatcher.php');


// Cargar el header
// include('header.php');

// Obtener la vista a mostrar desde la URL
// $view = isset($_GET['action']) ? $_GET['action'] : 'index'; 


// Cargar el footer
// include('footer.php');
?>
