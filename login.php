<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario ya está logueado, redirige al inicio
if (!empty($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

// Incluir la conexión a la base de datos
include('db.php'); 
include('header.php');

$error = ''; // Variable para almacenar mensajes de error

// Procesar el formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $contraseña = trim($_POST['contraseña']);

    // Consulta para obtener el usuario y la contraseña desde la base de datos
    $stmt = $conn->prepare('SELECT user, password FROM usuario WHERE user = ? LIMIT 1');
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        
        // Comparar la contraseña con el hash de la base de datos
        if (password_verify($contraseña, $fila['password'])) {
            $_SESSION['usuario'] = $fila['user']; // Guardar el nombre de usuario en la sesión
            header('Location: index.php'); // Redirigir al índice
            exit();
        } else {
            $error = 'Contraseña incorrecta.';
        }
    } else {
        $error = 'El usuario no existe.';
    }
    
    $stmt->close();
}

$conn->close(); // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <!-- Enlace a Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4 shadow-lg" style="max-width: 400px; width: 100%;">
            <h1 class="text-center mb-4">Iniciar sesión</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="contraseña" class="form-label">Contraseña</label>
                    <input type="password" name="contraseña" id="contraseña" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
            </form>

            <!-- Botón de registro -->
            <div class="mt-3 text-center">
                <a href="registro.php" class="btn btn-secondary w-100">¿No tienes cuenta? Regístrate</a>
            </div>
        </div>
    </div>

    <!-- Enlace a Bootstrap 5 JS (opcional, pero útil si deseas usar componentes interactivos de Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Incluir el footer
include('footer.php');
?>
