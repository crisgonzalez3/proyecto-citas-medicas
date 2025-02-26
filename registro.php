<?php
// Incluir la conexión a la base de datos
include('db.php');
include('header.php');

// Crear una instancia de la clase DB para obtener la conexión
$db = new DB();
$conn = $db->getConnection();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
</head>
<body>

<h2>Formulario de Registro</h2>

<!-- Formulario de registro -->
<form action="registro.php" method="POST">
    <label for="name">Nombre:</label>
    <input type="text" name="name" id="name" required><br><br>

    <label for="surname">Apellido:</label>
    <input type="text" name="surname" id="surname" required><br><br>

    <label for="email">Correo Electrónico:</label>
    <input type="email" name="email" id="email" required><br><br>

    <label for="user">Nombre de Usuario:</label>
    <input type="text" name="user" id="user" required><br><br>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required><br><br>

    <button type="submit">Registrar</button>
</form>

<?php
// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $user = trim($_POST['user']);
    $password = trim($_POST['password']);
    
    // Verificar si el correo ya está registrado (con PDO)
    $stmt = $conn->prepare('SELECT id FROM usuario WHERE email = :email OR user = :user LIMIT 1');
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':user', $user);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo '<p style="color: red;">¡El correo o el nombre de usuario ya están registrados!</p>';
    } else {
        // Hashear la contraseña
        $passwordHasheada = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar el nuevo usuario en la base de datos
        $stmt = $conn->prepare('INSERT INTO usuario (name, surname, email, user, password) VALUES (:name, :surname, :email, :user, :password)');
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':surname', $surname);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':user', $user);
        $stmt->bindValue(':password', $passwordHasheada);
        
        if ($stmt->execute()) {
            echo '<p style="color: green;">¡Usuario registrado exitosamente!</p>';
            
            // Redirigir a login.php después de un registro exitoso
            header('Location: login.php');
            exit();  // Es importante usar exit() después de header() para asegurarse de que el script se detenga aquí
        } else {
            echo '<p style="color: red;">Error al registrar el usuario.</p>';
        }
    }
}
?>

<?php
// Incluir el footer
include('footer.php');
?>

</body>
</html>
