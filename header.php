<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Clinic</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <!-- Font Awesome for user icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
            margin-top: 70px;
        }
        .navbar {
            background-color: #2c3e50;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
    <!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?action=home">
                <img src="logo.jpg" alt="Logo" class="logo" style="height: 50px; margin-right: 30px;">
                Dental Clinic
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=formulario">New Appointment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=listview">Appointment List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=calendar">Calendar</a>
                    </li>
                    <li class="nav-item">
                    <?php if (!empty($_SESSION['usuario'])): ?>
                        <!-- Mostrar el botón de Logout solo si la sesión está abierta -->
                        <a class="nav-link" href="index.php?action=logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    <?php else: ?>
                        <!-- Mostrar el botón de Login solo si la sesión está cerrada -->
                        <a class="nav-link" href="index.php?action=login">
                            <i class="fas fa-user"></i> Login
                        </a>
                    <?php endif; ?>
                </li>
                </ul>
            </div>
        </div>
    </header>
</body>
</html>