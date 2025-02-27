<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class AccessControl {
    public static function loginRequired() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: login.php');
            exit;
        }
    }

    public static function login($userData) {
        $_SESSION['usuario'] = $userData;
        header('Location: home.php');
        exit;
    }

    public static function logout() {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    public static function isLoggedIn() {
        return isset($_SESSION['usuario']);
    }
}
?>
