<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
            margin-top: 70px;
        }

        .navbar {
            background-color: #2c3e50;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 10; 
        }

        .hero-section {
            position: relative;
            height: 45vh; 
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 20px;
            z-index: 1; 
        }

        .hero-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; 
            z-index: -1;
        }

        .hero-content {
            position: relative; 
            z-index: 1;
        }

        .hero-title {
            font-size: 3.3rem;
            font-weight: 700;
            text-shadow: 3px 3px 5px rgba(0, 0, 0, 0.4);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            font-weight: 400;
            margin-top: 15px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4);
        }

        .btn-custom {
            background-color: #00b4b3;
            border: 2px solid #00b4b3;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease-in-out;
        }

        .btn-custom:hover {
            background-color: #1d8280;
            border-color: #1d8280;
            transform: scale(1.1);
        }

        .main-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 30vh; 
            text-align: center;
        }

        .dashboard-text {
            font-size: 2.5rem; 
            font-weight: 700;
            margin-bottom: 30px;
        }

        .button-container {
            display: flex;
            gap: 15px; 
            justify-content: center;
        }

        .container.mt-5.pt-4 {
            margin-top: 0;
        }

        footer {
            background-color: #34495e; 
            color: white; 
            padding: 30px 0;
            text-align: center;
            font-size: 1rem;
            margin-top: 30px; 
        }
    
        footer p {
            margin: 0;
        }
    </style>
</head>
<body>

    <section class="hero-section">
        <img src="public/clinica.jpg" alt="Imagen de la clínica" class="hero-image">
        <div class="hero-content">
            <h1 class="hero-title">Manage Your Dental Appointments Effortlessly</h1>
            <p class="hero-subtitle">Schedule, track, and manage your appointments with ease!</p>
        </div>
    </section>

    <div class="main-content">
        <h1 class="dashboard-text">Welcome to Your Appointment Dashboard</h1>
        
        <div class="button-container">
            <button class="btn btn-custom" onclick="window.location.href='index.php?action=formulario'">New Appointment</button>
            <button class="btn btn-custom" onclick="window.location.href='index.php?action=listview'">Appointment List</button>
            <button class="btn btn-custom" onclick="window.location.href='index.php?action=calendar'">View Calendar</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>