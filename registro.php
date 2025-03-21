<?php
// Incluir la conexión a la base de datos desde un archivo externo
include('src/db.php');  // Incluye la conexión a la base de datos
include('header.php');  // Incluye el encabezado (posiblemente contiene menús, scripts o estilos comunes)

// Crear una instancia de la clase DB para obtener la conexión a la base de datos
$db = new DB();  // Crear un objeto de la clase DB
$conn = $db->getConnection();  // Obtener la conexión a la base de datos utilizando el método getConnection
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
            padding-top: 30px;
        }
        .container {
            padding-top: 30px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h2>Formulario de Registro</h2>
                <!-- Formulario de registro de usuario -->
                <form action="registro.php" method="POST">
                    <!-- Campo para el nombre del usuario -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <!-- Campo para el apellido del usuario -->
                    <div class="mb-3">
                        <label for="surname" class="form-label">Apellido:</label>
                        <input type="text" name="surname" id="surname" class="form-control" required>
                    </div>

                    <!-- Campo para el correo electrónico del usuario -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <!-- Campo para el nombre de usuario -->
                    <div class="mb-3">
                        <label for="user" class="form-label">Nombre de Usuario:</label>
                        <input type="text" name="user" id="user" class="form-control" required>
                    </div>

                    <!-- Campo para la contraseña del usuario -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña:</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <!-- Botón para enviar el formulario -->
                    <button type="submit" class="btn btn-success">Registrar</button>
                </form>

                <?php
                // Procesar el formulario si se ha enviado
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
                    // Recoger los datos del formulario
                    $name = trim($_POST['name']);
                    $surname = trim($_POST['surname']);
                    $email = trim($_POST['email']);
                    $user = trim($_POST['user']);
                    $password = trim($_POST['password']);

                    // Verificar si el correo o el nombre de usuario ya están registrados en la base de datos
                    $stmt = $conn->prepare('SELECT id FROM usuario WHERE email = :email OR user = :user LIMIT 1');
                    $stmt->bindValue(':email', $email);
                    $stmt->bindValue(':user', $user);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        echo '<p style="color: red;">¡El correo o el nombre de usuario ya están registrados!</p>';
                    } else {
                        // Hashear la contraseña para seguridad
                        $passwordHasheada = password_hash($password, PASSWORD_DEFAULT);

                        // Insertar los datos del nuevo usuario en la base de datos
                        $stmt = $conn->prepare('INSERT INTO usuario (name, surname, email, user, password) VALUES (:name, :surname, :email, :user, :password)');
                        $stmt->bindValue(':name', $name);
                        $stmt->bindValue(':surname', $surname);
                        $stmt->bindValue(':email', $email);
                        $stmt->bindValue(':user', $user);
                        $stmt->bindValue(':password', $passwordHasheada);

                        if ($stmt->execute()) {
                            echo '<p style="color: green;">¡Usuario registrado exitosamente!</p>';
                            // Redirige al usuario a la página de login después de un registro exitoso
                            header('Location: login.php');
                            exit();
                        } else {
                            echo '<p style="color: red;">Error al registrar el usuario.</p>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer desde un archivo externo
include('footer.php');
?>

</body>
</html>
