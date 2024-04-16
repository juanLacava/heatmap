<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root"; // Usuario de la base de datos
$password = ""; // Contraseña de la base de datos
$dbname = "caesis_bddcaeg3"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener la fecha y hora de inicio seleccionadas
$fechaHoraInicio = $conn->real_escape_string($_GET['fechaHoraInicio']);

// Obtener la fecha y hora de finalización seleccionadas
$fechaHoraFin = $conn->real_escape_string($_GET['fechaHoraFin']);

// Obtener el número del equipo proporcionado en la solicitud GET
$equipoId = intval($_GET['equipoId']);

// Consultar las ubicaciones del equipo seleccionado para el rango de fecha y hora seleccionadas
$sql = "SELECT latitud, longitud FROM ntr_hist WHERE equipo_id = $equipoId AND fecha BETWEEN '$fechaHoraInicio' AND '$fechaHoraFin'";

$result = $conn->query($sql);

// Crear un array para almacenar las características (features) GeoJSON
$features = array();

if ($result->num_rows > 0) {
    // Iterar sobre los resultados y crear características GeoJSON para cada ubicación
    while ($row = $result->fetch_assoc()) {
        $feature = array(
            'type' => 'Feature',
            'geometry' => array(
                'type' => 'Point',
                'coordinates' => array(floatval($row["longitud"]), floatval($row["latitud"]))
            )
        );
        // Agregar la característica al array de características
        array_push($features, $feature);
    }
}

// Crear un objeto GeoJSON con todas las características
$geojson = array(
    'type' => 'FeatureCollection',
    'features' => $features
);

// Establecer el tipo de contenido como JSON
header('Content-type: application/json');

// Devolver el objeto GeoJSON como respuesta
echo json_encode($geojson);

// Cerrar la conexión a la base de datos
$conn->close();
?>
