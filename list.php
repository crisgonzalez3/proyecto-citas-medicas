<body>
    <!-- list.php -->
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
            <?php if (!empty($appointments)): ?>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <!-- Mostrar la fecha -->
                        <td><?= htmlspecialchars($appointment['date']); ?></td>
                        <!-- Mostrar la hora en formato hh:mm -->
                        <td><?= htmlspecialchars((new DateTime($appointment['time']))->format('H:i')); ?></td>
                        <!-- Mostrar el paciente -->
                        <td><?= htmlspecialchars($appointment['patient']); ?></td>
                        <!-- Mostrar la descripción (si no está vacía) -->
                        <td><?= htmlspecialchars($appointment['description'] ?? 'No description'); ?></td>
                        <td>
                            <!-- Botón para modificar -->
                            <form action="Dispatcher.php" method="get" style="display: inline;">
                                <input type="hidden" name="uuid" value="<?= htmlspecialchars($appointment['uuid']); ?>">
                                <input type="hidden" name="action" value="formulario"> <!-- Especificar la acción como 'save' -->
                                <button type="submit" class="btn btn-warning btn-sm">Modify</button>
                            </form>
                            <!-- Botón para eliminar -->
                            <form action="Dispatcher.php" method="get" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                <input type="hidden" name="uuid" value="<?= htmlspecialchars($appointment['uuid']); ?>">
                                <input type="hidden" name="action" value="delete"> <!-- Especificar la acción como 'delete' -->
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No appointments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    <!-- Incluir el layout común del footer -->
    <div id="footer-container"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
