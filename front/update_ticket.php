<?php
session_start();
include "../back/conexion.php";

if (!isset($_SESSION['Id'], $_POST['ticket_id'], $_POST['action'])) {
    header("Location: dashboard.php");
    exit;
}

$ticketId = intval($_POST['ticket_id']);
$response = $_POST['response'] ?? '';
$complexity = $_POST['complexity'] ?? null;
$honorIdIt = $_SESSION['HonorId'];
$action = $_POST['action'];
$pictureData = null;

// Procesar imagen IT si existe
if (isset($_FILES['it_picture']) && $_FILES['it_picture']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['it_picture']['tmp_name'];
    $pictureData = file_get_contents($tmpName);
}

if ($action === "update") {
    if ($pictureData) {
        $sql = "UPDATE Tickets SET ResulText = ?, PicturesIt = ?, Complexity = ?, State = 'En proceso' WHERE IdTicket = ? AND HonorIdIt = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssis", $response, $pictureData, $complexity, $ticketId, $honorIdIt);
    } else {
        $sql = "UPDATE Tickets SET ResulText = ?, Complexity = ?, State = 'En proceso' WHERE IdTicket = ? AND HonorIdIt = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssis", $response, $complexity, $ticketId, $honorIdIt);
    }
} elseif ($action === "close") {
    $today = date('Y-m-d');
    if ($pictureData) {
        $sql = "UPDATE Tickets SET ResulText = ?, PicturesIt = ?, Complexity = ?, State = 'Resuelto', FinishDate = ? WHERE IdTicket = ? AND HonorIdIt = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssis", $response, $pictureData, $complexity, $today, $ticketId, $honorIdIt);
    } else {
        $sql = "UPDATE Tickets SET ResulText = ?, Complexity = ?, State = 'Resuelto', FinishDate = ? WHERE IdTicket = ? AND HonorIdIt = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssis", $response, $complexity, $today, $ticketId, $honorIdIt);
    }
} else {
    header("Location: dashboard.php");
    exit;
}

$stmt->execute();
$stmt->close();
header("Location: dashboard.php");
exit;
