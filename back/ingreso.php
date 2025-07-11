<?php
session_start();
include "conexion.php"; // ajusta el path si es necesario

$User = $_POST['User'];
$Psw = $_POST['Psw'];

// Obtener al usuario sin validar contraseña todavía
$sql = "SELECT * FROM User WHERE HonorId = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $User);

if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Validar la contraseña usando password_verify
        if (password_verify($Psw, $user['PasswordUser'])) {
            // Login correcto
            $_SESSION['username'] = $user['NameUser'];
            $_SESSION['Id'] = $user['Id'];
            header("Location: ../front/dashboard.php");
            exit;
            
        } else {
            $_SESSION['login_error'] = "❌ Contraseña incorrecta.";
            header("Location: ../index.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = "❌ Usuario no encontrado.";
        header("Location: ../index.php");
        exit;
    }
} else {
    $_SESSION['login_error'] = "❌ Error en la base de datos.";
    header("Location: ../index.php");
    exit;
}

$stmt->close();
$conexion->close();
?>