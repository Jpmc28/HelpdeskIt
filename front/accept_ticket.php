<?php
session_start();
include "../back/conexion.php";

// Validación: asegurar sesión y datos POST
if (!isset($_SESSION['Id'], $_POST['ticket_id'])) {
    header("Location: ../index.php");
    exit;
}

$ticketId = intval($_POST['ticket_id']); // Asegura que sea numérico
$honorIdIt = $_SESSION['Id'];

// Verificar si el ticket aún está disponible (opcional pero recomendable)
$sqlCheck = "SELECT State FROM Tickets WHERE IdTicket = ?";
$stmtCheck = $conexion->prepare($sqlCheck);
$stmtCheck->bind_param("i", $ticketId);
$stmtCheck->execute();
$result = $stmtCheck->get_result();
$ticket = $result->fetch_assoc();

if (!$ticket || $ticket['State'] !== 'Ingresado') {
    // Redirigir si ya fue tomado o no existe
    header("Location: dashboard.php?error=ticket_unavailable");
    exit;
}

// Actualizar ticket con el usuario IT y cambiar estado
$sql = "UPDATE Tickets SET HonorIdIt = ?, State = 'En proceso' WHERE IdTicket = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $honorIdIt, $ticketId);
$stmt->execute();

// Redirigir de nuevo al dashboard
header("Location: dashboard.php");
exit;
/*para insercion*/
?>
