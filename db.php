<?php
class DB {
    // Configuración de la base de datos
    private $host = 'localhost'; // Servidor de la base de datos
    private $dbname = 'proyecto_citas_medicas'; // Nombre de tu base de datos
    private $username = 'root'; // Usuario de MySQL
    private $password = ''; // Contraseña de MySQL
    private $port = 3306; // Puerto de MySQL
    private $conn;

    public function __construct() {
        // Usamos PDO en lugar de mysqli
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};port={$this->port}";
        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            // Configuramos el modo de error a excepciones
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
