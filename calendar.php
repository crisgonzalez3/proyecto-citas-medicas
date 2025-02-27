<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Calendar</title>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Moment.js (necesario para FullCalendar 3.x) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">

    <style>
        body {
            background: #ebf0f5;
            font-family: 'Calibri', sans-serif;
        }
        /* FullCalendar container */
        #calendar {
            max-width: 900px;
            margin: 40px auto;
        }
        /* Customize event popup */
        .fc-event {
            cursor: pointer;
            border: 1px solid #074665;
            border-radius: 5px;
        }
        .fc-event:hover {
            background-color: #30c6f4;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Incluir el header dinámicamente -->
    <div id="header-container"></div>

    <!-- Main Content -->
    <div class="container mt-6">
        <h1 class="mb-4 text-center text-primary">Appointments Calendar</h1>
        <div id="calendar"></div>
    </div>

    <!-- Modal for Event Details -->
    <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalDescription"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir el footer dinámicamente -->
    <div id="footer-container"></div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" defer></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>

    <script>
        $(document).ready(function() {
            // Cambios en la función loadAppointments
            function loadAppointments() {
                fetch('Dispatcher.php?action=list')  // Llamamos a la acción 'list' de la API
                    .then(response => response.json())  // Parseamos la respuesta JSON
                    .then(data => {
                        console.log("Datos de las citas:", data);  // Ver los datos recibidos

                        // Convertimos los datos de citas en el formato que FullCalendar necesita
                        const events = data.map(appointment => {
                            return {
                                id: appointment.uuid,  // Usamos uuid como ID del evento
                                title: appointment.patient,  // El título es el nombre del paciente
                                start: `${appointment.date}T${appointment.time}`,  // Combinamos la fecha y hora
                                description: appointment.description || "Sin descripción",  // Descripción del evento
                            };
                        });

                        console.log("Eventos procesados:", events);  // Ver los eventos formateados

                        // Destruir el calendario previo para evitar superposiciones
                        $('#calendar').fullCalendar('destroy'); 

                        // Inicializar el calendario con los eventos cargados
                        $('#calendar').fullCalendar({
                            header: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'month,agendaWeek,agendaDay'
                            },
                            events: events,  // Pasamos los eventos procesados
                            eventClick: function(event) {
                                // Mostrar el modal con los detalles del evento
                                const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
                                document.getElementById('modalTitle').innerText = 'Cita: ' + event.title;
                                document.getElementById('modalDescription').innerText = 'Descripción: ' + event.description;
                                modal.show();
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar las citas:', error);
                    });
            }

            loadAppointments();  // Cargar las citas cuando se cargue la página
        });
    </script>
</body>
</html>
