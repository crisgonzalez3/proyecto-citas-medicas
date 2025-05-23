<?php
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Calendar</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">

    <style>
        body {
            background: #ebf0f5;
            font-family: 'Calibri', sans-serif;
            margin-top: 100px; 
        }

        #calendar {
            max-width: 900px;
            margin: 40px auto;
        }

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
    <div id="header-container"></div>

    <div class="container mt-6">
    
        <div id="calendar"></div>
    </div>

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
    <div id="footer-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>

    <script>
        $(document).ready(function() {
            function loadAppointments() {
                fetch('src/Dispatcher.php?action=list')
                    .then(response => response.json())  
                    .then(data => {
                        console.log("Datos de las citas:", data);  

                        const events = data.map(appointment => {
                            return {
                                id: appointment.uuid,  
                                title: appointment.patient,  
                                start: `${appointment.date}T${appointment.time}`,  
                                description: appointment.description || "Sin descripción",  
                            };
                        });

                        console.log("Eventos procesados:", events);

                        $('#calendar').fullCalendar('destroy'); 

                        $('#calendar').fullCalendar({
                            header: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'month,agendaWeek,agendaDay'
                            },
                            firstDay: 1, // <-- Añade esta línea para que la semana comience en lunes
                            events: events,
                            eventClick: function(event) {
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

            loadAppointments();  
        });
    </script>
</body>
</html>
