<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['usuario']) || isset($_COOKIE['user'])) {
    header('Location: index.php'); 
    exit();
}

include('src/db.php'); 
include('header.php');

$error = ''; 
$db = new DB();
$conn = $db->getConnection(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $contraseña = trim($_POST['contraseña']);
    $stmt = $conn->prepare('SELECT user, password FROM usuario WHERE user = :usuario LIMIT 1');
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        if (password_verify($contraseña, $resultado['password'])) {
            session_regenerate_id(true);

            $_SESSION['usuario'] = $resultado['user'];

            setcookie('user', $usuario, time() + 3600, '/', '', true, true);
            header('Location: index.php?action=home'); 
            exit();
        } else {
            $error = 'Contraseña incorrecta.';
        }
    } else {
        $error = 'El usuario no existe.';
    }
}

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

            <?php if (isset($error) && $error): ?>
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
include('footer.php');
?>
