<?php
$servername = "127.0.0.1:3306";  // Servidor MySQL
$username = "u323617474_orquibio2";  // Usuario MySQL
$password = 'T&m8|$YH~Q1';  // Contraseña MySQL
$dbname = "u323617474_orquibio2";  // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
//echo "Conexión exitosa a la base de datos.";
?>
