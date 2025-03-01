<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment List</title>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background: linear-gradient(45deg, #4b79a1, #283e51);
            color: white;
            padding: 90px 0;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2.8rem;
            font-weight: 600;
        }

        .container {
            margin-top: 30px;
        }

        table {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
        }

        table th {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 12px;
        }

        table td {
            text-align: center;
            padding: 12px;
            transition: background-color 0.3s ease;
        }

        table tr:hover td {
            background-color: #f5f5f5;
        }

        .btn-primary, .btn-danger, .btn-warning {
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #023e46;
            border: none;
        }

        .btn-primary:hover {
            background-color: #07777e;
            transform: scale(1.05);
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .table-responsive {
            overflow-x: auto;
        }

        /* Modal Styles */
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        
        /* Buttons for New Appointment */
        .new-appointment-btn {
            font-size: 1.2rem;
            padding: 10px 20px;
            margin-top: 15px;
            border-radius: 25px;
        }

    </style>
</head>
<body>
    <!-- Incluir el layout común del header -->
    <div id="header-container"></div>

    <!-- Header Section -->
    <div class="header">
        <h1>List of Medical Appointments</h1>
        <p>Manage your appointments effortlessly with our system</p>
    </div>

    <div class="container">
        <!-- Button to Add New Appointment -->
        <div class="text-center">
            <a href="formulario.php" class="btn btn-primary new-appointment-btn">New Appointment</a>
        </div>

        <!-- Table for Appointment List -->
        <div class="table-responsive mt-4">
            <table id="appointment-table" class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Deletion Confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this appointment?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir el layout común del footer -->
    <div id="footer-container"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script>
        let selectedUUID = null;

        // Función para formatear la hora al formato HH:MM
        function formatTime(time) {
            // Verificar si la hora es válida
            const parts = time.split(':');  // Asumiendo que el formato es HH:MM:SS o HH:MM
            if (parts.length < 2) return time;  // Si no tiene un formato esperado, retornar como está

            // Tomar solo las primeras dos partes (horas y minutos)
            return parts[0] + ':' + parts[1];
        }

        // Load appointments from the API
        async function loadAppointments() {
            try {
                const response = await fetch('http://localhost:8000/Dispatcher.php?action=list');
                const data = await response.json();

                const tableBody = document.querySelector("#appointment-table tbody");
                tableBody.innerHTML = "";

                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="5">No appointments found.</td></tr>`;
                    return;
                }

                data.forEach(appointment => {
                    const tr = document.createElement("tr");

                    // Usar la función formatTime para convertir la hora
                    const formattedTime = formatTime(appointment.time);

                    tr.innerHTML = `
                        <td>${appointment.date}</td>
                        <td>${formattedTime}</td> <!-- Mostrar la hora formateada -->
                        <td>${appointment.patient}</td>
                        <td>${appointment.description || 'No description'}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editAppointment('${appointment.uuid}')">Modify</button>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('${appointment.uuid}')">Delete</button>
                        </td>
                    `;

                    tableBody.appendChild(tr);
                });
            } catch (error) {
                console.error('Error loading appointments:', error);
                alert('Error loading appointments.');
            }
        }

        // Edit appointment - redirects to form
        function editAppointment(uuid) {
            window.location.href = `formulario.php?uuid=${uuid}`;
        }

        // Confirm deletion of appointment
        function confirmDelete(uuid) {
            selectedUUID = uuid;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Delete appointment
        function deleteAppointment() {
            if (!selectedUUID) return;

            fetch(`http://localhost:8000/Dispatcher.php?action=delete&uuid=${selectedUUID}`, {
                method: "GET"
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Appointment deleted successfully.");
                    loadAppointments();
                } else {
                    alert("Error deleting appointment: " + result.message);
                }
            })
            .catch(error => console.error("Error deleting appointment:", error));
        }

        // Associate delete button click
        document.getElementById("confirmDeleteBtn").addEventListener("click", deleteAppointment);

        // Load appointments when the page loads
        window.onload = loadAppointments;

        // Cargar el header y footer usando fetch
        function loadHTML(file, elementId) {
            fetch(file)
                .then(response => response.text())
                .then(data => {
                    document.getElementById(elementId).innerHTML = data;
                })
                .catch(error => console.error('Error loading HTML:', error));
        }

    </script>
</body>
</html>
