<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$public_views = ['login', 'registro', 'logout'];
$view = $_GET['action'] ?? 'home';

if (!isset($_SESSION['usuario']) && !in_array($view, $public_views)) {
    header('Location: login.php');
    exit;
}

//include_once('src/db.php');

include('header.php');

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

include('footer.php');
?>
