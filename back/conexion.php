<?php

#conexion a la bd

$host = "localhost";
$user = "root";
$password = "S0p0rt3!";
$database = "helpdesk";

$conexion = new mysqli($host, $user, $password, $database);

#verificacion de conexion a la bd
/*
if ($conexion->connect_error) {
    die("Conexion fallida: " . $conexion->connect_error);
} else {
    echo "Conexion exitosa a la base de datos";
}*/
?>