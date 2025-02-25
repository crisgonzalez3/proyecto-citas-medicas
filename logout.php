<?php
// // Asegúrate de que la sesión se inicie solo si no está activa
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
// // Destruir la sesión
// session_unset();
// session_destroy();
// // Redirigir al login
// header("Location: index.php?action=login");
// exit();
// Asegúrate de que la sesión se inicie solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir la sesión
session_unset();
session_destroy();
?>

<script type="text/javascript">
    // Redirigir al login con JavaScript
    window.location.href = 'index.php?action=login';
</script>

?>
