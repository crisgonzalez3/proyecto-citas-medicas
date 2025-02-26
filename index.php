<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Comprobar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, redirigir al login
    header('Location: login.php');
    exit;
}
// Incluir la conexión a la base de datos
include('db.php');
// Cargar el header
// Usando include
include('header.php');
// Usando require (detiene la ejecución si falla)
require('header.php');

// Obtener la vista a mostrar desde la URL
$view = isset($_GET['action']) ? $_GET['action'] : 'index'; // Si no se pasa 'action', mostramos 'index' por defecto

// Comprobamos qué vista queremos cargar
switch ($view) {
    case 'calendar':
        include_once 'calendar.html';  // Vista del calendario
        break;
    case 'formulario':
        include_once 'formulario.html';  // Vista del formulario
        break;
    case 'list':
        include_once 'list.html';  // Vista de la lista de citas
        break;
    case 'login':
        include_once 'login.php'; //Vista del login
        break;
    case 'logout':
        include_once 'logout.php';  // Cerrar sesión
         break;
    default:
        include_once 'home.html';  // Vista por defecto (home)
        break;
}
// Incluir el footer
include('footer.php');
?>