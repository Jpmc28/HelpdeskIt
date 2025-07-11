<?php
session_start();
include "../back/conexion.php";

// Verificar si el usuario de IT está autenticado
$honorIdIt = $_SESSION['Id'] ?? null;

if (!$honorIdIt) {
    header("Location: ../index.php");
    exit;
}

// Tickets asignados al usuario IT
$sqlAssigned = "SELECT * FROM Tickets WHERE HonorIdIt = ?";
$stmtAssigned = $conexion->prepare($sqlAssigned);
$stmtAssigned->bind_param("s", $honorIdIt);
$stmtAssigned->execute();
$resultAssigned = $stmtAssigned->get_result();

// Tickets disponibles con estado 'Ingresado'
$sqlAvailable = "SELECT * FROM Tickets WHERE State = 'Ingresado'";
$resultAvailable = $conexion->query($sqlAvailable);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <div class="tickets-container">
            <!-- Your Tickets -->
            <div class="column">
                <h2>Your Tickets</h2>
                <table>
                    <thead>
                        <tr><th>T.N°</th><th>L.U.D</th><th>R.U</th><th>State</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($ticket = $resultAssigned->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($ticket['IdTicket']) ?></td>
                                <td><?= htmlspecialchars($ticket['StartDate']) ?></td>
                                <td><?= htmlspecialchars($ticket['HonorId']) ?></td>
                                <td class="<?= strtolower(str_replace(' ', '', $ticket['State'])) ?>">
                                    <?= htmlspecialchars($ticket['State'] ?? 'Pending') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Available Tickets -->
            <div class="column">
                <h2>Available Tickets</h2>
                <table>
                    <thead>
                        <tr><th>T.N°</th><th>A.V</th><th>R.U</th><th>Select</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($ticket = $resultAvailable->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($ticket['IdTicket']) ?></td>
                                <td><?= htmlspecialchars($ticket['StartDate']) ?></td>
                                <td><?= htmlspecialchars($ticket['HonorId']) ?></td>
                                <td>
                                    <form action="accept_ticket.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['IdTicket']) ?>">
                                        <button type="submit" class="accept-btn">Accept</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer id="marca_de_propiedad">
        <p>Designed by Juan Martin (Field Master) @Colombia</p>
    </footer>
</body>
</html>
