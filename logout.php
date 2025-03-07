<?php
// Iniciar sesión si no está iniciada
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Eliminar la cookie de usuario
setcookie('user', '', time() - 3600, '/', '', true, true); // Establecer la cookie con tiempo en el pasado para eliminarla

// Redirigir a la página de login
header('Location: login.php');
exit();
?>
