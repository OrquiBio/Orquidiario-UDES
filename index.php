<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Sensores</title>
    <!-- Incluir Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- Navbar (Header) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">OrquiBio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="test.php">Test</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <h1 class="my-4">Informe de Temperatura y Humedad</h1>

        <!-- Formulario para Filtrar por Fecha -->
        <form action="index.php" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Fecha de Inicio</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Fecha Final</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>

        <!-- Tabla para mostrar los datos -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Humedad (%)</th>
                    <th>Temperatura (°C)</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Incluir el archivo de conexión a la base de datos
                include 'db_connect.php';

                // Inicializar arrays para la gráfica
                $fechas = [];
                $humedades = [];
                $temperaturas = [];

                // Verificar si se ha enviado el formulario de filtrado
                if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                    $start_date = $_GET['start_date'];
                    $end_date = $_GET['end_date'];

                    // Consulta para obtener los datos entre las fechas seleccionadas
                    $sql = "SELECT * FROM tabla_sensor WHERE fecha_hora BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' ORDER BY fecha_hora DESC";
                    $result = $conn->query($sql);

                    // Comprobar si hay resultados
                    if ($result->num_rows > 0) {
                        // Mostrar los datos en la tabla
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row['id'] . "</td>
                                    <td>" . $row['humedad'] . "</td>
                                    <td>" . $row['temperatura'] . "</td>
                                    <td>" . $row['fecha_hora'] . "</td>
                                  </tr>";

                            // Añadir los datos a los arrays para la gráfica
                            $fechas[] = $row['fecha_hora'];
                            $humedades[] = $row['humedad'];
                            $temperaturas[] = $row['temperatura'];
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No se encontraron datos para el rango seleccionado</td></tr>";
                    }
                } else {
                    // Si no se ha enviado el formulario de filtrado, mostrar un mensaje en la tabla
                    echo "<tr><td colspan='4' class='text-center'>Por favor, selecciona un rango de fechas para mostrar los datos</td></tr>";
                }

                // Cerrar la conexión
                $conn->close();
                ?>
            </tbody>
        </table>

        <!-- Solo mostrar la gráfica si hay datos -->
        <?php if (count($fechas) > 0): ?>
        <canvas id="sensorChart" width="400" height="200"></canvas>

        <script>
            // Datos recogidos de PHP
            var fechas = <?php echo json_encode($fechas); ?>;
            var humedades = <?php echo json_encode($humedades); ?>;
            var temperaturas = <?php echo json_encode($temperaturas); ?>;

            var ctx = document.getElementById('sensorChart').getContext('2d');
            var sensorChart = new Chart(ctx, {
                type: 'line', // Tipo de gráfica (puedes usar 'bar' si prefieres)
                data: {
                    labels: fechas, // Fechas como etiquetas
                    datasets: [
                        {
                            label: 'Humedad (%)',
                            data: humedades,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            fill: true,
                        },
                        {
                            label: 'Temperatura (°C)',
                            data: temperaturas,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha y Hora'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Valores'
                            }
                        }
                    }
                }
            });
        </script>
        <?php endif; ?>
    </div>

    <!-- Incluir JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>