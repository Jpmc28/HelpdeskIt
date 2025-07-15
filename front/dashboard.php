<?php
session_start();
include "../back/conexion.php";

// Verificar sesión activa
#$honorIdIt = $_SESSION['Id'] ?? null;
if (!$_SESSION['Id']) {
    header("Location: ../index.php");
    exit;
}

$honorIdIt = $_SESSION['HonorId'] ?? null;

// Tickets asignados al usuario IT
$sqlAssigned = "SELECT * FROM Tickets WHERE HonorIdIt = ?";
$stmtAssigned = $conexion->prepare($sqlAssigned);
$stmtAssigned->bind_param("s", $honorIdIt);
$stmtAssigned->execute();
$resultAssigned = $stmtAssigned->get_result();

// Tickets disponibles
$sqlAvailable = "SELECT * FROM Tickets WHERE State = 'Ingresado'";
$resultAvailable = $conexion->query($sqlAvailable);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>IT Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }
        .modal-content { background: #f4f4f4; margin: 10% auto; padding: 20px; border-radius: 10px; width: 500px; position: relative; }
        .modal-header, .blue-box, .gray-box { border-radius: 10px; padding: 10px; margin: 10px 0; }
        .modal-header { background: #1e3a8a; color: white; display: flex; justify-content: space-between; }
        .blue-box { background: #1e40af; color: white; }
        .gray-box { background: #737373; color: white; }
        .modal-btn { padding: 8px 12px; border: none; border-radius: 6px; margin: 5px; }
        .modal-btn.update { background: #6b7280; color: white; }
        .modal-btn.upload { background: #4b5563; color: white; }
        .modal-btn.download { background: #1e3a8a; color: white; }
        .modal-btn.close { position: absolute; top: 10px; right: 15px; cursor: pointer; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="tickets-container">
        <div class="column">
            <h2>Your Tickets</h2>
            <table>
                <thead><tr><th>T.N°</th><th>L.U.D</th><th>R.U</th><th>State</th><th>Atender</th></tr></thead>
                <tbody>
                <?php while ($ticket = $resultAssigned->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($ticket['IdTicket']) ?></td>
                        <td><?= htmlspecialchars($ticket['StartDate']) ?></td>
                        <td><?= htmlspecialchars($ticket['HonorId']) ?></td>
                        <td class="<?= strtolower(str_replace(' ', '', $ticket['State'])) ?>">
                            <?= htmlspecialchars($ticket['State'] ?? 'Pending') ?>
                        </td>
                        <td>
                            <?php if ($ticket['State'] !== 'Resuelto'): ?>
                                <button 
                                    class="attend-btn"
                                    data-ticketid="<?= htmlspecialchars($ticket['IdTicket']) ?>"
                                    data-user="<?= htmlspecialchars($ticket['FullName']) ?>"
                                    data-honorid="<?= htmlspecialchars($ticket['HonorId']) ?>"
                                    data-problem="<?= htmlspecialchars($ticket['ProblemText']) ?>"
                                    data-hasimage="<?= !empty($ticket['Pictures']) ? '1' : '0' ?>"
                                    data-response="<?= htmlspecialchars($ticket['ResulText']) ?>"
                                    data-complexity="<?= htmlspecialchars($ticket['Complexity']) ?>"
                                >Atender</button>
                            <?php else: ?>
                                <button class="attender-btn" disabled style="background-color: gray;">Atender</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="column">
            <h2>Available Tickets</h2>
            <table>
                <thead><tr><th>T.N°</th><th>A.V</th><th>R.U</th><th>Select</th></tr></thead>
                <tbody>
                <?php while ($ticket = $resultAvailable->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($ticket['IdTicket']) ?></td>
                        <td><?= htmlspecialchars($ticket['StartDate']) ?></td>
                        <td><?= htmlspecialchars($ticket['HonorId']) ?></td>
                        <td>
                            <form action="accept_ticket.php" method="POST">
                                <input type="hidden" name="ticket_id" value="<?= $ticket['IdTicket'] ?>">
                                <button type="submit">Accept</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para atender ticket -->
<div id="ticketModal" class="modal">
    <div class="modal-content">
        <span class="modal-btn close" onclick="modal.style.display='none'">&times;</span>
        <div class="modal-header">
            <strong>Solicitante: <span id="modalFullName"></span></strong>
            <strong>Honor Id: <span id="modalHonorId"></span></strong>
        </div>
        <div class="blue-box" id="modalProblem"></div>
        <!--
        <div style="text-align:center;margin:10px 0;">
            <img id="modalImage" src="" alt="Adjunto" style="max-height:200px;display:none;border-radius:8px;">
        </div> -->
        <form method="POST" action="update_ticket.php" enctype="multipart/form-data">
            <input type="hidden" name="ticket_id" id="modalTicketId">
            <textarea name="response" id="responseText" rows="5" placeholder="Escribe tu respuesta aquí..." maxlength="700"></textarea>
            <select name="complexity" required>
                <option value="">-- Dificultad --</option>
                <option value="Baja">Baja</option>
                <option value="Media">Media</option>
                <option value="Alta">Alta</option>
            </select>
            <br>
            <input type="file" name="it_picture" accept="image/*">
            <br>
            <button type="submit" name="action" value="update" class="modal-btn update">Update Ticket</button>
            <button type="submit" name="action" value="close" class="modal-btn upload">Cerrar Ticket</button>
            <a id="downloadLink" class="modal-btn download" href="#" target="_blank" disabled>Download Pictures</a>
        </form>
    </div>
</div>
<footer id="marca_de_propiedad">
    <p>Designed by Juan Martin (Field Master) @Colombia</p>
</footer>
<script>

document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById("ticketModal");
  const modalFullName = document.getElementById("modalFullName");
  const modalHonorId = document.getElementById("modalHonorId");
  const modalProblem = document.getElementById("modalProblem");
  const modalTicketId = document.getElementById("modalTicketId");
  const downloadLink = document.getElementById("downloadLink");

  document.querySelectorAll(".attend-btn").forEach(button => {
    button.addEventListener("click", () => {
      const ticketId = button.dataset.ticketid;
      const user = button.dataset.user;
      const honorId = button.dataset.honorid;
      const problem = button.dataset.problem;
      const hasImage = button.dataset.hasimage === "1";

      modalFullName.textContent = user;
      modalHonorId.textContent = honorId;
      modalProblem.textContent = problem;
      modalTicketId.value = ticketId;

      // Solo descarga si hay imagen
      if (hasImage) {
        downloadLink.href = `download_picture.php?ticket_id=${ticketId}`;
        downloadLink.removeAttribute("disabled");
        downloadLink.classList.remove("disabled-btn");
      } else {
        downloadLink.href = "#";
        downloadLink.setAttribute("disabled", "true");
        downloadLink.classList.add("disabled-btn");
      }

      modal.style.display = "flex";
    });
  });

  document.querySelector(".close").onclick = () => modal.style.display = "none";
  window.onclick = e => { if (e.target === modal) modal.style.display = "none"; };
});

    //guarda informacion de la dificutad del ticket

    //guarda informacion del texto escrito
const responseText = document.getElementById("responseText");

document.querySelectorAll(".attend-btn").forEach(button => {
  button.addEventListener("click", () => {
    // ... otras variables
    const response = button.dataset.response || "";

    responseText.value = response; // <<< aquí se carga lo último escrito
  });
});
</script>
</body>
</html>