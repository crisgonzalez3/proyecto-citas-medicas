<?php
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, "http://localhost/proyecto-citas-medicas/src/Dispatcher.php?action=list"); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch); 
curl_close($ch); 

$appointments = json_decode($response, true); 
if ($appointments === null) {
    $appointments = [];
}

?>
<body>
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
                        <td><?= htmlspecialchars($appointment['date']); ?></td>
                        <td><?= htmlspecialchars((new DateTime($appointment['time']))->format('H:i')); ?></td>
                        <td><?= htmlspecialchars($appointment['patient']); ?></td>
                        <td><?= htmlspecialchars($appointment['description'] ?? 'No description'); ?></td>
                        <td>
                          
                            <form action="index.php" method="GET" style="display: inline;">
                                <input type="hidden" name="uuid" value="<?= htmlspecialchars($appointment['uuid']); ?>">
                                <input type="hidden" name="action" value="formulario"> 
                                <button type="submit" class="btn btn-warning btn-sm">Modify</button>
                            </form>
                        
                            <form action="src/Dispatcher.php" method="get" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                <input type="hidden" name="uuid" value="<?= htmlspecialchars($appointment['uuid']); ?>">
                                <input type="hidden" name="action" value="delete"> 
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

    <div id="footer-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
