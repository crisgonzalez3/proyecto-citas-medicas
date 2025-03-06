<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointment</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <style>
        body {
            background: #f0f2f5;
        }
        .header {
            background: linear-gradient(45deg, #4b79a1, #283e51);
            color: white;
            padding: 90px 0;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 300px;
            flex-direction: column;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .btn-success {
            background: #28a745;
            border: none;
            transition: background 0.3s;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            transition: background 0.3s;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .form-component {
            padding: 80px;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>Manage Appointment</h1>
        <p>Organize and manage your appointments</p>
    </div>

    <!-- Formulario en tarjeta -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card form-component p-4">
                    <form id="appointment-form" method="POST">
                        <!-- Campo oculto para UUID -->
                        <input type="hidden" id="uuid" name="uuid">
                        
                        <!-- Campo de fecha -->
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>

                        <!-- Campo de hora -->
                        <div class="mb-3">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="time" name="time" required>
                        </div>

                        <!-- Campo de nombre de paciente -->
                        <div class="mb-3">
                            <label for="patient" class="form-label">Patient Name</label>
                            <input type="text" class="form-control" id="patient" name="patient" placeholder="Enter patient name" required>
                        </div>

                        <!-- Campo de descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description..."></textarea>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-grid gap-2">
                            <button type="submit" id="submit" class="btn btn-success">Save Appointment</button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para generar UUID
        function generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0,
                    v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        // Al cargar la página, generamos una UUID y la asignamos al campo oculto
        window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const uuidFromUrl = urlParams.get('uuid');  // Obtener el UUID desde la URL

    if (uuidFromUrl) {
        // Si existe el UUID, cargamos la cita y completamos el formulario con los datos existentes
        document.getElementById('uuid').value = uuidFromUrl;
        loadAppointmentData(uuidFromUrl); // Cargar los datos de la cita
    } else {
        // Si no existe UUID, generamos uno nuevo
        const generatedUUID = generateUUID();
        document.getElementById('uuid').value = generatedUUID;
    }
};

// Función para cargar los datos de la cita desde la API
document.getElementById('appointment-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar el envío tradicional del formulario

    // Obtener los datos del formulario
    const formData = new FormData(this);

    // Hacer la solicitud AJAX con los datos
    fetch('http://localhost:8000/Dispatcher.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Suponiendo que el servidor devuelve JSON
    .then(data => {
        if (data.success) {
            alert('Cita guardada con éxito');
            window.location.href = 'index.php?action=listview';  // Redirigir o hacer algo después de guardar
        } else {
            alert('Error al guardar la cita');
        }
    })
    .catch(error => {
        console.error('Error al guardar la cita:', error);
        alert('Hubo un error al guardar la cita');
    });
});




    </script>
</body>
</html>
