<?php
class DB {
    private static $host = 'localhost'; 
    private static $dbname = 'proyecto_citas_medicas';
    private static $username = 'root'; 
    private static $password = ''; 
    private static $port = 3306; 
    private static $conn = null;

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
