<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="IMG/Honor1.webp" type="image/x-icon">
    <link rel="stylesheet" href="css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Ingreso</title>
    <style>
        .error-msg {
            color: red;
            background-color: #ffe5e5;
            padding: 10px;
            margin: 10px auto;
            border-radius: 5px;
            width: fit-content;
            font-family: 'Open Sans', sans-serif;
        }
    </style>
</head>
<body>
    <div id="centrar-honor">
        <div id="honor"><h1>Honor</h1></div>
    </div>

    <div id="informacion">
        <div id="text-informacion">
            <div id="text">
                <h2>Welcome to HelpDesk from Honor Technologies Latam.<br> 
                    If you have any question about it software you can comunicate with W0136117 for Honor E link
                </h2>
            </div>    
        </div>

        <!-- ✅ CORREGIDO: formulario abierto correctamente -->
        <form action="back/ingreso.php" method="POST">
            <div id="ingreso">
                <input type="text" name="User" placeholder="User" id="User" required>
                <input type="password" name="Psw" placeholder="Password" id="Psw" required>
                <input type="submit" value="Login" id="login">
            </div>
        </form>

        <!-- ✅ Notificación de error si existe -->
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="error-msg"><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
        <?php endif; ?>
    </div>

    <footer id="marca_de_propiedad">
        <p>Designed by Juan Martin (Field Master) @Colombia</p>
    </footer>
</body>
</html>
