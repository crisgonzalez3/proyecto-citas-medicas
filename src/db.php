<?php
class DB {
    // Configuración de la base de datos
    private static $host = 'localhost'; // Servidor de la base de datos
    private static $dbname = 'proyecto_citas_medicas'; // Nombre de tu base de datos
    private static $username = 'root'; // Usuario de MySQL
    private static $password = ''; // Contraseña de MySQL
    private static $port = 3306; // Puerto de MySQL
    private static $conn = null;

    // Método estático para obtener la conexión
    public static function getConnection() {
        if (self::$conn === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";port=" . self::$port;
            try {
                self::$conn = new PDO($dsn, self::$username, self::$password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>
