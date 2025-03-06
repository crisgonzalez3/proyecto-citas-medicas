<?php
// Incluir la conexión a la base de datos desde un archivo externo
include('db.php');  // Incluye la conexión a la base de datos
include('header.php');  // Incluye el encabezado (posiblemente contiene menús, scripts o estilos comunes)

// Crear una instancia de la clase DB para obtener la conexión a la base de datos
$db = new DB();  // Crear un objeto de la clase DB
$conn = $db->getConnection();  // Obtener la conexión a la base de datos utilizando el método getConnection
?>

<!DOCTYPE html>
<html lang="es">  <!-- Indicamos que el contenido es en español -->
<head>
    <meta charset="UTF-8">  <!-- Define la codificación de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Para que la página se vea bien en dispositivos móviles -->
    <title>Registro de Usuario</title>  <!-- Título de la página -->
</head>
<body>

<h2>Formulario de Registro</h2>  <!-- Título principal del formulario de registro -->

<!-- Formulario de registro de usuario -->
<form action="registro.php" method="POST">
    <!-- Campo para el nombre del usuario -->
    <label for="name">Nombre:</label>
    <input type="text" name="name" id="name" required><br><br>  <!-- Entrada de texto para el nombre, con validación de campo requerido -->

    <!-- Campo para el apellido del usuario -->
    <label for="surname">Apellido:</label>
    <input type="text" name="surname" id="surname" required><br><br>  <!-- Entrada de texto para el apellido, con validación de campo requerido -->

    <!-- Campo para el correo electrónico del usuario -->
    <label for="email">Correo Electrónico:</label>
    <input type="email" name="email" id="email" required><br><br>  <!-- Entrada de texto para el correo electrónico, con validación de campo requerido y formato de correo -->

    <!-- Campo para el nombre de usuario -->
    <label for="user">Nombre de Usuario:</label>
    <input type="text" name="user" id="user" required><br><br>  <!-- Entrada de texto para el nombre de usuario, con validación de campo requerido -->

    <!-- Campo para la contraseña del usuario -->
    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required><br><br>  <!-- Entrada de texto para la contraseña, con validación de campo requerido -->

    <!-- Botón para enviar el formulario -->
    <button type="submit">Registrar</button>  <!-- Botón de envío del formulario -->
</form>

<?php
// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Verifica si el formulario se ha enviado por el método POST
    // Recoger los datos del formulario
    $name = trim($_POST['name']);  // Recoge el nombre y elimina espacios al principio y al final
    $surname = trim($_POST['surname']);  // Recoge el apellido
    $email = trim($_POST['email']);  // Recoge el correo electrónico
    $user = trim($_POST['user']);  // Recoge el nombre de usuario
    $password = trim($_POST['password']);  // Recoge la contraseña

    // Verificar si el correo o el nombre de usuario ya están registrados en la base de datos
    $stmt = $conn->prepare('SELECT id FROM usuario WHERE email = :email OR user = :user LIMIT 1');  // Preparar la consulta SQL para verificar duplicados
    $stmt->bindValue(':email', $email);  // Asocia el parámetro ':email' con el valor del correo del formulario
    $stmt->bindValue(':user', $user);  // Asocia el parámetro ':user' con el valor del nombre de usuario del formulario
    $stmt->execute();  // Ejecuta la consulta SQL

    if ($stmt->rowCount() > 0) {  // Si la consulta devuelve alguna fila, significa que el correo o el nombre de usuario ya están registrados
        echo '<p style="color: red;">¡El correo o el nombre de usuario ya están registrados!</p>';  // Muestra un mensaje de error
    } else {
        // Si el correo y el nombre de usuario son únicos, continuamos con el registro
        // Hashear la contraseña para seguridad
        $passwordHasheada = password_hash($password, PASSWORD_DEFAULT);  // Hashea la contraseña con un algoritmo seguro
        
        // Insertar los datos del nuevo usuario en la base de datos
        $stmt = $conn->prepare('INSERT INTO usuario (name, surname, email, user, password) VALUES (:name, :surname, :email, :user, :password)');  // Preparar la consulta de inserción
        $stmt->bindValue(':name', $name);  // Asocia el parámetro ':name' con el valor del nombre
        $stmt->bindValue(':surname', $surname);  // Asocia el parámetro ':surname' con el valor del apellido
        $stmt->bindValue(':email', $email);  // Asocia el parámetro ':email' con el valor del correo electrónico
        $stmt->bindValue(':user', $user);  // Asocia el parámetro ':user' con el valor del nombre de usuario
        $stmt->bindValue(':password', $passwordHasheada);  // Asocia el parámetro ':password' con la contraseña hasheada

        if ($stmt->execute()) {  // Si la consulta se ejecuta correctamente
            echo '<p style="color: green;">¡Usuario registrado exitosamente!</p>';  // Muestra un mensaje de éxito
            
            // Redirige al usuario a la página de login después de un registro exitoso
            header('Location: login.php');  // Redirige a la página de inicio de sesión
            exit();  // Es importante usar exit() después de header() para detener la ejecución del script
        } else {
            // Si hay un error al registrar el usuario
            echo '<p style="color: red;">Error al registrar el usuario.</p>';  // Muestra un mensaje de error
        }
    }
}
?>

<?php
// Incluir el footer desde un archivo externo
include('footer.php');  // Este archivo puede contener pie de página, scripts comunes, etc.
?>

</body>
</html>
