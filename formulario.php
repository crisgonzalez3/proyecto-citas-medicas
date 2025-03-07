<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointment</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
            padding-top: 30px; /* Añadimos más espacio superior */
        }
        /* Añadimos un padding-top adicional al contenedor para el formulario */
        .container {
            padding-top: 30px; /* Ajustar este valor si lo necesitas */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <form id="appointment-form" method="POST">
                        <input type="hidden" id="uuid" name="uuid">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="time" name="time" required>
                        </div>
                        <div class="mb-3">
                            <label for="patient" class="form-label">Patient Name</label>
                            <input type="text" class="form-control" id="patient" name="patient" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Save Appointment</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0,
                    v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function loadAppointmentData(uuid) {
            fetch(`http://localhost/proyecto-citas-medicas/Dispatcher.php?action=get&uuid=${uuid}`)
                .then(response => response.json())
                .then(data => {
                    if (data.uuid) {
                        document.getElementById('uuid').value = data.uuid;
                        document.getElementById('date').value = data.date;
                        document.getElementById('time').value = data.time.split(':').slice(0,2).join(':');
                        document.getElementById('patient').value = data.patient;
                        document.getElementById('description').value = data.description || '';
                    } else {
                        alert('Error loading appointment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching appointment data');
                });
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const uuidFromUrl = urlParams.get('uuid');
            if (uuidFromUrl) {
                document.getElementById('uuid').value = uuidFromUrl;
                loadAppointmentData(uuidFromUrl);
            } else {
                document.getElementById('uuid').value = generateUUID();
            }
        };

        document.getElementById('appointment-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Obtener valores del formulario
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const patient = document.getElementById('patient').value;
    const description = document.getElementById('description').value;

    // Validar los campos requeridos
    if (!date || !time || !patient) {
        alert("Por favor, complete todos los campos obligatorios.");
        return;
    }

    // Validar formato de la fecha (YYYY-MM-DD)
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateRegex.test(date)) {
        alert("El formato de la fecha es incorrecto. Debe ser YYYY-MM-DD.");
        return;
    }

    // Validar formato de la hora (HH:mm)
    const timeRegex = /^([01]?[0-9]|2[0-3]):([0-5][0-9])$/;
    if (!timeRegex.test(time)) {
        alert("El formato de la hora es incorrecto. Debe ser HH:mm.");
        return;
    }

    // Si todo está bien, enviar el formulario
    const formData = new FormData(this);
    fetch('http://localhost/proyecto-citas-medicas/Dispatcher.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cita guardada correctamente!');
            window.location.href = 'index.php?action=listview';
        } else {
            alert('Error al guardar la cita: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al guardar la cita.');
    });
});

    </script>
</body>
</html>
