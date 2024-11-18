<?php
// Incluir el archivo de conexión a la base de datos
include 'db_connect.php';

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el cuerpo de la solicitud y decodificar el JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Verificar que el JSON sea válido y que se han recibido los datos correctamente
    if (json_last_error() === JSON_ERROR_NONE && isset($data['humedad']) && isset($data['temperatura'])) {
        $humedad = $data['humedad'];
        $temperatura = $data['temperatura'];

        // Preparar la consulta SQL para insertar los datos
        $sql = "INSERT INTO tabla_sensor (humedad, temperatura, fecha_hora) VALUES ('$humedad', '$temperatura', NOW())";

        // Ejecutar la consulta e insertar los datos
        if ($conn->query($sql) === TRUE) {
            // Devolver respuesta exitosa
            echo json_encode(["status" => "success", "message" => "Datos almacenados correctamente."]);
        } else {
            // Devolver mensaje de error en la base de datos
            echo json_encode(["status" => "error", "message" => "Error al almacenar los datos: " . $conn->error]);
        }
    } else {
        // Si el JSON está mal formado o los datos son incompletos
        echo json_encode(["status" => "error", "message" => "Datos incompletos o formato JSON incorrecto"]);
    }
} else {
    // Si la solicitud no es POST
    echo json_encode(["status" => "error", "message" => "Método no permitido. Utiliza POST para enviar los datos."]);
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
