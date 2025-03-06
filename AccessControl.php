<?php
// Verifica si no hay ninguna sesión activa, en caso de que no exista, la inicia.
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Inicia una sesión si no existe ninguna.
}

class AccessControl {
    // Método para asegurar que el usuario esté logueado.
    public static function loginRequired() {
        // Si la variable de sesión 'usuario' no está definida, redirige a la página de login.
        if (!isset($_SESSION['usuario'])) {
            header('Location: login.php');  // Redirige a login.php.
            exit;  // Termina la ejecución del script para evitar más procesamiento.
        }
    }

    // Método para loguear a un usuario.
    public static function login($userData) {
        // Guarda la información del usuario en la variable de sesión 'usuario'.
        $_SESSION['usuario'] = $userData;
        header('Location: home.php');  // Redirige a home.php después de loguearse.
        exit;  // Termina la ejecución del script.
    }

    // Método para cerrar sesión.
    public static function logout() {
        session_destroy();  // Destruye toda la información de la sesión.
        header('Location: login.php');  // Redirige a la página de login después de cerrar sesión.
        exit;  // Termina la ejecución del script.
    }

    // Método para verificar si el usuario está logueado.
    public static function isLoggedIn() {
        // Devuelve true si la sesión 'usuario' está definida, es decir, si el usuario está logueado.
        return isset($_SESSION['usuario']);
    }
}
?>
