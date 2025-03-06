<?php
// Este archivo PHP está vacío por ahora, solo se define la estructura de la página HTML en adelante.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Establece la codificación de caracteres y la vista del navegador -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointment</title>

    <!-- jQuery, una librería de JavaScript para simplificar la manipulación del DOM -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Estilo de Bootstrap para darle formato a los elementos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Scripts de Bootstrap, se cargan de manera diferida -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <style>
        /* Estilos personalizados para la página */

        body {
            background: #f0f2f5;  /* Fondo gris claro */
        }

        .header {
            background: linear-gradient(45deg, #4b79a1, #283e51);  /* Gradiente de colores en el encabezado */
            color: white;  /* Texto blanco */
            padding: 90px 0;  /* Espaciado en el encabezado */
            text-align: center;  /* Texto centrado */
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);  /* Sombra suave */
            display: flex;  /* Usamos flexbox para alinear el contenido */
            justify-content: center;  /* Centra el contenido horizontalmente */
            align-items: center;  /* Centra el contenido verticalmente */
            min-height: 300px;  /* Altura mínima */
            flex-direction: column;  /* Dirección del flexbox en columna */
        }

        .card {
            border: none;  /* Eliminar borde de la tarjeta */
            border-radius: 20px;  /* Bordes redondeados */
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);  /* Sombra ligera */
            transition: transform 0.3s;  /* Efecto de transformación suave */
        }

        .card:hover {
            transform: translateY(-10px);  /* Al pasar el cursor, la tarjeta sube un poco */
        }

        .btn-success {
            background: #28a745;  /* Color verde para el botón */
            border: none;  /* Sin borde */
            transition: background 0.3s;  /* Transición de color de fondo */
        }

        .btn-success:hover {
            background: #218838;  /* Cambio de color cuando el ratón pasa sobre el botón */
        }

        .btn-secondary {
            background: #6c757d;  /* Color gris para el botón de cancelar */
            border: none;  /* Sin borde */
            transition: background 0.3s;  /* Transición de color de fondo */
        }

        .btn-secondary:hover {
            background: #5a6268;  /* Cambio de color al pasar el ratón */
        }

        .form-component {
            padding: 80px;  /* Padding adicional alrededor del formulario */
        }
    </style>
</head>
<body>

    <!-- Encabezado de la página -->
    <div class="header">
        <h1>Manage Appointment</h1>  <!-- Título de la página -->
        <p>Organize and manage your appointments</p>  <!-- Subtítulo descriptivo -->
    </div>

    <!-- Formulario para ingresar detalles de la cita -->
    <div class="container mt-5">
        <div class="row justify-content-center">  <!-- Centra el formulario horizontalmente -->
            <div class="col-md-6">  <!-- El formulario ocupará la mitad del ancho de la página -->
                <div class="card form-component p-4">  <!-- Tarjeta de formulario con padding -->
                    <form id="appointment-form" method="POST">  <!-- Formulario que enviará los datos por POST -->
                   
                        <!-- Campo oculto para almacenar el UUID de la cita -->
                        <input type="hidden" id="uuid" name="uuid">
                        
                        <!-- Campo para seleccionar la fecha de la cita -->
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>  <!-- Campo de fecha -->
                        </div>

                        <!-- Campo para seleccionar la hora de la cita -->
                        <div class="mb-3">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="time" name="time" required>  <!-- Campo de hora -->
                        </div>

                        <!-- Campo para ingresar el nombre del paciente -->
                        <div class="mb-3">
                            <label for="patient" class="form-label">Patient Name</label>
                            <input type="text" class="form-control" id="patient" name="patient" placeholder="Enter patient name" required>  <!-- Campo de texto -->
                        </div>

                        <!-- Campo para ingresar una descripción de la cita -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description..."></textarea>  <!-- Campo de texto largo -->
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-grid gap-2">
                            <button type="submit" id="submit" class="btn btn-success">Save Appointment</button>  <!-- Botón para guardar la cita -->
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Cancel</button>  <!-- Botón para cancelar -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Función para generar un UUID (Identificador único universal)
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,  // Genera un número aleatorio entre 0 y 15
                v = c === 'x' ? r : (r & 0x3 | 0x8);  // Asegura que el carácter sea válido en un UUID
            return v.toString(16);  // Devuelve el valor en formato hexadecimal
        });
    }

    // Función para cargar los datos de la cita usando el UUID
    function loadAppointmentData(uuid) {
    fetch(`http://localhost/proyecto-citas-medicas/Dispatcher.php?action=get&uuid=${uuid}`, {
        method: 'GET',
    })
    .then(response => response.json()) 
    .then(data => {
        console.log(data);  // Verifica que la respuesta sea correcta

        if (data.uuid) {
            const appointment = data;  // Los datos de la cita

            // Asignar los valores a los campos del formulario
            document.getElementById('uuid').value = appointment.uuid;
            
            // Asignar la fecha (asegurarse de que el formato sea YYYY-MM-DD)
            document.getElementById('date').value = appointment.date;
            
            // Asignar la hora (extraemos solo la parte de HH:mm)
            const timeParts = appointment.time.split(':'); // ["12", "05", "00.525000"]
            const formattedTime = `${timeParts[0]}:${timeParts[1]}`; // "HH:mm"
            document.getElementById('time').value = formattedTime;

            document.getElementById('patient').value = appointment.patient;
            document.getElementById('description').value = appointment.description || '';
        } else {
            alert('Error al cargar la cita');
        }
    })
    .catch(error => {
        console.error('Error al cargar los datos de la cita:', error); 
        alert('Hubo un error al cargar los datos de la cita');
    });
}


    // Al cargar la página, generamos un UUID o cargamos los datos si ya existe uno en la URL
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);  // Obtiene los parámetros de la URL
        const uuidFromUrl = urlParams.get('uuid');  // Obtiene el valor del parámetro 'uuid'

        if (uuidFromUrl) {
            // Si existe un UUID en la URL, asignamos ese valor al campo oculto y cargamos los datos de la cita
            document.getElementById('uuid').value = uuidFromUrl;
            loadAppointmentData(uuidFromUrl);  // Cargar los datos de la cita
        } else {
            // Si no hay un UUID en la URL, generamos uno nuevo
            const generatedUUID = generateUUID();
            document.getElementById('uuid').value = generatedUUID;  // Asignamos el UUID generado al campo oculto
        }
    };
</script>
