<?php
include "../back/conexion.php";

if (!isset($_GET['ticket_id'])) {
    die("Ticket ID not provided");
}

$ticketId = intval($_GET['ticket_id']);

$sql = "SELECT Pictures FROM Tickets WHERE IdTicket = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $ticketId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['Pictures']) {
    header("Content-type: image/jpeg");
    header('Content-Disposition: attachment; filename="imagen_usuario_' . $ticketId . '.jpg"');
    echo $row['Pictures'];
} else {
    echo "No picture found.";
}
?>
