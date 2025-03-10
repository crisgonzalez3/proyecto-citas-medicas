<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Comprobar si el usuario está logueado, excepto en las páginas públicas
$public_views = ['login', 'registro', 'logout'];
$view = $_GET['action'] ?? 'home';

if (!isset($_SESSION['usuario']) && !in_array($view, $public_views)) {
    header('Location: login.php');
    exit;
}

// Incluir la conexión a la base de datos
include_once('src/db.php');

// Incluir el header
include('header.php');

// Cargar la vista según el caso para evitar con gloval $view riesgo de 
//"Local File Inclusion(LFI)"
//al no haber ninguna inclusión dinamica de archivos con include($view. '.php')
switch ($view) {
    case 'home':
        include('home.php');
        break;
    case 'calendar':
        include('calendar.php');
        break;
    case 'formulario':
        include('formulario.php');
        break;
    case 'listview':
        include('listview.php');
        break;
    case 'registro':
        include('registro.php');
        break;
    case 'login':
        include('login.php');
        break;
    case 'logout':
        include('logout.php');
        break;
    default:
        echo "<h2>Página no encontrada</h2>";
        break;
}

// Incluir el footer
include('footer.php');
?>
